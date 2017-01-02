<?php

include __DIR__."/config.php";

if(isset($_GET['t'])) include __DIR__."/table/".$_GET['t'].".php"; else{ echo "No table is specified"; exit; }
if(!isset($_GET['f'])){ echo "No field is specified"; exit; }
if(!isset($_GET['id'])){ echo "No id is specified"; exit; }

// Read the registry of ID
db_connect();
$result=$link->query("SELECT * FROM ".$_GET['t']." WHERE ".$table['id']."=".$_GET['id']);
$row=mysqli_fetch_array($result);
db_close();

// Check user login
if($_SESSION['user_id']==0) exit;

$editable=false;
if(isset($table["edit"])) if($table["edit"]==true){
	$editable=true;
}
if(isset($table["fields"][$_GET['f']]["edit"])) if($table["fields"][$_GET['f']]["edit"]==true) $editable=true; else $editable=false;
if(isset($table["no-edit"])){
	$no_f=$table["no-edit"][0];
	$no_v=$table["no-edit"][1];
	if($row[$no_f]==$no_v) $editable=false;
}

$folder=getFileFolder($_GET['t'],$_GET['f'],$_GET['id']);

$form_action="?t=".$_GET['t']."&f=".$_GET['f']."&id=".$_GET['id'];


////// Upload a file
if($_SERVER['REQUEST_METHOD'] == 'POST') if(isset($_GET["id"])) if(isset($_FILES["fileToUpload"])){
	$target_file = basename($_FILES["fileToUpload"]["name"]);
	$target_file = str_replace('ñ', 'n', $target_file);
	$target_file = str_replace('ó', 'o', $target_file);
	$target_file = str_replace('í', 'i', $target_file);
	$target_file = str_replace('ú', 'u', $target_file);
	$target_file = str_replace('é', 'e', $target_file);
	$target_file = str_replace(',', '', $target_file);
	$uploadOk = 1;
	
	// Check if file already exists
	if (file_exists($folder.$target_file)) {
		echo "<script>alert('The file already exists!')</script>";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "<script>alert('The file was not uploaded!')</script>";
		// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $folder.$target_file)) {
			// Success !
			$tsampled=$table['fields'][$_GET['f']]['img_samples'];
			if(isset($tsampled)) foreach($tsampled as $tsam){
				if($tsam[0]=="thumb") img_thumb($folder.$target_file,$tsam[1],$tsam[2],$folder.$target_file);
				if($tsam[0]=="sample") img_sampled($folder.$target_file,$tsam[1],$tsam[2],$folder.$target_file);
			}

		} else {
			echo "<script>alert('El archivo no he subirse!')</script>";
		}
	}
	

}

////// Delete a file
if(isset($_GET["id"])) if(isset($_GET["del"])) {
	unlink($folder.$_GET["del"]);
}

echo FONT_AWESOME;
echo JQUERY_JS;

?>


<style>
body{ margin:0; background:#000000; color:#FFFFFF; padding:6px }
header{ background:#FFFFFF; color:#000000 }
.upload-div{ background:#489211; }
div{ min-width:90px; min-height:90px; margin:8px; padding:0; display:inline-block; vertical-align:top; position:relative; }
img{ max-width:120px; max-height:120px;margin:0; }
i{ position:absolute; right:4px; bottom:4px }
i:hover{ cursor:pointer; opacity:0.8;}
</style>



<?php //<header>Header</header>
// Read the files of the folder
$n_files=0;
$files = scandir($folder);
$gallery="";
foreach ($files as $file) if($file!= ".") if($file!= ".."){
	$n_files++;
	if($editable==true) $exr="<i class='fa fa-remove fa-4' f='$file'></i>"; else $exr="";
	$gallery.="<div><img src='$folder$file'>$exr</div>";
}

// Update the value
db_connect();
$link->query("UPDATE ".$_GET['t']." SET ".$_GET['f']."=$n_files WHERE ID=".$_GET['id']);
db_close();


?>


<form action="<?php echo $form_action; ?>" method="post" enctype="multipart/form-data" style="display:none">
    <input type="file" name="fileToUpload" id="fileToUpload" onchange="var file_ext=$('#fileToUpload').val().split('.').pop(); if($.inArray( file_ext , [ 'htm','html','xml','pdf','jpg','txt','HTM','HTML','XML','PDF','JPG','TXT' ] )!== -1){ $('#ajax_loader').toggle();submit(); }else{ if(file_ext!='') alert('El tipo de archivo .'+file_ext+' no esta admite'); }">
</form>

<?php if($editable==true){  ?>
<div class="upload-div"><i style=" padding:10px" class="fa fa-arrow-up fa-4" onclick="$('#fileToUpload').click();"> Upload</i></div>
<?php }
echo $gallery; ?>

<div id="ajax_loader" style="text-align:center;display:none;position:fixed;top:0;left:0;bottom:0;right:0;opacity:0.2;z-index:1000; background:url(ajax_loader.gif) center center no-repeat;background-color:#000;">
	
</div>

<script>
	$('.fa-remove').on('click',function(){
		if (confirm('¿Este seguro eliminar este archivo?')){
			$('#ajax_loader').toggle();
			window.location.href="<?php echo $form_action; ?>&del="+$(this).attr('f');
		}
	});
</script>

<?php
// Create photo with specific sizes
function img_thumb($img,$width,$height,$src){
	list($src_width,$src_height)=getimagesize($src);
	//start and end point of the source sample to be copied
	$sx1=$sh1=$sx2=$sh2=0; 
	//check if is portrait or landscape
	if($src_height>$src_width){ //portrait
		$sx1=0;
		$sx2=$src_width;
		$sh1=($src_height-$src_width)/2;
		$sh2=$sh1+$src_width;
	}else{ //landscape
		$sh1=0;
		$sh2=$src_height;
		$sx1=($src_width-$src_height)/2;
		$sx2=$sx1+$src_height;
	}
	$thumb = imagecreatetruecolor($width,$height);
	$img_src = imagecreatefromjpeg($src);
	imagecopyresampled($thumb,$img_src,0,0,$sx1,$sh1,$width,$height,$sx2,$sh2);
	imagejpeg($thumb,$img,100); //Save the thumb
	//Save memory
	imagedestroy($img_src);
	imagedestroy($thumb);
}
// Create photo with maximun sizes
function img_sampled($img,$max_width,$max_height,$src){
	list($src_width,$src_height)=getimagesize($src);
	$newwidth=$max_width;
	$newheight=$max_height;
	if($src_width>$max_width){
		$newheight=($src_height/$src_width)*$newwidth;
	}else if($src_height>$max_height){
		$newwidth=($src_width/$src_height)*$newheight;
	}else{
		copy($src,$img);
		return;
	}

	$tmp=imagecreatetruecolor($newwidth,$newheight);
	$img_src = imagecreatefromjpeg($src);
	imagecopyresampled($tmp,$img_src,0,0,0,0,$newwidth,$newheight,$src_width,$src_height);
	imagejpeg($tmp,$img,100);
	imagedestroy($img_src);
	imagedestroy($tmp);
}


?>
