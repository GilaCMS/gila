<?php

$data=array();

class pnk extends controller
{
	function __construct ()
	{
		global $table;
		define('UPLOAD_FOLDER',"/../../uploads/");
		//define('TABLE_FOLDER','/../tables/');
		define('PNK_URL','pnk/');
		define('PDF_FOLDER',"/");


		if(isset($_GET['t'])) {
			$tfile = $_GET['t'].".php";
			if (file_exists($tfile)) {
				include $tfile;
			}
			else echo "File $tfile could not be found";

		}
		else echo "Table not specified";

		foreach($table['fields'] as $key=>$field) {
			if(check_v($field['type'],'joins')) {
				$jt = $field['jt']; $ot = $field['ot']; $this_id = $table['name'].".".$table['id'];
				$ql = "(SELECT GROUP_CONCAT({$jt[2]}) FROM {$jt[0]},{$ot[0]} WHERE {$jt[1]}=$this_id AND {$ot[0]}.{$ot[1]}={$jt[2]})";
				$table['fields'][$key]['qcolumn'] = $ql;
			}
			if(check_v($field['type'],'meta')) {
				$mt = $field['mt']; $vt= $field['metatype']; $this_id = $table['name'].".".$table['id'];
				$ql = "(SELECT GROUP_CONCAT({$mt[2]}) FROM {$mt[0]} WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$vt[0]}='{$vt[1]}')";
				$table['fields'][$key]['qcolumn'] = $ql;
			}

		}


	}

	function indexAction ()
	{
		$this->fieldsAction();
	}

	function fieldsAction ()
	{
		global $table;
		fill_field_options();
		fill_field_datalist();
		echo json_encode($table);
	}

	function deleteAction ()
	{
		global $db,$table;
		echo $_POST['id'];
		$db->query("DELETE FROM ".$table['name']." WHERE ".$table['id']."=".$_POST['id'].";");
	}

	function jsonAction ()
	{
		global $table;
		echo json_encode($table);
	}

	function pnkfileAction ()
	{
		global $table, $data;
		$table["pagination"]=0;
		$pnkfile = $_GET["file"];
		fill_field_options();
		read_data_rows();
		include __DIR__."/pnk-$pnkfile.php";
	}

	function updateAction () {
		global $data, $table, $db;

		$erows = json_decode($_POST["erows"],true);

		foreach ($erows as $erow_id=>$erow) {
			$q="";$c="";$v="";

			if($erow_id!=""){
				if(isset($table['onchange'])) $table['onchange']($erow);
			}else{
				if(isset($table['oncreate'])) $table['oncreate']($erow);
			}

			foreach($erow as $col=>$value){

				if(check_v($table['fields'][$col]['type'],'joins')) continue;
				if(check_v($table['fields'][$col]['type'],'meta')) continue;

				if(editable($col)){
					if($q!="") $q.=",";
					$q.="`$col`='$value'";
				}
					if($c!="") $c.=",";
					$c.="`$col`";
					if($v!="") $v.=",";
					$v.="'$value'";
			}

			if($erow_id!=""){
				$query="UPDATE `".$table["name"]."` SET $q WHERE `".$table["id"]."`='$erow_id';";
				$db->query($query);
				$result="";
			}else{
				$query="INSERT INTO ".$table["name"]."($c) VALUES($v);";
				$db->query($query);
				$erow_id=$db->insert_id;
			}

			foreach($erow as $col=>$value) {
				if(check_v($table['fields'][$col]['type'],'joins')) {
					$jt = $table['fields'][$col]["jt"];
					$arrv = explode(",",$value);
					$db->query("DELETE FROM {$jt[0]} WHERE `{$jt[1]}`='$erow_id' AND `{$jt[2]}` NOT IN($value);");
					foreach($arrv as $arrv_k=>$arrv_v){
						$db->query("INSERT INTO {$jt[0]}(`{$jt[1]}`,`{$jt[2]}`) VALUES('$erow_id','$arrv_v');");
					}
					continue;
				}
				if(check_v($table['fields'][$col]['type'],'meta')) {
					$mt = $table['fields'][$col]["mt"];
					$vt = $table['fields'][$col]["metatype"];
					$arrv = explode(",",$value);
					$db->query("DELETE FROM {$mt[0]} WHERE `{$mt[1]}`='$erow_id' AND `{$vt[0]}`='{$vt[1]}' AND `{$mt[2]}` NOT IN($value);");
					foreach($arrv as $arrv_k=>$arrv_v){
						$db->query("INSERT INTO {$mt[0]}(`{$mt[1]}`,`{$mt[2]}`,`{$vt[0]}`) VALUES('$erow_id','$arrv_v','{$vt[1]});");
					}
					continue;
				}
			}

		}
		$qs=get_fields_query();
		$result=$db->query("SELECT $qs FROM ".$table['name']." WHERE ".$table['id']."=$erow_id;");

		fill_data_rows($result);
		if(isset($table['onupdate'])){
			$result2=$db->query("SELECT $qs FROM ".$table['name']." WHERE ".$table['id']."=$erow_id;");
			while($row=mysqli_fetch_array($result2)) $table['onupdate']($row);
		}
		echo json_encode($data);
	}

	function cloneAction ()
	{
		global $data, $table;

		$cols = "";
		foreach ($table["fields"] as $field_id=>$field) {
			if (isset($field['type'])) if($field['type']=='gallery') break;
			// if (check_v($field['edit'],true) or ( (array_key_exists("edit",$table['commands']) or (in_array("edit",$table['commands']))) and (!check_v($field['edit'],false)) ) ) {
			if (editable($field_id)) {
				$data["fields"][]=$field_id;
				if($cols!="") $cols=$cols.",";
				if(isset($field['qcolumn'])) $cols=$cols.$field['qcolumn']." AS $field_id"; else $cols=$cols."`".$field_id."`";
			}
		}
		if($_POST['id']!=""){
			// Edit of registry, return current
			$result=$db->query("SELECT $cols FROM ".$table['name']." WHERE `".$table['id']."`='".$_POST['id']."';");
			fill_data_rows($result);
		}
		echo json_encode($data);
	}

	function addAction ()
	{
		global $data, $table;

		$fields="";$values="";
		$last_id = $db->insert("INSERT INTO ".$table['name']." VALUES();");
		$qs=get_fields_query();

		$result=$db->query("SELECT $qs FROM ".$table['name']." WHERE ".$table['id']."=$last_id;");

		fill_data_rows($result);
		echo json_encode($data);
	}

	function editAction ()
	{
		global $data, $table,$db;

		$cols="";
		foreach($table["fields"] as $field_id=>$field){
			if(isset($field['type'])) if($field['type']=='gallery') break;
			if($_POST['id']!=""){
			//	echo "id:".$_POST['id'];
				//if( check_v($field['edit'],true) or ( (array_key_exists("edit",$table['commands']) or (in_array("edit",$table['commands']))) and (!check_v($field['edit'],false)) ) ) {
				if (editable($field_id)) {
					$data["fields"][]=$field_id;
					if($cols!="") $cols=$cols.",";
					if(isset($field['qcolumn'])) $cols=$cols.$field['qcolumn']." AS $field_id"; else $cols=$cols."`".$field_id."`";
				}
			}
			else {
				// New registry, return default values
				if( (check_v($field['create'],true)) or ( (in_array("add",$table['tools']))and(!check_v($field['create'],false)) ) ) {
					$data["fields"][]=$field_id;
					$fv="";
					if(isset($field['type'])){
						if(isset($field['options'])) $fv="0";
						if($field['type']=="date") $fv=date("Y-m-d");
						if($field['type']=="number") $fv="0";
					}
					if(isset($field['default'])) $fv=$field['default'];
					//if(isset($field))
					$data['rows'][0][]=$fv;
				}
			}
		}
		if($_POST['id']!=""){
			// Edit of registry, return current
			$result=$db->query("SELECT $cols FROM ".$table['name']." WHERE `".$table['id']."`='".$_POST['id']."';");
			fill_data_rows($result);
		}
		echo json_encode($data);
	}

	function listAction ()
	{
		global $data;
		read_data_rows();
		if(isset($_GET['stats'])) get_stats();
		echo json_encode($data);
	}

	function ucAction ()
	{
		global $data, $db, $table;

		$c=$_POST["col"];
		$v=$_POST["v"];

		if(!editable($c)) {
			echo "cannot edit";
			return;
		}

		$row_id=$_POST["rid"];
		$query="UPDATE ".$table["name"]." SET `$c`='$v' WHERE `".$table["id"]."`='$row_id';";
		$db->query($query);

		$qs = get_fields_query();
		$result = $db->query("SELECT $qs FROM ".$table['name']." WHERE ".$table['id']."=$row_id;");

		fill_data_rows($result);
		echo json_encode($data);
	}

	function switchAction ()
	{
		global $data;
		global $table;

		$col=$_POST["col"];
		$val=$_POST["val"];
		$id=$_POST["id"];

		$db->query("UPDATE {$table["name"]} SET `$col`=$val WHERE `".$table["id"]."`=$id;");

		echo json_encode($val);
	}

	function keysAction ()
	{
		global $data, $table;

		$kt=$_POST["kt"];
		$f=$_POST["f"];
		$v=$_POST["fv"];
		$k=$_POST["k"];

		if(!isset($_GET["update"])){
			$result = $db->query("SELECT `$k` FROM `$kt` WHERE `$f`='$v'");
			$i=0;
			while($row=mysqli_fetch_array($result)) $data[]=$row[$k];
			echo json_encode($data);
		}else{
			$keys = json_decode($_POST["keys"],true);
			$nk = sizeof($keys);
			$db->query("DELETE FROM `$kt` WHERE `$f`='$v'");
			foreach($keys as $kv) $db->query("INSERT INTO `$kt`(`$k`,`$f`) VALUES('$kv','$v')");
			$db->query("UPDATE ".$table["name"]." SET `$kt`=$nk");
			echo json_encode($nk);
		}
	}

}

function get_stats(){
	global $table,$data,$db;
	$stats_array = explode(",", $_GET["stats"]);
	if(sizeof($stats_array)==1) {
		$col = $stats_array[0];
		$res = $db->query("SELECT $col,COUNT(*) AS t FROM ".$table["name"]." GROUP BY $col");
		while($row=mysqli_fetch_array($res))	$data["stats"][$row[$col]] = $row["t"];
	}
	if(sizeof($stats_array)==2) {
		$col1 = $stats_array[0];
		$col2 = $stats_array[1];
		$res = $db->query("SELECT $col1,$col2,COUNT(*) AS t FROM ".$table["name"]." GROUP BY $col1,$col2");
		while($row=mysqli_fetch_array($res)) {
			$data["stats"][$row[$col1]][$row[$col2]] = $row["t"];
		}
	}
}

function read_data_rows(){
	global $table, $data, $link, $db;
	$filters="1";

	if(isset($table['filters'])) $filters=$table['filters'];

	foreach($table["fields"] as $field_id=>$tvalue) if(!isset($tvalue['value'])){
		if(!isset($tvalue["qcolumn"])) $qc="`".$field_id."`"; else $qc=$tvalue["qcolumn"];
			if( isset($_GET[$field_id]) ){
				$jsonget = explode(",", $_GET[$field_id]);
				$value=$_GET[$field_id];
				if(sizeof($jsonget)>1) {
					$filters.=" AND $qc IN ('".$jsonget[0];
					for($i=1; $i<sizeof($jsonget); $i++) $filters.="','".$jsonget[$i];
					$filters.="')";
					$data["filtered"][$field_id]=$jsonget;
				}else {
					if( (check_v($tvalue['type'],"number"))or(isset($tvalue['options']))or(isset($tvalue['qoptions'])) )
						$filters.=" AND $qc='$value'"; else $filters.=" AND $qc LIKE '%$value%'";
					$data["filtered"][$field_id]=$value;
				}
		}
		if( isset($_GET[$field_id."_from"]) ){
			$value=$_GET[$field_id."_from"];
			$filters.=" AND $qc>='$value'";
			$data["filtered"][$field_id][0]=$value;
		}
		if( isset($_GET[$field_id."_to"]) ){
			$value=$_GET[$field_id."_to"];
			$filters.=" AND $qc<='$value'";
			$data["filtered"][$field_id][1]=$value;
		}
	}
	if(isset($_GET["search"])) {
		$value=$_GET["search"];
		$xfilter="";
		foreach($table["fields"] as $field_id=>$tvalue) if(!isset($tvalue["qcolumn"])) {
			if($xfilter=="") $xfilter.=" AND (`"; else $xfilter.=" OR `";
			$xfilter.=$field_id."` LIKE '%$value%'";
		}
		$xfilter.=")";
		$filters.=$xfilter;
	}
	if(isset($table['order-by'])) $orderby="ORDER BY ".$table['order-by']; else $orderby="";
	if(isset($_GET['orderby'])){
		foreach($table["fields"] as $field_id=>$field){
			if($_GET['orderby']==$field_id."_d") $orderby="ORDER BY `$field_id` DESC";
			if($_GET['orderby']==$field_id."_a") $orderby="ORDER BY `$field_id` ASC";
		}
	}
	$groupby="";
	$qs="";
	if( (isset($_GET['groupby']))and(array_key_exists($_GET['groupby'],$table["fields"])) ){
		$table["pagination"]=0;
		foreach($table["fields"] as $field_id=>$field) if(!isset($field['value']))  if(!isset($field['run'])) if(!check_v($field['list'],false)){
			if(!isset($field['value']))  if(!isset($field['run'])){
				if($qs!="") $qs.=",";
				if( (isset($field['type']))and($field['type']=="number") ){
					if(isset($field['qcolumn'])) $qs.="SUM".$field['qcolumn']." AS `$field_id`"; else $qs.="SUM(`$field_id`) as `$field_id`";
				}else if($_GET['groupby']==$field_id){
					if(isset($field['qcolumn'])) $qs.=$field['qcolumn']." AS $field_id,COUNT(*)"; else $qs.="`$field_id`,COUNT(*)";
				}else $qs.="'' AS $field_id";
			}
			$data['fields'][]=$field_id;
		}
		$groupby="GROUP BY `{$_GET['groupby']}`";
		$data['totals']=[];
	}else{
		$qs=get_fields_query();
	}
	// Find total results
	if($groupby==""){
		$result=$db->query("SELECT COUNT(*) AS totalRows FROM ".$table["name"]." WHERE $filters;");
	}else{
		$field=$table['fields'][ $_GET['groupby'] ];
		if(isset($field['qcolumn'])){
			$result=$db->query("SELECT COUNT(*) AS totalRows FROM (SELECT ".$field['qcolumn']." AS ".$_GET['groupby']." FROM ".$table["name"]." WHERE $filters $groupby) AS Z;");
		}else $result=$db->query("SELECT COUNT(*) AS totalRows FROM (SELECT * FROM ".$table["name"]." WHERE $filters $groupby) AS Z;");
	}
	if($result){
		$row=mysqli_fetch_array($result);
		$data['totalRows']=$row['totalRows'];
	}
	// Correct the range of results
	$limit="";
	$startIndex=0;
	if(isset($table["pagination"])) if($table["pagination"]>0){
		$rowPage=$table["pagination"];
		if(isset($_GET['page'])) $startIndex=$rowPage*($_GET['page']-1); else $startIndex=0;
		if(isset($data['totalRows'])) if($startIndex>=$data['totalRows']) $startIndex=0;
		$limit="LIMIT $startIndex, $rowPage";
	}
	$data['startIndex']=$startIndex;
	$result=$db->query("SELECT $qs FROM ".$table["name"]." WHERE $filters $groupby $orderby $limit;");
	fill_data_rows($result);
}


function fill_data_rows(&$result){
	global $data, $table;
	$i = 0;
	if (!$result) return;

	while ($row=mysqli_fetch_array($result)) {

		$rowdata['row']=$row;
		foreach ($data['fields'] as $field_id=>$field) {
			$fv = "";

			if(isset($table["fields"][$field]['run'])) $fv=$table["fields"][$field]['run']($rowdata);
			if(isset($table["fields"][$field]['value'])) $fv=$table["fields"][$field]['value']($rowdata);

			if(isset($row[$field])) $fv=$row[$field];

			if($fv==" ") $fv="&nbsp;";

			if(check_v($table["fields"][$field]['type'],"index")) $fv=strval($i+1);

			$rowdata['value']=$fv;
			$data['rows'][$i][]=$fv;

		}
		if(isset($data['totals'])) $data['totals'][]=$row['COUNT(*)'];
		$i++;
	}
}

function fill_field_options(){
	global $table, $link, $db;

	foreach ($table["fields"] as $field_id=>$field) {
		if (isset($field['qoptions'])) {
			$result = $db->query("SELECT ".$field['qoptions']);
			$options_array = array();
			if (!$result) break;
			while ($row=mysqli_fetch_array($result)){
				$table["fields"][$field_id]['options'][ $row['Index'] ]= $row['Text'];
			}
		}
	}
}

function fill_field_datalist(){
	global $table, $link, $db;

	foreach($table["fields"] as $field_id=>$field){
		if(isset($field['type'])) if($field['type']=="datalist") if(!isset($field['datalist'])) {
			$result=$db->query("SELECT `$field_id` AS Text FROM ".$table['name']." GROUP BY $field_id ORDER BY $field_id");
			if(!$result) break;
			$options_array=array();
			while($row=mysqli_fetch_array($result)){
				$options_array[ ]= $row['Text'];
			}
			$table["fields"][$field_id]['datalist']=$options_array;
		}
	}
}

function update_pdf($id){
	global $table,$link,$db;

	if (isset($table["commands"]["pdf"]["folder"])) {
		$pdff=$table["commands"]["pdf"]["folder"];
	} else return;
	if (isset($table['commands']['pdf']['prefix'])) {
		$fnprefix=$table['commands']['pdf']['prefix'];
	} else $fnprefix="Report";

	// PDF Generator for the Pdf file
	$folder=getFileFolder($_GET['t'],$pdff);
	if (!file_exists($folder)) mkdir($folder, 0755);

	$sop_url = URL_DOMAIN.PNK_URL.'pdf.php?t='.$_GET['t'].'&id='.$id;
	$sop_pdf = $folder.$fnprefix.$id.".pdf";
	unlink($sop_pdf);
	file_put_contents($sop_pdf, file_get_contents($sop_url));
}

function get_fields_query(){
	global $data, $table, $db;

	$qs = "";
	foreach ($table["fields"] as $field_id=>$field)  if (!check_v($field['list'],false)) {
		if (!isset($field['value'])) if (!isset($field['run'])) {
			if ($qs!="") $qs.=",";
			if (isset($field['qcolumn'])) $qs.=$field['qcolumn']." AS $field_id"; else $qs.="`$field_id`";
		}
		$data['fields'][]=$field_id;
	}
	return $qs;
}
// Check value
function check_v(&$var,$v){
	if(!isset($var)) return false;
	if($var==$v) return true; else return false;
}

function editable($fid){
	global $table;
	$field = $table['fields'][$fid];

	if(!in_array($fid,array_keys($table['fields']))) return false;
	if(isset($field["edit"])) if ($field["edit"] == true) return true;
	if(isset($field["qcolumn"])) return false;

	if (isset($table['commands'])) {
		if(!in_array('edit',$table['commands'])) {
			if(!check_v($field['edit'],true)) return false;
		}
	}else return false;

	if(check_v($field['edit'],false)) return false;
	return true;
}
