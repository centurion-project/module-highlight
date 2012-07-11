<?php

class Highlight_View_Helper_HighlightAutoCompleteTextForm extends Zend_View_Helper_FormText
{
    /**
     * really only adds a submit button near the input field
     */
    public function highlightAutoCompleteTextForm($name, $value = null, $attribs = null)
    {
        $input = $this->formText($name, $value, $attribs);
        return $input . '<input type="submit" name="'.$this->view->translate('Add').'" value="'.$this->view->translate('Add').'"/>';
    }
}
