<?php
class PP_Form_Element_Text extends Zend_Form_Element_Text
{
    public function init()
    {
        $this->addFilter('StringTrim')
			 ->addFilter('StripTags')
             ->setAttrib('size', 25)
             ->setAttrib('maxLength', 255)
             ->setAttrib('class', 'text');
    }
	
	public function loadDefaultDecorators()
    {
		$this->addDecorator('ViewHelper')
             ->addDecorator('Errors')
             ->addDecorator('Label')
             ->addDecorator('HtmlTag', array('tag' => 'p'));
    }
}
