<?php
class ImageForm extends PP_Form
{
	public function __construct($options = null)
	{
		/* Zend/Form.php seems to indicate that $options could be an array or
		or instance of Zend_Config. Here we assume that $options is an array, 
		perhaps to bad effect? */
		$options['name'] = 'image_form';
		
		if (!$options['legend']) {
			$options['legend'] = 'Add Image';
		}
		
		parent::__construct($options);
			
		$config = Zend_Registry::get('config');

		$id = new PP_Form_Element_Hidden('id');
		
		$album_id = new PP_Form_Element_Hidden('album_id');

		$this->setAttrib('enctype', 'multipart/form-data');
		
		$title = new PP_Form_Element_Text('title');
		$title->setLabel('Title:')
			  ->setRequired(true) 
			  ->addValidator('NotEmpty');
		$title_empty = $title->getValidator('NotEmpty');
		$title_empty->setMessages(array(
										Zend_Validate_NotEmpty::IS_EMPTY => 'A title is required.')
								);


	
		$file = new PP_Form_Element_Text('image');
		$file->class = 'formfile';
		$file->setLabel('File:')
			 ->addDecorators(array(
				 array('ViewHelper', array('helper' => 'formFile')),
             ));
	
		$description = new PP_Form_Element_Textarea('description');
		$description->setLabel('Description');
			 
		$generate_thumb = new PP_Form_Element_Checkbox('thumb_auto_generate');	 
		$generate_thumb->setLabel('Generate Thumb:');
		$generate_thumb->setAttrib('onclick', 'toggleThumbGenerate()');

		$thumb_file = new PP_Form_Element_Text('thumb_file');
		$thumb_file->class = 'formfile';
		$thumb_file->setLabel('Thumbnail File:')
			  ->addDecorators(array(
				  array('ViewHelper', array('helper' => 'formFile')),
				  array('HtmlTag', array('tag' => 'p', 'id' => 'thumb')),
			  ));
		
		$default_thumb_width = $config->image->get('thumb_default_width');
		$thumb_width = new PP_Form_Element_Text('thumb_width');
		$thumb_width->setLabel('Thumbnail Width')
					->addDecorators(array(
						array('HtmlTag', array('tag' => 'p', 'id' => 'thumb_width', 'style' => 'display: none'))
					))
					->setValue($default_thumb_width)
					->setAttrib('size', '3');
					
		$default_thumb_height = $config->image->get('thumb_default_height');
		$thumb_height = new PP_Form_Element_Text('thumb_height');
		$thumb_height->setLabel('Thumbnail Height')
					->addDecorators(array(
						array('HtmlTag', array('tag' => 'p', 'id' => 'thumb_height', 'style' => 'display: none'))
					))
					->setValue($default_thumb_height)
					->setAttrib('size', '3');
					
		$submit = new PP_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($id, $album_id, $title, $file, $description, $generate_thumb, $thumb_file, $thumb_width, $thumb_height, $submit));
	}
}
		
