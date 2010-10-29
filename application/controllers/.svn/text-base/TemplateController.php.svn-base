<?php

class TemplateController extends PPController
{
	/**
	 * Initialize controller
	 *
	 * @return void
	 */
	function init()
	{
		$this->db = Zend_Registry::get('db');
		$this->logger = Zend_Registry::get('logger');
		$this->config = Zend_Registry::get('config');
		$this->viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	}
	
	function indexAction()
	{
		$this->_forward('list');
	}
	
	function listAction()
	{
		$this->view->title = 'Template List';
		
		$templates = new Templates();
		$this->view->templates = $templates->fetchAll($templates->select()->order('name'));
	}
	
	function createAction()
	{
		$this->view->title = 'Create New Template';
		
		$form = new TemplateForm(array('legend' => $this->view->title));
		$form->submit->setLabel('Create');
		$this->view->form = $form;
		
		if ($this->_request->isPost()) {
			$form_data = $this->_request->getPost();
			if ($form->isValid($form_data)) {
				$templates = new Templates();
				$row = $templates->createRow();
				
				$row->name = $form->getValue('name');
				$row->width = $form->getValue('width');
				$row->height = $form->getValue('height');
				$row->save();
				
				$id = $this->db->lastInsertId();
				$this->saveData($id);
				
				$this->_redirect('/template/list');
			} else {
				$this->view->form->populate($form_data);
			}
		}
		$this->_helper->viewRenderer('form');
	}
	
	function editAction()
	{
		$this->view->title = 'Edit Template';
		
		$form = new TemplateForm(array('legend' => $this->view->title));
		$form->submit->setLabel('Update');
		$this->view->form = $form;
		
		if ($this->_request->isPost()) {
			$form_data = $this->_request->getPost();
			if ($form->isValid($form_data)) {
				$templates = new Templates();
				$id = $form->getValue('id');
				$row = $templates->fetchRow('id='.$id);
				
				$row->name = $form->getValue('name');				
				$row->width = $form->getValue('width');
				$row->height = $form->getValue('height');			
				$row->save();
				
				$this->saveData($id);
				
				$this->_redirect('/template/view/id/'.$id);
			} else {
				$form->populate($form_data);
			}
		} else {
			$id = $this->_request->getParam('id');
			if ($id > 0) {
				$templates = new Templates();
				$row = $templates->fetchRow('id='.$id);
				$form->populate($row->toArray());
			}
		}
		
		$this->_helper->viewRenderer('form');
	}					

	function viewAction()
	{
		$this->view->title = 'Template Details';
		
		$id = $this->_request->getParam('id', 0);
		if ($id > 0) {
			$templates = new Templates();
			$template = $templates->fetchRow('id='.$id);
			$this->view->template = $template;
		}else {
			throw new Exception('No id passed to viewAction');
		}
	}
	
	function showDataAction()
	{
		$id = $this->_request->getParam('id', 0);
		$templates = new Templates;
		$template = $templates->fetchRow('id='.$id);
		
		/*$path = $this->config->path->get('template');
		$file_data = join('', file($path.'/'.$id.'.xml'));*/
		
		$this->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$this->getResponse()
			 ->setHeader('Content-Type', 'text/plain')
			 ->appendBody($template->data);
	}
	
	function deleteAction()
	{
		$id = $this->_request->getParam('id', 0);
		$templates = new Templates();
		
		if ($this->_request->isPost()) {
			$confirm = $this->_request->getPost('confirm');
			$return_to = $this->_request->getParam('return_to', 0);
			
			if (!strcmp($confirm, 'Confirm') && $id > 0) {
				$templates->delete('id='.$id);
				
				// remove all the viewers using this template
				$viewer_class = new Viewers();
				$viewer_class->delete('template_id'.$id);
			} elseif (!strcmp($return_to, 'view')) {
				$this->_redirect('/template/view/id/'.$id);
				return;
			}
			
			$this->_redirect('/template/list');
		} elseif ($id > 0) {
			$this->view->title = 'Confirm Deletion';
			
			$templates = new Templates();
			$template = $templates->fetchRow('id='.$id);
			$this->view->template_name = $template->name;
			
			$form = new TemplateDeleteForm();
			
			$in_return_to = $this->_request->getParam('return_to', 0);
			if ($in_return_to) {
				$return_to = new PP_Form_Element_Hidden('return_to');
				$return_to->setValue($in_return_to);
				$form->addElement($return_to);
			}
			
			$form->confirm->setLabel('Confirm');
			$form->cancel->setLabel('Cancel');
			$this->view->form = $form;
		}
	}
	
	function saveFile($id)
	{
		/* not currently used - could be deleted */
		if (is_uploaded_file($_FILES['file']['tmp_name'])) {					
			$path = $this->config->path->get('template');
			
			$out_file = $path.'/'.$id.'.xml';
			move_uploaded_file($_FILES['file']['tmp_name'], $out_file);
			
			$templates = new Templates();
			$template = $templates->fetchRow('id = '.$id);
			$template->file_uploaded = 1;
			$template->save();
			
			$template_data = join('', file($out_file));
			$this->resourcesToDb($this->findResources($template_data), $id);
		}
	}
	 
	function saveData($id)
	{
		if (is_uploaded_file($_FILES['file']['tmp_name'])) {
			
			$templates = new Templates();
			$template = $templates->fetchRow('id = '.$id);
			$template->data = join('', file($_FILES['file']['tmp_name']));
			$template->save();
			
			$this->resourcesToDb($this->findResources($template->data), $id);
		}
	}
	
	function findResources($template)
	{
		$resources = array();
		
		$this->logger->info("finding resources");
		// get <img> srcs
		preg_match_all('#<img[^>]+src="([^"]+)"[^>]+>#i', $template, $matches);		
		foreach ($matches[1] as $match) {
			$resources[] = $match;
		}
	
		// get stylesheet srcs
		preg_match_all('#<link([^>]*)>#', $template, $links);
		foreach ($links[1] as $link) {
			// we only care about the href if the rel attribute is set to 'stylesheet'
			// there may be a better way (i.e. single expression) to do this
			if (preg_match('#rel="stylesheet"#i', $link)) {
				preg_match('#href="([^"]+)"#', $link, $stylesheet);
				$resources[] = $stylesheet[1];
			}
		}

		// check for valid file names and remove the losers	
		for ($i=0; $i<sizeof($resources); $i++) {
			if(!(preg_match('#^\w+\.\w+$#', $resources[$i]))) {
				array_splice($resources, $i, 1);
			}
		}
		
		return $resources;
	}
	
	function resourcesToDb($resources, $template_id)
	{
		$resource_class = new Resources();

		foreach ($resources as $resource) {
			$select = $resource_class->select()->where('name = ?', $resource)
											   ->where('template_id = ?', $template_id);
			$existing_resource = $resource_class->fetchAll($select);
			if (sizeof($existing_resource) == 0) {
				$row = $resource_class->createRow();
				$row->name = $resource;
				$row->template_id = $template_id;
				$row->save();
			}
		}
	}	
}
