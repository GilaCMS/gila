<?php

include __DIR__."/config.php";

if($_SESSION['user_id']==0) exit;

if(isset($_GET['t'])) include __DIR__.TABLE_FOLDER.$_GET['t'].".php";

if(isset($table['commands']['pdf']['folder'])){ // if folder is set that means that report is already stored in a file..hopefully
	$fnprefix=$table['commands']['pdf']['prefix'];
	$pdff=$table["commands"]["pdf"]["folder"];
	$folder=getFileFolder($_GET['t'],$pdff);
	$filename=$fnprefix.$_GET['id'].".pdf";
	$sop_pdf = $folder.$filename;
	if(file_exists($sop_pdf)){
	    header('Content-type: application/pdf');
	    header('Content-Disposition: inline; filename="' . $filename . '"');
	    header('Content-Transfer-Encoding: binary');
	    header('Accept-Ranges: bytes');
	    @readfile($sop_pdf);
	    exit;
	}
}

db_connect();
$result=$link->query("SELECT * FROM ".$table['name']." WHERE ".$table['id']."=".$_GET['id']);
$data=mysqli_fetch_array($result);
db_close();

ob_start();
if(isset($table['commands']['pdf']['template'])) include_once __DIR__."/pdf/".$table['commands']['pdf']['template'];
	else include_once "/pdf/".$_GET['t'].".php";
	
$content = ob_get_clean();
if(isset($table['commands']['pdf']['prefix'])) $fnprefix=$table['commands']['pdf']['prefix']; else $fnprefix="Report";

// convert in PDF
require_once(__DIR__.HTML2PDF_URL);
	try
	{
		$html2pdf = new HTML2PDF('P', 'A4', 'en');//, true, 'UTF-8', array(15, 5, 15, 5)
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output($fnprefix.$_GET['id'].'.pdf');
	}
    catch(HTML2PDF_exception $e) {
		echo $e;
	}

