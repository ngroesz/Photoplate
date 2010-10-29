<?php
class ResourceForm extends PP_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->setAttrib('enctype', 'multipart/form-data');
		
		$template_id = new PP_Form_Element_Hidden('template_id');
		$this->addElement($template_id);
		
		$resource_class = new Resources();
		
		$select = $resource_class->select();
		$select->where('template_id = ?', $options['template_id']);
		$resources = $resource_class->fetchAll($select);
		
		$view = $this->getView();
		foreach ($resources as $resource) {
			$file = new PP_Form_Element_Text('resource_'.$resource->id, array('disableLoadDefaultDecorators' => true));
			$file->class = 'formfile';
	
			$file->setLabel($resource->name);
			$file->addDecorator('ViewHelper')
                 ->addDecorator('Errors')
				 ->addDecorator('Label')
				 ->addDecorators(array(array('ViewHelper', array('helper' => 'formFile'))));

			 if ($resource->data) {
				 $file->addDecorator('HtmlTag', array('tag' => 'p', 'class' => 'file_present'));
			 } else {
				 $file->addDecorator('HtmlTag', array('tag' => 'p', 'class' => 'file_missing'));
			 }
			
			$this->addElement($file);
		}
		
		$submit = new PP_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$this->addElement($submit);
	}
}
			
