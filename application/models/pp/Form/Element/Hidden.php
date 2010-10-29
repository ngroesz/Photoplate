<?php
class PP_Form_Element_Hidden extends Zend_Form_Element_Hidden
{
	public function loadDefaultDecorators()
    {
		$this->addDecorator('ViewHelper')
             ->addDecorator('HtmlTag', array('tag' => 'p'));
    }
}
