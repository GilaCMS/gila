<?php


class image {

    static function make_thumb ($src,$file,$max_width,$max_height)
    {
        //if(!file_exists($src)) return false;
        if(!$image = @getimagesize($src)) return false;

        list($src_width,$src_height)=$image;
        $newwidth=$max_width;
        $newheight=$max_height;

        if($src_width>$max_width) {
            $newheight=($src_height/$src_width)*$newwidth;
        }else if($src_height>$max_height){
            $newwidth=($src_width/$src_height)*$newheight;
        }else{
            copy($src,$file);
            return;
        }

        $tmp=imagecreatetruecolor($newwidth,$newheight);

        switch($image[2]) {
            case 1:
            $img_src = imageCreateFromGIF($src);
            break;
            case 2:
            $img_src = imageCreateFromJPEG($src);
            break;
            case 3:
            $img_src = imageCreateFromPNG($src);
            break;
        }
        imagecopyresampled($tmp,$img_src,0,0,0,0,$newwidth,$newheight,$src_width,$src_height);
        //imagejpeg($tmp,$file,80);
        switch($image[2]) {
        case 1:
            imagegif($tmp,$file);
            break;
        case 2:
            imageinterlace($tmp, 1); //convert to progressive ?
            imagejpeg($tmp,$file,80);
            break;
        case 3:
            imagepng($tmp,$file);
            break;
        }
        imagedestroy($img_src);
        imagedestroy($tmp);
        return true;
    }
}
