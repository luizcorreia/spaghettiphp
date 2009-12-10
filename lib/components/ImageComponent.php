<?php
/**
 *  ImageComponent provê funcionalidades para a manipulação de imagens, como corte,
 *  redimensionamento, conversão entre formatos e geração de thumbnails.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

class ImageComponent extends Component {
    /**
      *  Short description.
      */
    protected $destiny = array(
        'constrain' => false,
        'height' => 0,
        'quality' => 80,
        'resize' => false,
        'width' => 0,
        'x' => 0,
        'y' => 0
    );
    protected $source = array(
        'x' => 0,
        'y' => 0
    );
    
    /**
      *  Short description.
      *
      *  @param string $filename
      *  @param array $destiny
      *  @return boolean
      */
    public function resize($filename, $destiny = array()) {
        $destiny += $this->destiny;
        $size = $this->size($filename);
        extract($size);
        if($destiny['constrain']):
            if(
                $destiny['width'] && ($width > $height || !$destiny['height'])
            ):
                $ratio = $destiny['width'] / $width;
                $destiny['height'] = floor($height * $ratio);
            elseif(
                $destiny['height'] && ($width < $height || !$destiny['width'])
            ):
                $ratio = $destiny['height'] / $height;
                $destiny['width'] = floor($width * $ratio);
            endif;
        endif;
        
        return $this->image($filename, $size + $this->source, $destiny);
    }
    /**
      *  Short description.
      *
      *  @param string $filename
      *  @param array $destiny
      *  @return boolean
      */
    public function scale($filename, $destiny = array()) {
        $destiny += $this->destiny;
        $size = $this->size($filename);
        extract($size);
        $destiny['width'] = $width * ($destiny['scale'] / 100);
        $destiny['height'] = $height * ($destiny['scale'] / 100);

        return $this->image($filename, $size + $this->source, $destiny);
    }
    /**
      *  Short description.
      *
      *  @param string $filename
      *  @param array $destiny
      *  @return boolean
      */
    public function crop($filename, $destiny = array()) {
        $destiny += $this->destiny;
        $size = $this->size($filename);        
        $source = $this->cropSource($size, $destiny);
        
        return $this->image($filename, $source, $destiny);
    }
    /**
      *  Short description.
      *
      *  @param string $filename
      *  @return array
      */
    public function size($filename) {
        $size = getimagesize($filename);
        return array(
            'width' => $size[0],
            'height' => $size[1],
            'type' => $size[2]
        );
    }
    /**
      *  Short description.
      *
      *  @param string $filename
      *  @return string
      */
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
    /**
      *  Short description.
      *
      *  @param string $filename
      *  @param array $size
      *  @param array $destiny
      *  @return boolean
      */
    protected function image($filename, $source, $destiny) {
        $input_type = image_type_to_extension($source['type'], false);
        $input_function = 'imagecreatefrom' . $input_type;
        
        if(!isset($destiny['filename'])):
            $destiny['filename'] = $filename;
        endif;
        $output_type = $this->imageType($destiny['filename']);
        $output_function = 'image' . $output_type;
        
        $input = $input_function($filename);
        $output = imagecreatetruecolor($destiny['width'], $destiny['height']);
        imagecopyresampled(
            $output, $input,
            $destiny['x'], $destiny['y'],
            $source['x'], $source['y'],
            $destiny['width'], $destiny['height'],
            $source['width'], $source['height']
        );
        imagedestroy($input);

        // @todo check for PNG quality
        $output_image = $output_function($output, $destiny['filename'], $destiny['quality'], PNG_ALL_FILTERS);
        imagedestroy($output);
        
        return $output_image;
    }
    /**
      *  Short description.
      *
      *  @param array $source
      *  @param array $destiny
      *  @return array
      */
    protected function cropSource($source, $destiny) {
        extract($source);
        $source['width'] = $destiny['width'];
        $source['height'] = $destiny['height'];
        if($destiny['resize']):
            if($width > $height):
                $source['height'] = $height;
                $source['width'] = floor($height * $destiny['width'] / $destiny['height']);
            else:
                $source['width'] = $width;
                $source['height'] = floor($width * $destiny['height'] / $destiny['width']);
            endif;
        endif;
        $source['x'] = floor(($width - $source['width']) / 2);
        $source['y'] = floor(($height - $source['height']) / 2);
        
        return $source;
    }
}