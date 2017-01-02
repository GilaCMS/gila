<?php

// filename to be downloaded
$filename = $table['title']. date('Y-m-d') . ".csv";

header("Content-Disposition: attachment; filename=\"$filename\"");
//header("Content-Type: application/excel");

$exp="";



//add the tiles first here
foreach ($data['fields'] as $th) if(in_array($th,$table['csv'])){
	if($exp!="") $exp.=",";
	if(isset($table['fields'][$th]['title'])) $thv=$table['fields'][$th]['title']; else $thv="\"$th\"";
	$exp.="$thv"; //$exp.="\"$thv\"";
}
echo $exp;

if(isset($data['rows'])) foreach ($data['rows'] as $tr) {
	echo "\r\n";
	foreach ($tr as $nfield=>$td) {
		cleanData($td);
		$fieldid=$data['fields'][$nfield];
		$field=$table['fields'][$fieldid];
		if(in_array($fieldid,$table['csv'])) {
			if(isset($field['options'])) if(isset($field['options'][$td])) $td=$field['options'][$td];
			if(isset($field['dateFormat'])){
				$date = new DateTime($td);
				$td=$date->format($field['dateFormat']);
			}
			//if(isset($field['type'])) if($field['type']="string")
			$td = str_replace('"','\'',$td);
			$td="\"$td\"";
			echo "$td,";
		}
	}
}


  function cleanData(&$str)
  {
    $str = preg_replace("/\t/", "", $str);//\\t
    $str = preg_replace("/\r?\n/", "", $str);//\\n
    if($str=="null") $str="";
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }
