<?php
class PP_Form_Element_Submit extends Zend_Form_Element_Submit
{
	public function loadDefaultDecorators()
    {
		$this->addDecorator('ViewHelper')
             ->addDecorator('HtmlTag', array('tag' => 'p', 'class' => 'submit'));
    }
}
