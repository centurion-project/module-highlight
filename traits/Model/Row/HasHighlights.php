<?php

/**
 *  An interface that a row can implement to help
 *  it forces to have a getCrawler method
 **/
class Highlight_Traits_Model_Row_HasHighlights extends Centurion_Traits_Model_DbTable_Row_Abstract 
{
    public function init()
    {
        parent::init();
    }

}
