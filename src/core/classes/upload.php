<?php

function img_sampled($img,$width,$height,$src){
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
function img_thumb($img,$max_width,$max_height,$src){
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





if($_SESSION['submited']==0){

//Capitalize the 1st characters
$title = ucfirst($title);
$text = ucfirst($text);
// Remove the commas from price
$price=str_replace(',', '', $price);

$pc = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 6);


$db->query("
	INSERT INTO `clas`(`state`,`municipality`, `category`, `tr`, `text`, `contact_name`, `contact_num`,`contact_email`,`price`,`title`,`pc`)
	VALUES ('$state','$m','$cat','$tr','$text','$name','$tel','$email','$price','$title','$pc')
	");

// We need $row[id] for the image upload
$result = $db->query("SELECT * FROM clas WHERE id=LAST_INSERT_ID()");
$row = mysqli_fetch_assoc($result);




// Upload the images

$allowedExts = array("gif", "jpeg", "jpg", "png","GIF", "JPEG", "JPG", "PNG");
$allowedTypes = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png" );

for($i=-1,$n=0;$n<IMGS_PER_AD;$n++) if(!empty($_FILES["upimg".$n]['name'])){
	$i++;
	$upimg="upimg".$n;
	$temp = explode(".", $_FILES[$upimg]['name']);
	$extension = end($temp);
	if (in_array($_FILES[$upimg]['type'], $allowedTypes)
	&& ($_FILES[$upimg]['size'] < 800000)
	&& in_array($extension, $allowedExts)) {

		if (isset($_FILES["file"]["error"])) if ($_FILES["file"]["error"] > 0) {
			echo "Error: " . $_FILE[$upimg]['error'] . "<br>";
			echo _file_problem;
		}

		$target_path = IMG_PATH."uploaded/".$row['id']."_".$i."_".$_FILES[$upimg]['name'];
		move_uploaded_file($_FILES[$upimg]['tmp_name'],$target_path);

		if($i==0){
			$img_name = IMG_PATH.$row['id'];
		}else $img_name = IMG_PATH.$row['id']."_".$i;

		img_sampled($img_name."s.jpg",120,120,$target_path);

		img_thumb($img_name.".jpg",600,800,$target_path);

	}
}

$_SESSION['submited']=1;

}

?>

<div id="content">
<h1><?php echo _submited; ?></h1>
<?php echo _submited_txt; ?>
<br><br>
<a href="<?php siteURL(""); ?>"><div class="submit"><?php echo "< "._frontpage; ?></div></a>

</div>
