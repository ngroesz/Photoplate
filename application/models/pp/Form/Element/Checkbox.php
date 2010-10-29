<?php
class PP_Form_Element_Checkbox extends Zend_Form_Element_Checkbox
{
    public function init()
    {
        $this->setAttrib('class', 'checkbox');
    }
	
	public function loadDefaultDecorators()
    {
		$this->addDecorator('ViewHelper')
             ->addDecorator('Errors')
             ->addDecorator('Label')
             ->addDecorator('HtmlTag', array('tag' => 'p'));
    }
}
