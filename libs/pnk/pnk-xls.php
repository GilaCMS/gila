<?php

// filename for download
$filename = $table['title']. date('Y-m-d') . ".xls";

header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/vnd.ms-excel");

$exp="";



//add the tiles first here
foreach ($data['fields'] as $th){
	if($exp!="") $exp.="\t";
	if(isset($table['fields'][$th]['title'])) $exp.=$table['fields'][$th]['title']; else $exp.=$th;
}
echo $exp;

if(isset($data['rows'])) foreach ($data['rows'] as $tr) {
	echo "\r\n";
	foreach ($tr as $nfield=>$td){
		cleanData($td);
		$fieldid=$data['fields'][$nfield];
		$field=$table['fields'][$fieldid];
		if(isset($field['options'])) if(isset($field['options'][$td])) $td=$field['options'][$td];
		if(isset($field['dateFormat'])){
			$date = new DateTime($td);
			$td=$date->format($field['dateFormat']);
		}
		echo "$td\t";
	}
	//echo "";
}


  function cleanData(&$str)
  {
    $str = preg_replace("/\t/", "", $str);//\\t
    $str = preg_replace("/\r?\n/", "", $str);//\\n
    if($str=="null") $str="";
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }
