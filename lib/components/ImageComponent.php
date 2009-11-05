<?php

class ImageComponent {
    protected $params = array(
        'constrain' => false,
        'quality' => 80
    );
    
    public function resize($filename, $params = array()) {
        $params += $this->params;
        $size = $this->size($filename);
        list($width, $height) = $size;
        if($params['constrain']):
            if(
                isset($params['width']) && ($width > $height || !isset($params['height']))
            ):
                $ratio = $params['width'] / $width;
                $params['height'] = floor($height * $ratio);
            elseif(
                isset($params['height']) && ($width < $height || !isset($params['width']))
            ):
                $ratio = $params['height'] / $height;
                $params['width'] = floor($width * $ratio);
            endif;
        endif;
        
        return $this->image($filename, $size, $params);
    }
    public function scale($filename, $params = array()) {
        $params += $this->params;
        $size = $this->size($filename);
        list($width, $height) = $size;
        $params['width'] = $width * ($params['scale'] / 100);
        $params['height'] = $height * ($params['scale'] / 100);

        return $this->image($filename, $size, $params);
    }
    public function size($filename) {
        return getimagesize($filename);
    }
    public function imageType($filename) {
        $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
        switch($ext):
            case 'jpeg':
            case 'jpg':
                return 'jpeg';
            case 'gif':
                return 'gif';
            case 'png':
                return 'png';
            default:
                return false;
        endswitch;
    }

    protected function image($filename, $size, $params) {
        list($width, $height) = $size;
        $input_type = image_type_to_extension($size[2], false);
        $input_function = 'imagecreatefrom' . $input_type;
        $output_type = $this->imageType($params['filename']);
        $output_function = 'image' . $output_type;
        
        $input = $input_function($filename);
        $output = imagecreatetruecolor($params['width'], $params['height']);
        imagecopyresampled($output, $input, 0, 0, 0, 0, $params['width'], $params['height'], $height, $width);

        // @todo check for PNG quality
        return $output_function($output, $params['filename'], $params['quality'], PNG_ALL_FILTERS);
    }
}

?>