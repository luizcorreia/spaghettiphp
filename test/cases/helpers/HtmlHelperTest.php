<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/test.php';
require_once 'lib/helpers/HtmlHelper.php';

class HtmlHelperTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->html = new HtmlHelper(new View());
    }
    
    /**
     * @testdox tag should return a HTML tag
     */
    public function testTagShouldReturnAHtmlTag() {
        $expected = '<p id="paragraph">Spaghetti Framework</p>';
        $actual = $this->html->tag('p', 'Spaghetti Framework', array(
            'id' => 'paragraph'
        ));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox tag without content should return an empty tag
     */
    public function testTagWithoutContentShouldReturnAnEmptyTag() {
        $expected = '<hr />';
        $actual = $this->html->tag('hr');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox tag with false attribute should suppress the attribute
     */
    public function testTagWithFalseAttributeShouldSuppressTheAttribute() {
        $expected = '<input />';
        $actual = $this->html->tag('input', null, array('checked' => false));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox tag with true attribute should duplicate the attribute in the value
     */
    public function testTagWithTrueAttributeShouldDuplicateTheAttributeInTheValue() {
        $expected = '<input checked="checked" />';
        $actual = $this->html->tag('input', null, array('checked' => true));
        
        $this->assertEquals($expected, $actual);
    }
}