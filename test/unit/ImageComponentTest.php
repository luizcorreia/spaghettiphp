<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';
import('core.Mapper');
import('components.ImageComponent');

// dimensions of the images:
// tall.jpg: 274x350
// wide.jpg: 450x250
// square.jpg: 300x300
// square.gif: 300x300

class ImageComponentTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->Image = new ImageComponent;
    }
    public function tearDown() {
        if(file_exists('results.jpg')):
            unlink('results.jpg');
        endif;
        if(file_exists('results.gif')):
            unlink('results.gif');
        endif;
    }

    public function testSize() {
        $expected = array(
            'width' => 300,
            'height' => 300
        );
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('square.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithFixedSize() {
        $expected = array(
            'width' => 250,
            'height' => 250
        );
        $this->Image->resize('square.jpg', array(
            'width' => 250,
            'height' => 250,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedSquareSize() {
        $expected = array(
            'width' => 250,
            'height' => 250
        );
        $this->Image->resize('square.jpg', array(
            'width' => 250,
            'height' => 250,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedTallSize() {
        $expected = array(
            'width' => 137,
            'height' => 175
        );
        $this->Image->resize('tall.jpg', array(
            'width' => 100,
            'height' => 175,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedWideSize() {
        $expected = array(
            'width' => 225,
            'height' => 125
        );
        $this->Image->resize('wide.jpg', array(
            'width' => 225,
            'height' => 100,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedWideSizeAndSquareThumbnail() {
        $expected = array(
            'width' => 100,
            'height' => 55
        );
        $this->Image->resize('wide.jpg', array(
            'width' => 100,
            'height' => 100,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedTallSizeAndSquareThumbnail() {
        $expected = array(
            'width' => 78,
            'height' => 100
        );
        $this->Image->resize('tall.jpg', array(
            'width' => 100,
            'height' => 100,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedTallSizeAndWideThumbnail() {
        $expected = array(
            'width' => 78,
            'height' => 100
        );
        $this->Image->resize('tall.jpg', array(
            'width' => 200,
            'height' => 100,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedWideSizeAndTallThumbnail() {
        $expected = array(
            'width' => 100,
            'height' => 55
        );
        $this->Image->resize('wide.jpg', array(
            'width' => 100,
            'height' => 200,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedWidthAndWideImage() {
        $expected = array(
            'width' => 100,
            'height' => 55
        );
        $this->Image->resize('wide.jpg', array(
            'width' => 100,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedHeightAndWideImage() {
        $expected = array(
            'width' => 180,
            'height' => 100
        );
        $this->Image->resize('wide.jpg', array(
            'height' => 100,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedWidthAndTallImage() {
        $expected = array(
            'width' => 100,
            'height' => 127
        );
        $this->Image->resize('tall.jpg', array(
            'width' => 100,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithConstrainedHeightAndTallImage() {
        $expected = array(
            'width' => 78,
            'height' => 100
        );
        $this->Image->resize('tall.jpg', array(
            'height' => 100,
            'constrain' => true,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithGifImage() {
        $expected = IMAGETYPE_GIF;
        $this->Image->resize('square.gif', array(
            'width' => 250,
            'height' => 250,
            'filename' => 'results.gif'
        ));
        $results = $this->Image->size('results.gif');
        
        $this->assertEquals($results[2], $expected);
    }
    public function testResizeWithGifImageAndJpegOutput() {
        $expected = IMAGETYPE_JPEG;
        $this->Image->resize('square.gif', array(
            'width' => 250,
            'height' => 250,
            'filename' => 'results.jpg'
        ));
        $results = $this->Image->size('results.jpg');
        
        $this->assertEquals($results[2], $expected);
    }
    public function testResizeWithJpegImageAndGifOutput() {
        $expected = IMAGETYPE_GIF;
        $this->Image->resize('square.jpg', array(
            'width' => 250,
            'height' => 250,
            'filename' => 'results.gif'
        ));
        $results = $this->Image->size('results.gif');
        
        $this->assertEquals($results[2], $expected);
    }
    public function testScaleWithSquareImage() {
        $expected = array(
            'width' => 150,
            'height' => 150
        );
        $this->Image->scale('square.jpg', array(
            'scale' => 50,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
    public function testScaleWithWideImage() {
        $expected = array(
            'width' => 225,
            'height' => 125
        );
        $this->Image->scale('wide.jpg', array(
            'scale' => 50,
            'filename' => 'results.jpg'
        ));
        $results = array();
        list($results['width'], $results['height']) = $this->Image->size('results.jpg');
        
        $this->assertEquals($expected, $results);
    }
}

?>