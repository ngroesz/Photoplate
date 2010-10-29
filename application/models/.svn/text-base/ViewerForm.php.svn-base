<?php
class ViewerForm extends PP_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
			
		$album_id = new PP_Form_Element_Hidden('album_id');
				
		$template_id = new PP_Form_Element_Select('template_id');
		$template_id->setLabel('Template:');
		$templates = new Templates();
		$templates = $templates->fetchAll();
		
		$viewers = new Viewers();
		foreach ($templates as $template) {
			$viewer = $viewers->fetchRow($viewers->select()->where('album_id = ? AND template_id = ?', $options['album_id'], $template->id));
			
			$select = $viewers->select()
							  ->where('album_id = ?', $options['album_id'])
							  ->where('template_id = ?', $template->id);
			
			$viewer = $viewers->fetchRow($select);
			// only present the template as an option if it is not yet added
			if (!$viewer) {
				$template_id->addMultiOption($template->id, $template->name);
			}
		}
		
		$submit = new PP_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($album_id,$template_id, $submit));
	}
}
