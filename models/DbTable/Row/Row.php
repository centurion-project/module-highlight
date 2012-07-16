<?php

class Highlight_Model_DbTable_Row_Row extends Centurion_Db_Table_Row_Proxy
{

    public function getBlockHtml(Highlight_Model_FieldMapper_Interface $mapper=null)
    {
        $meta = $this->getProxy()->getTable()->getMeta();
        if(!$mapper) {
            return sprintf('<span>%s</span>', $meta['verboseName']);
        }
        else {
            $mapped = $mapper->map($this->getProxy());
            $image = '';
            if(!empty($mapped['cover'])) {
                $imageUrl = $mapped['cover']->getStaticUrl(array('cropcenterresize'=>array('width'=>75, 'height'=>100)));
                $image = sprintf('<img src="%s" height="100" widht="75" style="float: left; width: 75; height: 100; margin-right: 5px;" width="75" height="100" /> ', $imageUrl);
            }
            return sprintf('<div>%s%s</div>', $image, $mapped['description']);
        }
    }

    public function map(Highlight_Model_FieldMapper_Interface $mapper = null)
    {
        if(!$mapper) {
            $mapper = Highlight_Model_FieldMapper_Factory::get('default');
        }
        if(!$this->getProxy()) {
            $res = $mapper->map($this);
            $typename = 'highlight';
        }
        else {
            $meta = $this->getProxy()->getTable()->getMeta();
            $typename = $meta['verboseName'];
        }

        $res = $mapper->map($this);
        if(empty($res['type'])) {
            $res['type'] = $typename;
        }

        return $res;
    }
    
}
