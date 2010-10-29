<?php
class AlbumForm extends PP_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$id = new PP_Form_Element_Hidden('id');
		
		$title = new PP_Form_Element_Text('title');
		$title->setLabel('Title:')
			  ->setRequired(true)
			  ->addValidator('NotEmpty');
		$title_empty = $title->getValidator('NotEmpty');
		$title_empty->setMessages(array(
										Zend_Validate_NotEmpty::IS_EMPTY => 'A title is required.')
								);

		$description = new PP_Form_Element_Textarea('description');
		$description->setLabel('Description:');
		$description->rows = 2;
		$description->cols = 40;
		
		$submit = new PP_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($id, $title, $description, $submit));
	}
}
		
