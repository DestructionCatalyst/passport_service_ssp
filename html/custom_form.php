<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of custom_form
 *
 * @author vladislav
 */
include_once 'string_utils.php';

interface Renderable {

    public function render();
}

abstract class Container implements Renderable {

    private $content;

    function __construct($content) {
        $this->content = $content;
    }

    function getContentString() {
        if ($this->content instanceof Renderable) {
            return $this->content->render();
        }
        if (is_array($this->content) 
                or ($this->content instanceof Traversable)){
            
            $buffer = '';
            foreach ($this->content as $value) {
                if ($value instanceof Renderable) {
                    $buffer = $buffer . $value->render();
                }
                else {
                    $buffer = $buffer . $value;
                }
            }
            return $buffer;
        }
        return $this->content;
    }

}

class CustomWrapper extends Container {

    private $type;
    private $class;

    function __construct($content, $type, $class) {
        $this->type = $type;
        $this->class = $class;
        parent::__construct($content);
    }

    public function render() {
        return '<' . $this->type . ' '
                . toAssignment("class", $this->class, '', '"') . '>'
                . ' ' . $this->getContentString() . ''
                . '</' . $this->type . '>';
    }

}

class CustomLabel extends Container {

    private $for;
    private $class;

    function __construct($content, $for, $class = 'form-label') {
        $this->for = $for;
        $this->class = $class;
        parent::__construct($content);
    }

    public function render() {
        return '<label '
                . toAssignment("for", $this->for, '', '"') . ' '
                . toAssignment("class", $this->class, '', '"') . '>'
                . ' ' . $this->getContentString() . ''
                . '</label>';
    }

}

abstract class CustomField implements Renderable {

    protected $name;
    protected $label;
    protected $verbose_name_nom;
    protected $verbose_name_acc;
    protected $required;
    protected $value;
    protected $class;

    function __construct($name,
            $label,
            $verbose_name_nom,
            $verbose_name_acc,
            $required = false,
            $value = '',
            $class = 'form-control') {
        $this->name = $name;
        $this->verbose_name_nom = $verbose_name_nom;
        $this->verbose_name_acc = $verbose_name_acc;
        $this->required = $required;
        $this->value = $value;
        $this->class = $class;

        if ($label == 'auto') {
            $label_name = $verbose_name_nom;
            if ($required){
                $label_name = $label_name . '*';
            }
            $this->label = new CustomLabel($label_name, $name);
        }
        else{
            $this->label = $label;
        }
    }

    function required() {
        if ($this->required) {
            return 'required';
        }
        return '';
    }
    
    function getName() {
        return $this->name;
    }

    abstract function getType();

    abstract function getAdditionalParams();

    abstract function validate($value);

    public function render() {
        $label = '';
        if ($this->label) {
            $label = $this->label->render();
        }
        return $label . '<input '
                . toAssignment("type", $this->getType(), '', '"') . ' '
                . toAssignment("class", $this->class, '', '"') . ' '
                . toAssignment("id", $this->name, '', '"') . ' '
                . toAssignment("name", $this->name, '', '"') . ' '
                . toAssignment("verbose_name_nom", $this->verbose_name_nom,
                        '', '"') . ' '
                . toAssignment("verbose_name_acc", $this->verbose_name_acc,
                        '', '"') . ' '
                . $this->required() . ' '
                . toAssignment("value", $this->value, '', '"') . ' '
                . $this->getAdditionalParams() . '>'
                . '</input>';
    }

}

class CustomTextField extends CustomField {

    private $regexp;
    private $maxlength;
    private $hint;
    private $custom_validation;
    
    function __construct($name,
            $label,
            $verbose_name_nom,
            $verbose_name_acc,
            $required = false,
            $value = '',
            $regexp = '',
            $hint = '',
            $maxlength = -1,
            $custom_validation = null,
            $class = 'form-control') {
        parent::__construct($name, $label,
                $verbose_name_nom, $verbose_name_acc,
                $required, $value, $class);
        $this->regexp = $regexp;
        $this->maxlength = $maxlength;
        $this->hint = $hint;
        $this->custom_validation = $custom_validation;
    }

    private function phpRegexp() {
        return '/' . $this->regexp . '/u';
    }

    private function jsRegexp() {
        return $this->regexp;
    }

    public function getAdditionalParams() {
        $params = '';
        if ($this->regexp){
            $params = toAssignment("regexp", $this->jsRegexp(), '', '"');
        }
        appendIfNotEmpty($params, ' ', toAssignment("maxlength",
                                        $this->maxlength, '', '"'));
        appendIfNotEmpty($params, ' ', toAssignment("hint",
                                        $this->hint, '', '"'));
        return $params;
        
    }

    public function getType() {
        return 'text';
    }

    public function validate($value) {
        if ($this->required && !($value)){
            return false;
        }
        setlocale(LC_ALL, "ru_RU.UTF-8");
        if ($this->regexp && !(preg_match($this->phpRegexp(), $value))){
            return false;
        }
        if ($this->custom_validation){
            if (!$this->custom_validation($value)){
                return false;
            }
        }
        return true;
    }

}

class CustomDateField extends CustomField {

    private $before;
    private $after;
    private $hint;
    private $custom_validation;
    
    function __construct($name,
            $label,
            $verbose_name_nom,
            $verbose_name_acc,
            $required = false,
            $value = '',
            $before = '',
            $after = '',
            $hint = '',
            $custom_validation = null,
            $class = 'form-control') {
        parent::__construct($name, $label,
                $verbose_name_nom, $verbose_name_acc, 
                $required, $value, $class);
        self::assignDate($before, $this->before);
        self::assignDate($after, $this->after);
        $this->hint = $hint;
        $this->custom_validation = $custom_validation;
    }
    
    private static function assignDate($date, &$var){
        if ($date){
            if (str_starts_with($date, "P")){
                $di = new DateInterval($date);
                $var = (new DateTime())->add($di);
            }
            elseif (str_starts_with($date, "-")){
                $new_date = trim($date, '-');
                $di = new DateInterval($new_date);
                $di->invert = 1; 
                $var = (new DateTime())->add($di);
            }
            else{
                $var = new DateTime($date);
            }
        }
    }

    public function getAdditionalParams() {
        $params = '';
        if ($this->before){
            appendIfNotEmpty($params, ' ', toAssignment("before",
                                        $this->before->format("Y-m-d"),
                                        '', '"'));
        }
        if ($this->after){
            appendIfNotEmpty($params, ' ', toAssignment("after",
                                        $this->after->format("Y-m-d"),
                                        '', '"'));
        }
        appendIfNotEmpty($params, ' ', toAssignment("hint",
                                        $this->hint, '', '"'));
        return $params;
        
    }

    public function getType() {
        return 'date';
    }

    public function validate($value) {
        if ($this->required && !($value)){
            return false;
        }
        if (!$this->required && !($value)){
            return true;
        }
        $date_value = DateTime::createFromFormat("Y-m-d", $value);
        if ($date_value){
            if ($this->before && $date_value > $this->before){
                
                return false;
            }
            if ($this->after && $date_value < $this->after){
                return false;
            }            
        }
        else {
            return false;
        }
        if ($this->custom_validation){
            if (!$this->custom_validation($value)){
                return false;
            }
        }
        return true;
    }

}

class CustomEmailField extends CustomField {

    private $maxlength;
    private $hint;
    private $custom_validation;
    
    function __construct($name,
            $label,
            $verbose_name_nom,
            $verbose_name_acc,
            $required = false,
            $value = '',
            $hint = "Введите корректный адрес электронной почты",
            $custom_validation = null,
            $class = 'form-control') {
        parent::__construct($name, $label,
                $verbose_name_nom, $verbose_name_acc,
                $required, $value, $class);
        $this->maxlength = 255;
        $this->hint = $hint;
        $this->custom_validation = $custom_validation;
    }

    public function getAdditionalParams() {
        $params = '';

        appendIfNotEmpty($params, ' ', toAssignment("maxlength",
                                        $this->maxlength, '', '"'));
        appendIfNotEmpty($params, ' ', toAssignment("hint",
                                        $this->hint, '', '"'));
        return $params;
        
    }

    public function getType() {
        return 'email';
    }

    public function validate($value) {
        if ($this->required && !($value)){
            return false;
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if ($this->custom_validation){
            if (!$this->custom_validation($value)){
                return false;
            }
        }
        return true;
    }

}

class CustomCheckField extends CustomField {

    private $hint;
    private $custom_validation;
    
    function __construct($name,
            $label,
            $verbose_name_nom,
            $verbose_name_acc = '',
            $required = false,
            $value = 'true',
            $hint = "Введите корректный адрес электронной почты",
            $custom_validation = null,
            $class = 'form-check-input') {
        parent::__construct($name, $label,
                $verbose_name_nom, $verbose_name_acc,
                $required, $value, $class);
        $this->hint = $hint;
        $this->custom_validation = $custom_validation;
    }

    public function getAdditionalParams() {
        $params = '';
        appendIfNotEmpty($params, ' ', toAssignment("hint",
                                        $this->hint, '', '"'));
        return $params;
        
    }

    public function getType() {
        return 'checkbox';
    }

    public function validate($value) {
        if ($this->required && !($value)){
            return false;
        }
        if ($this->custom_validation){
            if (!$this->custom_validation($value)){
                return false;
            }
        }
        return true;
    }
    
    public function render() {
        $label = '';
        if ($this->label) {
            $label = $this->label->render();
        }
        return '<input '
                . toAssignment("type", $this->getType(), '', '"') . ' '
                . toAssignment("class", $this->class, '', '"') . ' '
                . toAssignment("id", $this->name, '', '"') . ' '
                . toAssignment("name", $this->name, '', '"') . ' '
                . toAssignment("verbose_name_nom", $this->verbose_name_nom,
                        '', '"') . ' '
                . toAssignment("verbose_name_acc", $this->verbose_name_acc,
                        '', '"') . ' '
                . $this->required() . ' '
                . toAssignment("value", $this->value, '', '"') . ' '
                . $this->getAdditionalParams() . '>'
                . '</input>' . $label;
    }

}

class CustomSelectField extends CustomField {
    
    private $hint;
    private $custom_validation;
    
    function __construct(
            $name,
            $label,
            $verbose_name_nom,
            $hint = "Выберите один из вариантов",
            $custom_validation = null,
            $class = 'form-control') {
        parent::__construct($name, $label,
                $verbose_name_nom, '',
                required: true, value: '', class: $class);
        $this->hint = $hint;
        $this->custom_validation = $custom_validation;
    }
    
    public function getAdditionalParams() {
        return '';
    }

    public function getType() {
        return 'select';
    }

    public function validate($value) {
        if ($this->required && !($value)){
            return false;
        }
        if ($this->custom_validation){
            if (!$this->custom_validation($value)){
                return false;
            }
        }
        return true;
    }
    
    public function render() {
        $label = '';
        if ($this->label) {
            $label = $this->label->render();
        }
        
        $html = $label . '<select ';
        $html = $html . toAssignment("class", $this->class, '', '"') . ' ';
        $html = $html . toAssignment("id", $this->name, '', '"') . ' ';
        $html = $html . $this->required() . ' ';
        $html = $html . toAssignment("name", $this->name, '', '"') . ' ';
        $html = $html . $this->getAdditionalParams() . '>';
        $html = $html . '</select>';
        
        return $html;
    }
    
    

}

abstract class SubmitPlacement{
    const before = -1;
    const after = 1;
}

class CustomSubmit extends CustomField {
    
    private $placement;
            
    function __construct($name,
            $verbose_name_nom,
            $verbose_name_acc,
            $placement,
            $class = 'btn btn-primary') {
        parent::__construct($name, '', $verbose_name_nom, $verbose_name_acc,
                false, $verbose_name_nom, $class);
        $this->placement = $placement;
    }
    
    public function getPlacement() {
        return $this->placement;
    }
    
    public function getAdditionalParams() {
        return '';
    }

    public function getType() {
        return 'submit';
    }

    public function validate($value) {
        return true;
    }

}

class CustomPasswordField extends CustomField {
    private $maxlength;
    private $hint;
    private $custom_validation;
    
    function __construct($name,
            $label,
            $verbose_name_nom,
            $verbose_name_acc,
            $required = false,
            $value = '',
            $hint = "Введите корректный пароль",
            $custom_validation = null,
            $class = 'form-control') {
        parent::__construct($name, $label,
                $verbose_name_nom, $verbose_name_acc,
                $required, $value, $class);
        $this->maxlength = 255;
        $this->hint = $hint;
        $this->custom_validation = $custom_validation;
    }

    public function getAdditionalParams() {
        $params = '';

        appendIfNotEmpty($params, ' ', toAssignment("maxlength",
                                        $this->maxlength, '', '"'));
        appendIfNotEmpty($params, ' ', toAssignment("hint",
                                        $this->hint, '', '"'));
        return $params;
        
    }

    public function getType() {
        return 'password';
    }

    public function validate($value) {
        if ($this->required && !($value)){
            return false;
        }
        if ($this->custom_validation){
            if (!$this->custom_validation($value)){
                return false;
            }
        }
        return true;
    }
}

class CustomForm implements Renderable{
    
    private $name;
    private $fields;
    private $submit;
    private $wrapperType;
    private $wrapperClass;
    private $method;
    private $action;
    private $extraSubmits;
    
    function __construct($name, $fields, 
            $submitClass="btn btn-primary", $submitText="Подтвердить",
            $wrapperType='div', $wrapperClass='mb-3', 
            $method='POST', $action="#", $extraSubmits=[]) {
        $this->name = $name;
        $this->fields = array();
        foreach ($fields as $field) {
            $this->fields += [$field->getName() => $field];
        }
        
        $this->submit = new CustomSubmit(
                name: 'submit',
                verbose_name_nom: $submitText,
                verbose_name_acc: $submitText,
                placement: null,
                class: $submitClass
        );
        $this->wrapperType = $wrapperType;
        $this->wrapperClass = $wrapperClass;
        $this->method = $method;
        $this->action = $action;
        $this->extraSubmits = $extraSubmits;
    }


    public function render() {
        $form = '<form id="' . $this->name . '" method="' . $this->method
                . '" action="' . $this->action . '">';
        $form = $form . '<div class="alert alert-danger" id="dangerAlertBlock" '
                . 'role="alert" style="display: none;"></div>';
        $form = $form . '<div class="alert alert-success" id="successAlertBlock" '
                . 'role="alert" style="display: none;"></div>';
 
        foreach ($this->fields as $field) {
            $wrapperClass = $this->wrapperClass;
            if ($field->getType() == 'checkbox'){
                $wrapperClass = 'form-check';
            }
            $form = $form . (new CustomWrapper(
                    type: $this->wrapperType,
                    class: $wrapperClass,
                    content: $field
                ))->render();
        }
        
        $submitsRow = [];
        
        foreach ($this->extraSubmits as $extraSubmit) {
            if ($extraSubmit->getPlacement() == SubmitPlacement::before){
                $wrappedSubmit = new CustomWrapper($extraSubmit, 'div', 'm-2');
                array_push($submitsRow, $wrappedSubmit);
            }
        }
        $wrappedSubmit = new CustomWrapper($this->submit, 'div', 'm-2');
        array_push($submitsRow, $wrappedSubmit);
        foreach ($this->extraSubmits as $extraSubmit) {
            if ($extraSubmit->getPlacement() == SubmitPlacement::after){
                $wrappedSubmit = new CustomWrapper($extraSubmit, 'div', 'm-2');
                array_push($submitsRow, $wrappedSubmit);
            }
        }
        
        $form = $form 
                . (new CustomWrapper($submitsRow, 'div', 'd-flex form-row'))->render();
        
        $form = $form . "</form>";
        return $form;
    }
    
    public function getSaintizedField($field) {
        $methods = [
                'text' => FILTER_SANITIZE_STRING,
                'date' => FILTER_SANITIZE_STRING,
                'email' => FILTER_SANITIZE_EMAIL,
                'select' => FILTER_SANITIZE_STRING,
                'checkbox' => FILTER_SANITIZE_STRING,
                'submit' => FILTER_SANITIZE_STRING,
            
        ];
        return filter_input(INPUT_POST, $field->getName(), 
                $methods[$field->getType()]);
    }


    public function validate() {
        foreach ($this->fields as $field) {
            $field_value = $this->getSaintizedField($field);
            if (!$field->validate($field_value)){
                return false;
            }
        }
        return true;
        
    }
    
    public function validate_input($input) {
        foreach ($input as $field_name => $field) {
            if (!$this->fields[$field_name]->validate($field)){
                return false;
            }
        }
        return true;
        
    }
    
    public function getFormData() {
        $data = array();
        foreach ($this->fields as $field_name => $field) {
            $field_value = $this->getSaintizedField($field);
            $data += [$field_name => $field_value];
        }
        return $data;
    }

}
