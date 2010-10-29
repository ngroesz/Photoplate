<?php

require 'XmlParser.php';

class ViewerBuilder
{
	function __construct()
	{
		$this->config = Zend_Registry::get('config');
		$this->logger = Zend_Registry::get('logger');
		
		$this->parser = new XmlParser($this);
		$this->parser->setHandler('image', 'handleImage');
		$this->parser->setHandler('capture_description', 'handleCaptureDescription');
		$this->parser->setHandler('capture_title', 'handleCaptureTitle');
		$this->parser->setHandler('capture_select_list', 'handleCaptureSelectList');		
		$this->parser->setHandler('album_title', 'handleAlbumTitle');
	}
	
	function buildViewer($template_id, $album_id)
	{	
		$this->template_id = $template_id;
		$this->album_id = $album_id;
		
		$this->setupDir();
		
		$templates = new Templates();
		$this->template = $templates->fetchRow('id='.$template_id);
		
		if (!$this->template->data) {
			throw new BuilderException('Template not uploaded');
		}

		$this->writeResources();
		
		$images = $this->imageList();
		foreach ($images as $image) {
			$this->logger->info("building $image->title");
			$this->current_image = $image;
			$this->buildPage();
		}
		
		symlink($this->build_dir . '/1.html', $this->build_dir . '/index.html');
		
		$this->setBuildTime();
	}
	
	function buildPage()
	{
		$this->copyImage();
		
		$index = $this->current_image->viewer_order;
		
		$this->parser->parseXml($this->template->data);
		
		$dom = $this->parser->dom;
		$html = $dom->saveXml();
		
		$out_file = $this->build_dir . '/' . $this->current_image->viewer_order . '.html';
		$this->writeFile($out_file, $html);
	}
	
	function writeResources()
	{
		$resource_class = new Resources();
		
		$resources = $resource_class->fetchAll('template_id='.$this->template_id);

		// we keep this associative array around to ensure unique filenames
		$this->resource_id_to_filename = array();
		
		foreach ($resources as $resource) {
			$this->logger->info("looking at resource $resource->name");
			$filename = $this->getResourceFileName($resource);
			$this->replaceSrcName($resource->name, $filename);
			
			$this->logger->info("name is $filename");
			$out_file = $this->build_dir . '/' . $filename;
			$this->writeFile($out_file, $resource->data);
			
			$this->resource_id_to_filename[$resource->id] = $filename;
		}
	}
	
	function getResourceFileName($resource)
	{
		$filename = '';
		
		// this regular expression matches the filename and discards the preceding path
		if (preg_match('#(\w+/)*(\w+\.\w+)#', $resource->name, $matched)) {
			$filename = $matched[2];
		} else {
			$filename = $resource->id;
		}
		
		// make sure that the filename is unique
		$resource_class = new Resources();
		$resources = $resource_class->fetchAll('template_id='.$this->template_id);
		$unique = 1;
		do {
			foreach ($resources as $other_resource) {
				if (($resource->id != $other_resource->id) && 
				  array_key_exists($other_resource->id, $this->resource_id_to_filename) &&
				  (strcmp($filename, $this->resource_id_to_filename[$other_resource->id]) == 0)) {
					$unique = 0;
					$filename = $resource->id . '_' . $filename;
				}
			}
		} while($unique == 0);
		
		return $filename;
	}
	
	function replaceSrcName($original, $new)
	{
		if (strcmp($original, $new) == 0) {
			return 0;
		}
		
		preg_replace("/src=\"$original\"/", "src=\"$new\"", $this->template->data);
	}
	
	function copyImage()
	{
		$image_path = $this->config->path->get('image') . '/raw';

		$extension = '';
		if ($this->current_image->file_type == 'image/jpeg') {
			$extension = 'jpg';
		}		
		$dest = $this->build_dir . '/' . $this->current_image->viewer_order . ".$extension";
		if (!copy($image_path.'/'.$this->current_image->id, $dest)) {
			throw new BuilderException("Could not copy image to $dest: $php_errormsg");
		}
	}
	
	function writeFile($file_name, $data)
	{
		$this->logger->info("writing to $file_name");
		if (!($fh = fopen($file_name, 'w'))) {
			throw new BuilderException("Could not open file $file_name for writing: $php_errormsg");
		}
		fputs($fh, $data);
		fclose($fh);
	}
	
	function setupDir()
	{
		$viewer_dir = $this->config->path->get('viewer');
		$this->build_dir = $viewer_dir.'/'.$this->album_id.'_'.$this->template_id;
		
		if (!is_dir($this->build_dir)) {
			if (!mkdir($this->build_dir, 0755)) {
				throw new BuilderException("Could not make dir $this->build_dir: $php_errormsg");
			}
		}
		
		// clear out directory
		if (!($dh = opendir($this->build_dir))) {
			throw new BuilderException("Could not open dir $this->build_dir: $php_errormsg");
		}
		while (false !== ($file = readdir($dh))) {
			if (is_file($this->build_dir.'/'.$file)) {
				if (!unlink($this->build_dir.'/'.$file)) {
					throw new BuilderException("Could not erase file $this->build_dir/$file: $php_errormsg");
				}
			}
		}
		closedir($dh);
	}
	
	function imageList()
	{
		$image_class = new Images();
		$images = $image_class->fetchAll('file_uploaded=1 AND album_id='.$this->album_id);
		
		return $images;
	}
	
	function setBuildTime()
	{
		$viewer_class = new Viewers();
		$select = $viewer_class->select();
		$select->where('album_id = ' . $this->album_id)
			   ->where('template_id = ' . $this->template_id);
			   
		$viewer = $viewer_class->fetchRow($select);
		
		$viewer->built_on = date('Y-m-d h:i:s');
		$viewer->save();
	}
	
	function handleImage($dom) {
		$extension = '';
		if ($this->current_image->file_type == 'image/jpeg') {
			$extension = 'jpg';
		}		
		
		$img = $dom->createElement('img');
		$img->setAttribute('src', $this->current_image->viewer_order.".$extension");
		$img->setAttribute('alt', $this->current_image->title);
		$img->setAttribute('width', $this->current_image->width);
		$img->setAttribute('height', $this->current_image->height);
		return $img;
	}
	
	function handleCaptureSelectList($dom) {
		$div = $dom->createElement('div');
		
		$form = $dom->createElement('form');
		$form->setAttribute('method', 'get');
		$form->setAttribute('action', $this->current_image->viewer_order . '.html');
		
		$p = $dom->createElement('div');
		
		$select = $dom->createElement('select');
		$select->setAttribute('name', 'capture');
		$change_script = "location.href=form.capture.options[selectedIndex].value;";
		$select->setAttribute('onchange', $change_script);
		
		$images = $this->imageList();
		foreach ($images as $image) {
			$option = $dom->createElement('option');
			$option->setAttribute('value', $image->viewer_order . '.html');
			
			if ($image->id == $this->current_image->id) {
				$option->setAttribute('selected', 'selected');
			}
			
			$option->appendChild($dom->createTextNode($image->title));
			$select->appendChild($option);
		}
		$p->appendChild($select);
		$form->appendChild($p);
		$div->appendChild($form);
		
		return $div;
	}
	
	function handleCaptureDescription($dom) {
		$text = $dom->createTextNode($this->current_image->description);
		
		return $text;
	}
		
	function handleCaptureTitle($dom) {
		$text = $dom->createTextNode($this->current_image->title);
		
		return $text;
	}
	
	function handleAlbumTitle($dom) {
		$album_class = new Albums();
		$album = $album_class->fetchRow('id = '.$this->album_id);
		
		$title = $dom->createElement('title');
		$title->appendChild($dom->createTextNode($album->title));
		return $title;
	}
}
