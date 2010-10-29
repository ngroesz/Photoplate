<?php
class PP_Form_Element_Textarea extends Zend_Form_Element_Textarea
{
    public function init()
    {
        $this->addFilter('StringTrim')
			 ->addFilter('StripTags')
             ->setAttrib('maxLength', 255)
             ->setAttrib('class', 'textarea');
		$this->rows = 4;
		$this->cols = 40;
    }
	
	public function loadDefaultDecorators()
    {
        $this->addDecorator('ViewHelper')
             ->addDecorator('Errors')
             ->addDecorator('Label', array('tag' => 'label'))
             ->addDecorator('HtmlTag', array('tag' => 'p'));
    }
}
