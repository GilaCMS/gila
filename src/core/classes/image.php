<?php


class image {

    /**
     * Creates a thumbnail image from a file.
     * @param $src (string) Path to the original image
     * @param $file (string) Path to the thumbnail to create
     * @param $max_width (int) Maximun width in pixels for the new image
     * @param $max_height (int) Maximun height in pixels for the new image
     * @return boolean True if the thumbnail is created successfully
     */
    static function make_thumb ($src,$file,$max_width,$max_height)
    {
        if(parse_url($src, PHP_URL_HOST) != null) {
            $_src = 'tmp/'.str_replace(["://",":\\\\","\\","/",":"], "_", $src);
            if(!file_exists($_src)) {
                if(!copy($src, $_src)) return false;
            }
            $src = $_src;
        }

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
            return true;
        }

        $tmp=imagecreatetruecolor($newwidth,$newheight);
        $img_src = self::create($src,$image[2]);

        imagecopyresampled($tmp,$img_src,0,0,0,0,$newwidth,$newheight,$src_width,$src_height);
        self::save($tmp,$file,$image[2]);
        imagedestroy($img_src);
        imagedestroy($tmp);
        return true;
    }

    /**
     * Creates an image depends the type.
     * @param $src (string) Path to the original image
     * @param $type (int) Original image type
     */
    static function create ($src, $type = 2)
    {
        if($type == 1)
            return imageCreateFromGIF($src);
        if($type == 2)
            return imageCreateFromJPEG($src);
        if($type == 3)
            return imageCreateFromPNG($src);

        return imageCreateFromJPEG($src);
    }

    /**
     * Saves an image.
     * @param $src (string) Path to the original image
     * @param $file (string) Path to the target image
     * @param $type (int) Original image type
     */
    static function save ($tmp,$file,$type = 2)
    {
        switch($type) {
        case 1:
            imagegif($tmp,$file);
            break;
        case 2:
            imageinterlace($tmp, 1);
            imagejpeg($tmp,$file);
            break;
        case 3:
            imagepng($tmp,$file);
            break;
        }

    }

    /**
     * Creates a stacked image from many files
     * @param $src_array (Array string) Paths to the original images
     * @param $file (string) Path to the stacked thumbnail to create
     * @param $max_width (int) Maximun width in pixels for the new images
     * @param $max_height (int) Maximun height in pixels for the new images
     * @return Array
     */
    static function make_stack ($src_array,$file,$max_width,$max_height)
    {
        $response = [];
        $dst_y = 0; $total_y = 0;

        foreach($src_array as $key=>$src) if($image = @getimagesize($src)) {
            list($src_width,$src_height)=$image;
            $newwidth=$max_width;
            $newheight=$max_height;

            if($src_width>$max_width) {
                $newheight = ($src_height/$src_width)*$newwidth;
            }else if($src_height>$max_height){
                $newwidth = ($src_width/$src_height)*$newheight;
            }

            $total_y += $newheight;
            $response[$key]=[
                'src' => $src,
                'src_width' => $src_width,
                'src_height' => $src_height,
                'width' => $newwidth,
                'height' => $newheight,
                'type' => $image[2]
            ];
        } else $response[$key] = false;

        $tmp = imagecreatetruecolor($max_width,$total_y);

        foreach($response as $key=>$img) if($img){
            $src = $src_array[$key];
            $img_src = self::create($src, $img['type']);
            imagecopyresampled($tmp,$img_src,0,$dst_y,0,0,$img['width'],$img['height'],$img['src_width'],$img['src_height']);
            $dst_y += $img['height'];
            $response[$key]['top'] = $dst_y;
            imagedestroy($img_src);
        }

        self::save($tmp,$file);
        imagedestroy($tmp);
        file_put_contents($file.'.json', json_encode($response));
        return $response;
    }
}
