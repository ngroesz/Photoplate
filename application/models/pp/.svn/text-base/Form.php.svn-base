<?php
class PP_Form extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->addElementPrefixPath('PP_Decorator', 'PP/Decorator/', 'decorator');
		
		$fieldset = new Zend_Form_Decorator_Fieldset;
		if (isset($options['legend'])) {
			$fieldset->setLegend($options['legend']);
		}
		$this->setDecorators(array(
			'FormElements',
			$fieldset,
			'Form'
		));
		if (isset($options['name'])) {
			$this->setName($options['name']);
		}
	}
}	
