<?php

class ResourceController extends PPController
{
	function init()
	{
		$this->logger = Zend_Registry::get('logger');
		$this->viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	}
	
	function listAction()
	{
		$this->view->title = 'Template Resource List';
		$template_id = $this->_request->getParam('template_id');
		
		$this->view->template_id = $template_id;
		
		$resource_class = new Resources();
		$resources = $resource_class->fetchAll($resource_class->select());
		$this->view->form = new ResourceForm(array(
								'template_id' => $template_id
								));
		
		$this->view->form->submit->setLabel('Upload');
		
		if ($this->_request->isPost()) {
			$form_data = $this->_request->getPost();
			if ($this->view->form->isValid($form_data)) {
				$resource_class = new Resources();
				
				// look at each template resource to see if it was uploaded
				$resources = $resource_class->fetchAll($resource_class->select()->where('template_id = ?', $template_id));
				foreach ($resources as $resource) {
					$field = 'resource_'.$resource->id;
					if (is_uploaded_file($_FILES[$field]['tmp_name'])) {
						$resource->data = join('', file($_FILES[$field]['tmp_name']));
						$resource->file_type = $_FILES[$field]['type'];
						$resource->save();
					}
				}
				
				$this->_redirect('/resource/list/template_id/'.$template_id);
			} else {
				$this->view->form->populate($form_data);
			}
		}
		$this->_helper->viewRenderer('form');
	}
	/*
	function listAction()
	{
		$this->view->title = 'Template Resource List';
		$template_id = $this->_request->getParam('template_id');
		
		$this->view->template_id = $template_id;
		
		$resource_class = new Resources();
		$this->view->form = new Zend_Form;
		$this->view->form->addElementPrefixPath('PP_Decorator', 'PP/Decorator/', 'decorator');
		$this->view->form->setAttrib('enctype', 'multipart/form-data');
		$id_element = new PP_Form_Element_Hidden('template_id');
		$this->view->form->addElement($id_element);
		
		if ($this->_request->isPost()) {
			$form_data = $this->_request->getPost();
			if ($this->view->form->isValid($form_data)) {				
				// look at each template resource to see if it was uploaded
				$resources = $resource_class->fetchAll($resource_class->select()->where('template_id = ?', $template_id));
				foreach ($resources as $resource) {
					$field = 'resource_'.$resource->id;
					if (is_uploaded_file($_FILES[$field]['tmp_name'])) {
						$resource->data = join('', file($_FILES[$field]['tmp_name']));
						$resource->file_type = $_FILES[$field]['type'];
						$resource->save();
					}
				}
				
				$this->_redirect('/resource/list/template_id/'.$template_id);
			} else {
				$this->view->form->populate($form_data);
			}
		} 
		$resources = $resource_class->fetchAll($resource_class->select());
		foreach ($resources as $resource) {
			$file = new PP_Form_Element_Text('resource_'.$resource->id, array('disableLoadDefaultDecorators' => true));
			$file->class = 'formfile';

			$file->clearDecorators()
				 ->addDecorator('ViewHelper')
                 ->addDecorator('Errors')
				 ->addDecorators(array(array('ViewHelper', array('helper' => 'formFile'))));
			

			if ($resource->data) {
				$url = $this->view->url(array('controller' => 'resource', 'action' => 'show.resource', 'id' => $resource->id), null, true);
				$href = "javascript:openUrl('$url', '800', '600')";
				$file->addPrefixPath('PP_Form_Decorator', 'PP/Form/Decorator/', 'decorator');				
				$file->addDecorator('HtmlTag', array('tag' => 'a', 'href' => $href))
					  ->addDecorator('Label', array('href' => $href));

				//$file->addDecorator('HtmlTag', array('tag' => 'a', 'href' => $href));
			}else {
				$file->addDecorator('HtmlTag', array('tag' => 'p', 'class' => 'file_missing'))
					 ->addDecorator('Label');
			}
			//$file->addDecorators(array(array('ViewHelper', array('helper' => 'formFile'))));
			
			$file->setLabel($resource->name);
			
			$this->view->form->addElement($file);
		}	
		$submit = new PP_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Upload');
		$this->view->form->addElement($submit);
		
		//$this->_helper->viewRenderer('form');
	}*/
	
	function showResourceAction()
	{
		$id = (int)$this->_request->getParam('id');
		
		$resource_class = new Resources();
		$resource = $resource_class->fetchRow($resource_class->select()->where('id='.$id));
		
		$this->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$this->getResponse()
			 ->setHeader('Content-Type', $resource->file_type)
			 ->appendBody($resource->file_type);
	}
}
