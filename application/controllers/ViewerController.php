<?php

require 'ViewerBuilder.php';

class ViewerController extends PPController
{

	function init()
	{
		$this->logger = Zend_Registry::get('logger');
		$this->db = Zend_Registry::get('db');
		$this->viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	}
	
	function indexAction()
	{
		$this->_forward('list');
	}
	
	function listAction()
	{
		$this->view->album_id = $this->_request->getParam('album_id', 0);
		$this->view->title = 'Viewer List';
		$this->view->all_templates_added = false;
		
		$viewers = new Viewers();		
		$select = $viewers->select()->setIntegrityCheck(false);
		$select->from('viewers')
			   ->join('templates', 'templates.id = viewers.template_id', array('name AS template_name', 'width AS template_width', 'height AS template_height', 'data AS template_data'))
			   ->where('viewers.album_id = ?', $this->view->album_id);
		
		$this->view->viewers = $viewers->fetchAll($select);
	
		$templates = new Templates();
		$template_rows = $templates->fetchAll();
		
		if (count($template_rows) == count($this->view->viewers)) {
			$this->view->all_templates_added = true;
		}
		
		$response = $this->getResponse();
		$this->logger->info("response: $response");
		
		$this->getResponse()->prepend('blah', 'had');
	}
	
	function createAction()
	{
		$this->view->title = 'Add Viewer';
		
		$album_id = $this->_request->getParam('album_id', 0);
		
		$form = new ViewerForm(array('album_id' => $album_id));
		$form->submit->setLabel('Add');
		$album_id_element = new PP_Form_Element_Hidden('album_id');
		$album_id_element->setValue($album_id);
		$form->addElement($album_id_element);
		
		$this->view->form = $form;
		
		if ($this->_request->isPost()) {
			$form_data = $this->_request->getPost();
			if ($form->isValid($form_data)) {
				$viewers = new Viewers();
				$viewer = $viewers->createRow();
				
				$viewer->album_id = $form->getValue('album_id');
				$viewer->template_id = $form->getValue('template_id');
				$viewer->save();
				
				$this->_redirect('/viewer/list/album_id/'.$viewer->album_id);
			} else {
				$form->populate($form_data);
			}
		}
		$this->_helper->viewRenderer('form');
	}
	
	function viewAction()
	{
		$this->view->title = 'Viewer Details';
		
		$album_id = $this->_request->getParam('album_id', 0);
		$template_id = $this->_request->getParam('template_id', 0);
		if ($album_id > 0 && $template_id > 0) {
			$viewers = new Viewers();
			$select = $viewers->select()->setIntegrityCheck(false);
			$select->from('viewers')
				   ->join('templates', 'templates.id = viewers.template_id', 'name AS template_name')
				   ->where('viewers.album_id = ?', $album_id)
				   ->where('viewers.template_id = ?', $template_id);
				   
			$viewer = $viewers->fetchRow($select);
			$this->view->viewer = $viewer;
		}
	}
	
	function deleteAction()
	{
		$album_id = $this->_request->getParam('album_id', 0);
		$template_id = $this->_request->getParam('template_id', 0);
		$viewers = new Viewers();
		
		
		if ($this->_request->isPost()) {
			$del = $this->_request->getPost('confirm');
			$return_to = $this->_request->getParam('return_to', 0);
			
			if (strcmp($del, 'Confirm') == 0 && $album_id > 0 && $template_id > 0) {
				$viewers->delete('album_id = '.$album_id.' AND template_id = '.$template_id);
			} 
			
			$this->_redirect('/album/view/id/'.$album_id.'/view/viewers');
		} elseif ($album_id > 0 && $template_id > 0) {
			$this->view->title = 'Confirm Deletion';
			
			$form = new ViewerDeleteForm();
			
			$in_return_to = $this->_request->getParam('return_to');
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
	
	function buildAction()
	{
		$template_id = $this->_request->getParam('template_id');
		$album_id = $this->_request->getParam('album_id');
		
		$builder = new ViewerBuilder();
		
		try {
			$builder->BuildViewer($template_id, $album_id);
		} catch (Exception $error) {
			$this->_request->setParam('error', $error);
			$this->_forward('error');
			return;
		}	
		$this->_forward('list');
	}
	
	function errorAction()
	{
		$this->view->title = 'Viewer Build Error';
		$this->view->album_id = $this->_request->getParam('album_id');
		$this->view->error = $this->_request->getParam('error');
	}
}
