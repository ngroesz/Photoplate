<?php

class ImageController extends PPController
{
	/**
	 * Initialize controller
	 *
	 * @return void
	 */
    function init()
    {
		$this->logger = Zend_Registry::get('logger');
		$this->config = Zend_Registry::get('config');
		$this->viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	}
	
	/**
	 * List images in album
	 *
	 * @return void
	 */
	function listAction()
	{
		$album_id = (int)$this->_request->getParam('album_id', 0);

		$images = new Images();
		$this->view->images = $images->fetchAll('album_id='.$album_id);
		$this->view->album_id = $album_id;
		
		$this->view->imageViewLinks = Array();
		foreach ($this->view->images as $image) {
			$this->view->imageViewLinks[$image->id] = $this->imageViewLink($image->id);
			if (!$this->view->imageViewLinks[$image->id]) {
				$this->view->imageViewLinks[$image->id] = '-';
			}
		}
	}
	
	/**
	 * Create a new image
	 *
	 * @return void
	 */
	function createAction()
	{
		$this->view->title = 'Create New Image';
		
		$form = new ImageForm(array('legend' => 'Create Image'));
		$form->submit->setLabel('Create');
		$this->view->form = $form;
	
		$album_id = (int)$this->_request->getParam('album_id', 0);
		$form->album_id->setValue($album_id);
		
		if ($this->_request->isPost() ) {
			$form_data = $this->_request->getPost();
			if ($form->isValid($form_data)) {
				$images = new Images();
				$row = $images->createRow();
				
				$row->title = $form->getValue('title');
				$row->album_id = $form->getValue('album_id');
				$row->description = $form->getValue('description');
				$row->created_at = $this->datetime();
				$row->updated_at = $this->datetime();
								
				$select = $images->select();
				$select->order('viewer_order DESC')
					   ->where('album_id='.$row->album_id);
				
			    $statement = $select->__toString();
			    $this->logger->info("statement: $statement");	   
				$highest_image = $images->fetchRow($select);
						
				$row->viewer_order = $highest_image->viewer_order + 1;
				$this->logger->info("highest: $highest_image->viewer_order new: $row->viewer_order");
				$row->save();

				$db = Zend_Registry::get('db');
				$id = $db->lastInsertId();
				$this->saveImage($id, 'image', 'raw');
				$this->saveImage($id, 'thumb_file', 'thumb');

				$this->_redirect("/image/view/id/$id");
			} else {
				$form->populate($form_data);
			}
		}
		$this->_helper->viewRenderer('form');
	}
	
	/** 
	 * Edit image properties
	 *
	 * @return void
	 */
	function editAction()
    {
        $this->view->title = "Edit Image";
    
		$form = new ImageForm(array('legend' => 'Edit Image'));
		$form->submit->setLabel('Update');
		$this->view->form = $form;
		
		if ($this->_request->isPost()) {
			$form_data = $this->_request->getPost();
			if ($form->isValid($form_data)) {
				$images = new Images();
				$id = (int)$form->getValue('id');
				$row = $images->fetchRow('id='.$id);
				
				$row->title = $form->getValue('title');
				$row->description = $form->getValue('description');
				$row->album_id = $form->getValue('album_id');
				$row->updated_at = $this->datetime();
				$row->save();

				$this->saveImage($id, 'image', 'raw');
				$this->saveImage($id, 'thumb_file', 'thumb');
				
				$this->_redirect("/image/view/id/$id");
			} else {
				$form->populate($form_data);
			}
		} else {
			$id = (int)$this->_request->getParam('id', 0);
			if ($id > 0) {
				$images = new Images();
				$image = $images->fetchRow('id='.$id);
				$form->populate($image->toArray());
			}
		}
		$this->_helper->viewRenderer('form');
	}
    
	function viewAction()
	{
			$id = (int)$this->_request->getParam('id', 0);
			$images = new Images();
			
			$this->view->image = $images->fetchRow('id='.$id);
			$this->view->title = 'Image Details';
	
			$this->view->imageViewLink = $this->imageViewLink($this->view->image->id);
	}		
			
	/** 
	 * Confirm if image should be deleted and delete image
	 *
	 * @return void
	 */
    function deleteAction()
    {
		$id = (int)$this->_request->getParam('id', 0);
		$image_class = new Images();

		if ($this->_request->isPost()) {
			$image = $image_class->fetchRow('id = '.$id);
			$album_id = $image->album_id;
			
			// decerement viewer_order on all the images that have a higher
			// viewer_order than the deleted image
			$select = $image_class->select();
			$select->where('album_id = '.$image->album_id)
				   ->where('viewer_order > '.$image->viewer_order);
			$statement = $select->__toString();
			$this->logger->info($statement);
		    $images = $image_class->fetchAll($select);
			foreach ($images as $image) {
				$image->viewer_order--;
				$this->logger->info("image $image->id now has index of $image->viewer_order");
				$image->save();
			}
			
			$del = $this->_request->getPost('confirm');
			if ($del == 'Confirm' && $id > 0) {
				$image_class->delete('id='.$id);
			}
			
			if ($this->_request->getParam('from', 0) == 'viewImage' && $del != 'Confirm') {
				$this->_redirect('/image/view/id/'.$id);
			}else {
				$this->_redirect('/album/view/id/'.$album_id);
			}
		} else {
			if ($id > 0) {
				$this->view->title = 'Confirm Deletion';
				
				$image = $image_class->fetchRow('id='.$id);
				$this->view->image_title = $image->title;
				$form = new ImageDeleteForm();
				$form->confirm->setLabel('Confirm');
				$form->cancel->setLabel('Cancel');
				$this->view->form = $form;
				$form->populate($image->toArray());
			}
		}
    }
	
	/**
	 * Display image
	 *
	 * @return void
	 */
	function showImageAction()
	{
		$id = (int)$this->_request->getParam('id', 0);
	    
		$images = new Images();
		$image = $images->fetchRow('id='.$id);
		
		$this->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$image_data = join('', file('/www/photoplate/image/raw/'.$id));
		$this->getResponse()
			 ->setHeader('Content-Type', $image->file_type)
			 ->appendBody($image_data);
	}

	function showThumbAction()
	{
		$id = (int)$this->_request->getParam('id', 0);
		
		$images = new Images();
		$image = $images->fetchRow('id='.$id);
		
		$this->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$thumb_data = join('', file('/www/photoplate/image/thumb/'.$id));
		$this->getResponse()
			 ->setHeader('Content-Type', 'image/jpeg')
			 ->appendBody($thumb_data);
	}
	
    /**
     * Save uploaded image to filesystem
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $type Determines where image will be saved. Can be one of: raw
	 * @return void
	 */
	function saveImage($id, $name, $type)
	{
		if (is_uploaded_file($_FILES[$name]['tmp_name'])) {					
			$dest_path = $this->config->path->get('image');
			
			switch($type) {
			case 'raw':
				$dest_path .= '/raw';
				$this->setImageData($id, $name);
				break;
			case 'thumb':
				$dest_path .= '/thumb';
				$this->setThumbData($id, $name);
				break;
			default:
				$this->logger->error("saveImage(): Unknown image type \"$type\"");
				return;
			}
						
			$out_file = $dest_path.'/'.$id;
			$this->logger->info("saveImage(): saving image \"$name\" to $out_file");
			move_uploaded_file($_FILES[$name]['tmp_name'], $out_file);
		}
	}
	 
	/**
     * Update DB with uploaded image data
     *
     * @param  int $id
	 * @param  string $name
     * @return void
	 */
	function setImageData($id, $name)
	{
		$images = new Images();
		$row = $images->fetchRow('id='.$id);
		$row->file_type = $_FILES[$name]['type'];
	
		$sizes = getimagesize($_FILES[$name]['tmp_name']);
		$row->width = $sizes[0];
		$row->height = $sizes[1];
		$row->file_uploaded = 1;
		
		$row->save();
	}
	
	function setThumbData($id, $name)
	{
		$images = new Images();
		$row = $images->fetchRow('id='.$id);
		$row->thumb_file_type = $_FILES[$name]['type'];
		
		$sizes = getimagesize($_FILES[$name]['tmp_name']);;
		$row->thumb_width = $sizes[0];
		$row->thumb_height = $sizes[1];
		$row->thumb_exists = 1;

		$row->save();
	}
	
	/**
	 * Create a link to view the image in a pop-up
	 *
	 * @param  int $id
	 * @return string $link 
	 */
	function imageViewLink($id) 
	{
		$images = new Images();
		$image = $images->fetchRow('id='.$id);
		
		$link = '';
		if ($image->file_uploaded) {
				$url = $this->view->url(array('controller'=>'image', 'action'=>'show.image', 'id'=>$image->id), null, true);
				$width = $image->width + 20;
				$height = $image->height + 20;
				$link = "<a href=\"javascript:openUrl('$url', '$width', '$height');\">View</a>";
		}
		
		return $link;
	}
}
