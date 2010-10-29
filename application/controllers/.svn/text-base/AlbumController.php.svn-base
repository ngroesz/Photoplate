<?php

class AlbumController extends PPController
{
	/**
	 * Initialize controller
	 *
	 * @return void
	 */
    function init()
    {
		$this->logger = Zend_Registry::get('logger');
		$this->viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	}
	
	function indexAction()
	{
		$this->_forward('list');
	}
	
    function listAction()
    {
        $this->view->title = "My Albums";
        $albums = new Albums();
        $this->view->albums = $albums->fetchAll();
    }
    
    function createAction()
    {
        $this->view->title = 'Create New Album';
		
		$form = new AlbumForm(array('legend' => $this->view->title));
		$form->submit->setLabel('Create');
		$this->view->form = $form;
		
		if ($this->_request->isPost()) {
			$form_data = $this->_request->getPost();
			if ($form->isValid($form_data)) {
				$albums = new Albums();
				$row = $albums->createRow();
				
				$row->title = $form->getValue('title');
				$row->description = $form->getValue('description');
				
				$row->save();
				
				$this->_redirect('/album/list');
			} else {
				$form->populate($form_data);
			}
		}
		
		$this->_helper->viewRenderer('form');
	}
    
    function editAction()
    {
        $this->view->title = "Edit Album";
    
		$form = new AlbumForm(array('legend' => $this->view->title));
		$form->submit->setLabel('Update');
		$this->view->form = $form;
		
		if ($this->_request->isPost()) {
			$form_data = $this->_request->getPost();
			if ($form->isValid($form_data)) {
				$albums = new Albums();
				$id = (int)$form->getValue('id');
				$row = $albums->fetchRow('id='.$id);
				
				$row->title = $form->getValue('title');
				$row->description = $form->getValue('description');
				$row->save();
				
				$this->_redirect('/album/view/id/'.$id);
			} else {
				$form->populate($form_data);
			}
		} else {
			$id = (int)$this->_request->getParam('id', 0);
			if ($id > 0) {
				$albums = new Albums();
				$album = $albums->fetchRow('id='.$id);
				$form->populate($album->toArray());
			}
		}
		$this->_helper->viewRenderer('form');
	}
    
    function deleteAction()
    {
		$id = $this->_request->getParam('id', 0);
		$albums = new Albums();

		if ($this->_request->isPost()) {
			$del = $this->_request->getPost('confirm');
			$return_to = $this->_request->getParam('return_to', 0);
			
			if (strcmp($del, 'Confirm') == 0 && $id > 0) {
				$albums->delete('id='.$id);
			} elseif (!strcmp($return_to, 'view')) {
				$this->_redirect('/album/view/id/'.$id);
				return;
			}
			
			$this->_redirect('/album/list');
		} elseif ($id > 0) {
				$this->view->title = 'Confirm Deletion';
				
				$album = $albums->fetchRow('id='.$id);
				$this->view->album_title = $album->title;
				$form = new AlbumDeleteForm();
				
				$in_return_to = $this->_request->getParam('return_to', 0);
				if ($in_return_to) {
					$return_to = new PP_Form_Element_Hidden('return_to');
					$return_to->setValue($in_return_to);
					$form->addElement($return_to);
				}
				
				$form->confirm->setLabel('Confirm');
				$form->cancel->setLabel('Cancel');
				$this->view->form = $form;
		} else {
			/* we were given a bad ID */
		}
    }
	
	function viewAction()
	{
		$id = $this->_request->getParam('id', 0);
		
		if ($id > 0) {
			$albums = new Albums();
			$album = $albums->fetchRow('id='.$id);
			$this->view->album = $album;
			$this->view->title = "Viewing Album $album->title";
		
			/*$view = $this->_request->getParam('view', 0);
			$request = clone $this->getRequest();
			// list the viewers of the album
			if (!strcmp($view, 'viewers')) {
				$request->setControllerName('viewer')
					    ->setActionName('list')
						->setParams(array('album_id' => $id));
			} else {
			// by default, list images in the album
				$request->setControllerName('image')
						->setActionName('list')
						->setParams(array('album_id' => $id));
			}
			$this->_helper->actionStack($request);*/
		}else {
			$this->logger->error("No album id passed to viewAction");
		}
	}
}
