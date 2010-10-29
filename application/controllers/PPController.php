<?php

class PPController extends Zend_Controller_Action
{
	function init()
	{
		$logger = Zend_Registry::get('logger');
	}
	
	function datetime($format = 'Y-m-d h:i:s') {
		return date($format);
	}
	
	function getUploadedFile($name)
	{	
		if (is_uploaded_file($_FILES[$name]['tmp_name'])) {					
			$fh = fopen($_FILES[$name]['tmp_name'], 'r') or die($php_errormsg);
			$data = fread($fh, filesize($_FILES[$name]['tmp_name']));
			fclose($fh) or die($php_errormsg);
			
			return $data;
		} else {
			return null;
		}
	}
}
