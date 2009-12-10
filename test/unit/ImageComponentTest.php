<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';
import('core.Mapper');
import('components.ImageComponent');

// image dimensions:
// tall.jpg: 274x350
// wide.jpg: 450x250
// square.jpg: 300x300
// square.gif: 300x300

require_once "ImageComponent.php";

class TestImageComponent extends ImageComponent {
    public function cropSource($source, $destiny) {
        return parent::cropSource($source, $destiny);
    }
}

class ImageComponentTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->Image = new TestImageComponent;
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
        $results = $this->Image->size('square.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
        $this->assertEquals($expected, $results);
    }
    public function testResizeWithFixedSizeAndSameFilename() {
        copy('square.jpg', 'results.jpg');
        $expected = array(
            'width' => 250,
            'height' => 250
        );
        $this->Image->resize('results.jpg', array(
            'width' => 250,
            'height' => 250
        ));
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);        
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        
        $this->assertEquals($results['type'], $expected);
    }
    public function testResizeWithGifImageAndJpegOutput() {
        $expected = IMAGETYPE_JPEG;
        $this->Image->resize('square.gif', array(
            'width' => 250,
            'height' => 250,
            'filename' => 'results.jpg'
        ));
        $results = $this->Image->size('results.jpg');
        
        $this->assertEquals($results['type'], $expected);
    }
    public function testResizeWithJpegImageAndGifOutput() {
        $expected = IMAGETYPE_GIF;
        $this->Image->resize('square.jpg', array(
            'width' => 250,
            'height' => 250,
            'filename' => 'results.gif'
        ));
        $results = $this->Image->size('results.gif');
        
        $this->assertEquals($results['type'], $expected);
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
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
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
        $this->assertEquals($expected, $results);
    }
    public function testCrop() {
        $expected = array(
            'width' => 200,
            'height' => 200
        );
        $this->Image->crop('tall.jpg', array(
            'height' => 200,
            'width' => 200,
            'filename' => 'results.jpg',
            'resize' => false
        ));
        $results = $this->Image->size('results.jpg');
        unset($results['type']);
        
        $this->assertEquals($expected, $results);
    }
    public function testCropSourceWithoutResizeAndTallImage() {
        $params = array(
            'width' => 200,
            'height' => 200,
            'resize' => false
        );        
        $expected = array(
            'height' => 200,
            'width' => 200,
            'x' => 37,
            'y' => 75,
            'type' => IMAGETYPE_JPEG
        );
        $results = $this->Image->cropSource($this->Image->size('tall.jpg'), $params);

        $this->assertEquals($expected, $results);
    }
    public function testCropSourceWithSquareResizeAndTallImage() {
        $params = array(
            'width' => 200,
            'height' => 200,
            'resize' => true
        );        
        $expected = array(
            'height' => 274,
            'width' => 274,
            'x' => 0,
            'y' => 38,
            'type' => IMAGETYPE_JPEG
        );
        $results = $this->Image->cropSource($this->Image->size('tall.jpg'), $params);

        $this->assertEquals($expected, $results);
    }
    public function testCropSourceWithTallResizeAndTallImage() {
        $params = array(
            'width' => 200,
            'height' => 250,
            'resize' => true
        );        
        $expected = array(
            'height' => 342,
            'width' => 274,
            'x' => 0,
            'y' => 4,
            'type' => IMAGETYPE_JPEG
        );
        $results = $this->Image->cropSource($this->Image->size('tall.jpg'), $params);

        $this->assertEquals($expected, $results);
    }
    public function testCropSourceWithWideResizeAndWideImage() {
        $params = array(
            'width' => 250,
            'height' => 200,
            'resize' => true
        );        
        $expected = array(
            'height' => 250,
            'width' => 312,
            'x' => 69,
            'y' => 0,
            'type' => IMAGETYPE_JPEG
        );
        $results = $this->Image->cropSource($this->Image->size('wide.jpg'), $params);

        $this->assertEquals($expected, $results);
    }
    public function testCropSourceWithTallResizeAndWideImage() {
        $params = array(
            'width' => 200,
            'height' => 250,
            'resize' => true
        );        
        $expected = array(
            'height' => 250,
            'width' => 200,
            'x' => 125,
            'y' => 0,
            'type' => IMAGETYPE_JPEG
        );
        $results = $this->Image->cropSource($this->Image->size('wide.jpg'), $params);

        $this->assertEquals($expected, $results);
    }
    public function testCropSourceWithWideResizeAndTallImage() {
        $params = array(
            'width' => 250,
            'height' => 200,
            'resize' => true
        );        
        $expected = array(
            'height' => 219,
            'width' => 274,
            'x' => 0,
            'y' => 65,
            'type' => IMAGETYPE_JPEG
        );
        $results = $this->Image->cropSource($this->Image->size('tall.jpg'), $params);

        $this->assertEquals($expected, $results);
    }
}