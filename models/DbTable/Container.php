<?php
class Highlight_Model_DbTable_Container extends Centurion_Db_Table_Abstract
{
    protected $_name = 'highlight_container';
    protected $_rowClass = 'Highlight_Model_DbTable_Row_Container';
    
    public function addRow($container, $row, $position)
    {
        if ($container instanceof Centurion_Db_Table_Row_Abstract) {
            $container = $container->id;
        }
        
        list($highlightRow, $created) = Centurion_Db::getSingleton('highlight/row')->getOrCreate(array('container_id' => $container, 'position' => $position));
        $highlightRow->setProxy($row);
        $highlightRow->save();
    }
    
    protected $_referenceMap = array(
        'proxy_content_type' => array(
            'columns' => 'proxy_content_type_id',
            'refColumns' => 'id',
            'refTableClass' => 'Core_Model_DbTable_ContentType'
        ),
    );

    public function createWithName($name)
    {
        if(!is_string($name)) {
            throw new InvalidArgumentException('Name must be a string. '.gettype($name).' given');
        }

        list($container, $created) = $this->getOrCreate(array(
            'name'                  => $name,
        ));
        if(!$created) {
            throw new InvalidArgumentException('There already is a container by that name');
        }

        return $container;
    }

    public function createWithProxy(Centurion_Db_Table_Row_Abstract $instance)
    {
        $modelName = $instance->getTableClass();
        list($model, $created) = Centurion_Db::getSingleton('core/content_type')->getOrCreate(array('name'=>$modelName));
        list($container, $created) = $this->getOrCreate(array(
            'proxy_content_type_id' => $model->id,
            'proxy_pk'              => $instance->pk
        ));
        if(!$created) {
            throw new InvalidArgumentException('There already is a container by that proxy');
        }



        return $container;
    }

    public function createWithNameAndProxy($name, Centurion_Db_Table_Row_Abstract $instance)
    {
        if(!is_string($name)) {
            throw new InvalidArgumentException('Name must be a string. '.gettype($name).' given');
        }
        $modelName = $instance->getTableClass();
        list($model, $created) = Centurion_Db::getSingleton('core/content_type')->getOrCreate(array('name'=>$modelName));
        list($container, $created) = $this->getOrCreate(array(
            'name'                  => $name,
            'proxy_content_type_id' => $model->id,
            'proxy_pk'              => $instance->pk
        ));

        if (!$created) {
            throw new InvalidArgumentException('There already is a container by that name for that proxy');
        }

        return $container;
    }


}
