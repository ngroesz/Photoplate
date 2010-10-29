<?php
class TemplateForm extends PP_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setAttrib('enctype', 'multipart/form-data');

		$id = new PP_Form_Element_Hidden('id');
		
		$name = new PP_Form_Element_Text('name');
		$name->setLabel('Name:')
			 ->setRequired(true)
			 ->addValidator('NotEmpty');
		$name_empty = $name->getValidator('NotEmpty');
		$name_empty->setMessages(array(
										Zend_Validate_NotEmpty::IS_EMPTY => 'A name is required.')
								);

		$width = new PP_Form_Element_Text('width');
		$width->setLabel('Viewer Width:')
			  ->setAttrib('size', '4');
			  
		$height = new PP_Form_Element_Text('height');
		$height->setLabel('Viewer Height:')
			   ->setAttrib('size', '4');
			   
		$file = new PP_Form_Element_Text('file');
		$file->class = 'formfile';
		$file->setLabel('File:')
			 ->addDecorators(array(
				 array('ViewHelper', array('helper' => 'formFile')),
             ));
		
		$submit = new PP_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($id, $name, $width, $height, $file, $submit));
	}
}
