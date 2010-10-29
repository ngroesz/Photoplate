<?php
class ImageDeleteForm extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('delete');
		
		$id = new Zend_Form_Element_Hidden('id');
		
		$confirm = new Zend_Form_Element_Submit('confirm');
		$cancel = new Zend_Form_Element_Submit('cancel');
		
		$this->addElements(array($id, $confirm, $cancel));
	}
}
