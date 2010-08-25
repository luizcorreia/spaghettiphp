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
        $actual = $this->form->modelname();
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox close should pop from the form stack
     */
    public function testCloseShouldPopFromTheFormStack() {
        $expected = null;
        $this->form->create('users');
        $this->form->close();
        $actual = $this->form->modelname();
        
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
        $expected = '<input value="spaghettiphp" id="users_username" name="users[username]" type="text" />';
        $this->form->create('users');
        $actual = $this->form->text('username', array('value' => 'spaghettiphp'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox textarea should return a textarea
     */
    public function testTextareaShouldReturnATextarea() {
        $expected = '<textarea id="users_description" name="users[description]" class="text"></textarea>';
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
        $expected = '<input value="123456" id="users_password" name="users[password]" type="password" />';
        $this->form->create('users');
        $actual = $this->form->password('password', array('value' => '123456'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox hidden should return a input with type=hidden
     */
    public function testHiddenShouldReturnAInputWithTypeHidden() {
        $expected = '<input value="123456" id="users_hidden" name="users[hidden]" type="hidden" />';
        $this->form->create('users');
        $actual = $this->form->hidden('hidden', array('value' => '123456'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox file should return a input with type=file
     */
    public function testFileShouldReturnAInputWithTypeFile() {
        $expected = '<input id="users_avatar" name="users[avatar]" type="file" class="user-avatar" />';
        $this->form->create('users');
        $actual = $this->form->file('avatar', array('class' => 'user-avatar'));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox checkbox should return a input with type=checkbox and another with type=hidden
     */
    public function testCheckboxShouldReturnAInputWithTypeCheckbox() {
        $expected  = '<input value="0" name="users[newsletter]" type="hidden" />';
        $expected .= '<input value="1" id="users_newsletter" name="users[newsletter]" type="checkbox" />';
        $this->form->create('users');
        $actual = $this->form->checkbox('newsletter');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox radio should return a input with type=radio
     */
    public function testRadioShouldReturnAInputWithTypeRadio() {
        $expected = '<input value="yes" id="users_newsletter_yes" name="users[newsletter]" type="radio" />';
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
     * @testdox select with attribute empty=true should add empty option
     */
    public function testSelectWithEmptyAttributeShouldAddEmptyOption() {
        $expected  = '<select id="users_role" name="users[role]">';
        $expected .= '<option value="0"></option><option value="user">User</option>';
        $expected .= '<option value="admin">Admin</option></select>';
        $this->form->create('users');
        $actual = $this->form->select('role', array(
            'user' => 'User',
            'admin' => 'Admin'
        ), array(
            'empty' => true
        ));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox select with attribute empty=array should add empty option
     */
    public function testSelectWithEmptyArrayAttributeShouldAddEmptyOption() {
        $expected  = '<select id="users_role" name="users[role]">';
        $expected .= '<option value="empty">Empty</option><option value="user">User</option>';
        $expected .= '<option value="admin">Admin</option></select>';
        $this->form->create('users');
        $actual = $this->form->select('role', array(
            'user' => 'User',
            'admin' => 'Admin'
        ), array(
            'empty' => array('empty' => 'Empty')
        ));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox select with attribute empty=string should add empty option
     */
    public function testSelectWithEmptyStringAttributeShouldAddEmptyOption() {
        $expected  = '<select id="users_role" name="users[role]">';
        $expected .= '<option value="0">Empty</option><option value="user">User</option>';
        $expected .= '<option value="admin">Admin</option></select>';
        $this->form->create('users');
        $actual = $this->form->select('role', array(
            'user' => 'User',
            'admin' => 'Admin'
        ), array(
            'empty' => 'Empty'
        ));
        
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

    /**
     * @testdox text should add model value to input
     */
    public function testTextShouldAddModelValueToInput() {
        $expected = '<input value="spaghettiphp" id="users_username" name="users[username]" type="text" />';
        $user = new Users();
        $user->username = 'spaghettiphp';
        $this->form->create($user);
        $actual = $this->form->text('username');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox textarea should add model value to tag
     */
    public function testTextareaShouldAddModelValueToTag() {
        $expected = '<textarea id="users_description" name="users[description]">Spaghetti* Framework</textarea>';
        $user = new Users();
        $user->description = 'Spaghetti* Framework';
        $this->form->create($user);
        $actual = $this->form->textarea('description');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox select should select option from model value
     */
    public function testSelectShouldSelectOptionFromModelValue() {
        $expected  = '<select id="users_role" name="users[role]">';
        $expected .= '<option value="user">User</option>';
        $expected .= '<option value="admin" selected="selected">Admin</option></select>';

        $user = new Users();
        $user->role = 'admin';
        $this->form->create($user);
        $actual = $this->form->select('role', array(
            'user' => 'User',
            'admin' => 'Admin'
        ));
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox radio should add model value to input
     */
    public function testRadioShouldAddModelValueToInput() {
        $expected = '<input value="yes" id="users_newsletter_yes" name="users[newsletter]" type="radio" checked="checked" />';
        $user = new Users();
        $user->newsletter = 'yes';
        $this->form->create($user);
        $actual = $this->form->radio('newsletter', 'yes');
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @testdox text should add class="error" if field failed validation
     */
    public function testTextShouldAddClassErrorIfFieldFailedValidation() {
        $expected = '<input id="users_username" name="users[username]" type="text" class="error" />';
        $user = new Users();
        $user->validate();
        $this->form->create($user);
        $actual = $this->form->text('username');
        
        $this->assertEquals($expected, $actual);
    }
    
}