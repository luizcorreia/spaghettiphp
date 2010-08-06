<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/test.php';
require_once 'lib/helpers/FormHelper.php';

class FormHelperTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new FormHelper(new View());
    }
    
    /**
     * @testdox create should return a form tag
     */
    public function testCreateShouldReturnAFormTag() {
        $expected = '<form action="/" method="post">';
        $actual = $this->form->create('users');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox create with parameters should return a form tag
     */
    public function testCreateWithParametersShouldReturnAFormTag() {
        $expected = '<form class="users-form" action="/users" method="post">';
        $actual = $this->form->create('users', '/users', array('class' => 'users-form'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox create with method=file should be encoded as multipart/form-data
     */
    public function testCreateWithMethodFileShouldBeEncodedAsMultipart() {
        $expected = '<form method="post" action="/users" enctype="multipart/form-data">';
        $actual = $this->form->create('users', '/users', array('method' => 'file'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox close should end form tag
     */
    public function testCloseShouldEndFormTag() {
        $expected = '</form>';
        $this->form->create('users');
        $actual = $this->form->close();
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox create should push to the form stack
     */
    public function testCreateShouldPushToTheFormStack() {
        $expected = 'users';
        $this->form->create('users');
        $actual = $this->form->model();
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox close should pop from the form stack
     */
    public function testCloseShouldPopFromTheFormStack() {
        $expected = null;
        $this->form->create('users');
        $this->form->close();
        $actual = $this->form->model();
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox label should return a label to a form field
     */
    public function testLabelShouldReturnALabelToAFormField() {
        $expected = '<label class="form-label" for="users_username">User Name</label>';
        $this->form->create('users');
        $actual = $this->form->label('username', 'User Name', array('class' => 'form-label'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox label without $text should use humanized field name
     */
    public function testLabelWithoutTextShouldUseHumanizedFieldName() {
        $expected = '<label for="users_username">Username</label>';
        $this->form->create('users');
        $actual = $this->form->label('username');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox text should return a input with type=text
     */
    public function testTextShouldReturnAInputWithTypeText() {
        $expected = '<input value="spaghettiphp" type="text" id="users_username" name="users[username]" />';
        $this->form->create('users');
        $actual = $this->form->text('username', array('value' => 'spaghettiphp'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox textarea should return a textarea
     */
    public function testTextareaShouldReturnATextarea() {
        $expected = '<textarea class="text" id="users_description" name="users[description]"></textarea>';
        $this->form->create('users');
        $actual = $this->form->textarea('description', array('class' => 'text'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox textarea should put value inside tag
     */
    public function testTextareaShouldPutValueInsideTag() {
        $expected = '<textarea id="users_description" name="users[description]">value</textarea>';
        $this->form->create('users');
        $actual = $this->form->textarea('description', array('value' => 'value'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox password should return a input with type=password
     */
    public function testPasswordShouldReturnAInputWithTypePassword() {
        $expected = '<input value="123456" type="password" id="users_password" name="users[password]" />';
        $this->form->create('users');
        $actual = $this->form->password('password', array('value' => '123456'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox hidden should return a input with type=hidden
     */
    public function testHiddenShouldReturnAInputWithTypeHidden() {
        $expected = '<input value="123456" type="hidden" id="users_hidden" name="users[hidden]" />';
        $this->form->create('users');
        $actual = $this->form->hidden('hidden', array('value' => '123456'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox file should return a input with type=file
     */
    public function testFileShouldReturnAInputWithTypeFile() {
        $expected = '<input class="user-avatar" type="file" id="users_avatar" name="users[avatar]" />';
        $this->form->create('users');
        $actual = $this->form->file('avatar', array('class' => 'user-avatar'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox checkbox should return a input with type=checkbox and another with type=hidden
     */
    public function testCheckboxShouldReturnAInputWithTypeCheckbox() {
        $expected  = '<input value="0" type="hidden" name="users[newsletter]" />';
        $expected .= '<input value="1" type="checkbox" id="users_newsletter" name="users[newsletter]" />';
        $this->form->create('users');
        $actual = $this->form->checkbox('newsletter');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox radio should return a input with type=radio
     */
    public function testRadioShouldReturnAInputWithTypeRadio() {
        $expected = '<input value="yes" id="users_newsletter_yes" type="radio" name="users[newsletter]" />';
        $this->form->create('users');
        $actual = $this->form->radio('newsletter', 'yes');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox select should return a select with options
     */
    public function testSelectShouldReturnASelectWithOptions() {
        $expected  = '<select id="users_role" name="users[role]">';
        $expected .= '<option value="user">User</option><option value="admin">Admin</option>';
        $expected .= '</select>';
        $this->form->create('users');
        $actual = $this->form->select('role', array(
            'user' => 'User',
            'admin' => 'Admin'
        ));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox select with value attribute should have selected option
     */
    public function testSelectWithValueAttributeShouldHaveSelectedOption() {
        $expected  = '<select id="users_role" name="users[role]">';
        $expected .= '<option value="user" selected="selected">User</option>';
        $expected .= '<option value="admin">Admin</option></select>';
        $this->form->create('users');
        $actual = $this->form->select('role', array(
            'user' => 'User',
            'admin' => 'Admin'
        ), array('value' => 'user'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox submit should return a input with type=submit
     */
    public function testSubmitShouldReturnAInputWithTypeSubmit() {
        $expected = '<input class="button-submit" name="commit" type="submit" value="Submit" />';
        $this->form->create('users');
        $actual = $this->form->submit('Submit', array('class' => 'button-submit'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox imagesubmit should return a input with type=image
     */
    public function testImagesubmitShouldReturnAInputWithTypeImage() {
        $expected = '<input class="button-submit" name="commit" type="image" src="/images/submit.png" />';
        $this->form->create('users');
        $actual = $this->form->imagesubmit('submit.png', array('class' => 'button-submit'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox button should return a button tag
     */
    public function testButtonShouldReturnAButtonTag() {
        $expected = '<button name="commit" type="submit">Submit</button>';
        $this->form->create('users');
        $actual = $this->form->button('Submit');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox button with second parameter should return a button tag with another type
     */
    public function testButtonWithSecondParameterShouldReturnAButtonTagWithAnotherType() {
        $expected = '<button name="commit" type="button">OK</button>';
        $this->form->create('users');
        $actual = $this->form->button('OK', 'button');
        
        $this->assertEquals($expected, $actual);
    }
}