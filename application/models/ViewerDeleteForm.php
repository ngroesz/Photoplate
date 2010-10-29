<?php
class ViewerDeleteForm extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('delete');
		
		$album_id = new Zend_Form_Element_Hidden('album_id');
		$template_id = new Zend_Form_Element_Hidden('template_id');
		
		$confirm = new Zend_Form_Element_Submit('confirm');
		$cancel = new Zend_Form_Element_Submit('cancel');
		
		$this->addElements(array($album_id, $template_id, $confirm, $cancel));
	}
}
