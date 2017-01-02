<?php

ob_start();


if(isset($table['pdf-fz'])) $fz=$table['pdf-fz']; else $fz="1e";
if(isset($table['pdf-pa'])) $PA=$table['pdf-pa']; else $PA='P';

?>
<style type="text/css">
table
{
    max-width:  100%;
    border-width: 0px;
    font-size:<?php echo $fz; ?>;
}

th{	text-align: center; vertical-align:bottom; 	/*border-bottom: solid 1px #000;*/ }
td{		text-align: left; border-width: 0px; }
th,td{ padding: 3px 6px; word-wrap: break-word; }

</style>
<?php // Put the head ?>



<page backtop="10mm" backbottom="10mm" backleft="5mm" backright="5mm">
    <page_header>
    </page_header>
    <page_footer>
        <span>Page [[page_cu]]/[[page_nb]]</span>
    </page_footer>
   
<span style="font-size: 20px; font-weight: bold; border-bottom:solid 1px #000"><?php echo strtoupper($table['title']); ?><br></span><br>

<?php // Search terms 
if(isset($data['filtered'])) foreach ($data['filtered'] as $f_key=>$f_value){
		echo "<span style='border-bottom:solid 1px #000'>".$table['fields'][$f_key]['title'].": ";
		if(is_array($f_value)){
			if(!isset($f_value[0])) $f_value[0]="";
			if(!isset($f_value[1])) $f_value[1]="";
			echo $f_value[0]." - ".$f_value[1];
		}else echo $f_value;
		echo "</span>";
	}
?>

<?php // Table - thead ?>


<table style="width:100%" width="100%" cellspacing="0">
<?php
		foreach ($data['fields'] as $th) echo "<col>";
?>
    <thead>
        <tr>
<?php
		foreach ($data['fields'] as $th){
			$field=$table['fields'][$th];
			if(check_v($field['show'],false)) continue;
			if(check_v($field['pdf'],false)) continue;
			if(isset($table['fields'][$th]['pdf-title'])) $th=$table['fields'][$th]['pdf-title']; else  $th=$table['fields'][$th]['title'];
			if(isset($field['pdf-w'])) $thw=" style='width:".$field['pdf-w']."'"; else $thw="";
			echo "<th$thw>$th</th>";
		}
?>
        </tr>
    </thead>
<?php
    if(isset($data['rows'])) foreach ($data['rows'] as $tr) {
		echo "<tr>";
		foreach ($tr as $nfield=>$td){
			$fieldid=$data['fields'][$nfield];
			$field=$table['fields'][$fieldid];
			if(check_v($field['show'],false)) continue;
			if(check_v($field['pdf'],false)) continue;
			
			if(isset($field['options'])) if(isset($field['options'][$td])) $td=$field['options'][$td];
			if(isset($field['type'])) if($field['type']=="date"){
				$date = new DateTime($td);
				if(isset($field['dateFormat'])) $td= $date->format($field['dateFormat']) ;
				if(isset($field['strftime'])) $td= strftime("%d.%b%y",$date->getTimestamp() ); //////// Only for spanish
			}
		
			if(isset($field['pdf-w'])) $thw=" style='width:".$field['pdf-w']."'"; else $thw="";
			echo "<td$thw>$td</td>";
		}
		echo "</tr>";
    }
?>

</table>

</page>

<?php

$content = ob_get_clean();

// convert in PDF
require_once(__DIR__.HTML2PDF_URL);
	try
	{
		$html2pdf = new HTML2PDF($PA, 'A4', 'en');//, true, 'UTF-8', array(15, 5, 15, 5)
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output( $table['title']. date('Y-m-d') . ".pdf" );
	}
    catch(HTML2PDF_exception $e) {
		echo $e;
	}

?>
