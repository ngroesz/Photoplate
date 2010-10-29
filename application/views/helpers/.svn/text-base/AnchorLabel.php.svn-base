<?php

require_once 'Zend/View/Helper/FormElement.php';

class Zend_View_Helper_AnchorLabel extends Zend_View_Helper_FormElement
{
    /**
     * Generates a 'label' element.
     *
     * @param  string $name The form element name for which the label is being generated
     * @param  string $value The label text
     * @param  array $attribs Form element attributes (used to determine if disabled)
     * @return string The element XHTML.
     */
    public function formLabel($name, $value = null, array $attribs = array())
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable, escape

        // build the element
        if ($disable) {
            // disabled; do nothing
        } else {
            $value = ($escape) ? $this->view->escape($value) : $value;

            // enabled; display label
            $xhtml = '<a'
                   . $this->_htmlAttribs($attribs)
                   . '>' . $value . '</a>';
        }

        return $xhtml;
    }
}

