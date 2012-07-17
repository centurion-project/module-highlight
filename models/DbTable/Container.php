<?php
class Highlight_Model_DbTable_Container extends Centurion_Db_Table_Abstract
{
    protected $_name = 'highlight_container';
    protected $_rowClass = 'Highlight_Model_DbTable_Row_Container';
    
    /**
     * adds a row to a given container at the given position
     * @param $containers the container to which we add the row
     * @param $row the row to which we need a reference added to this container
     * @param $position the position at which we want the row to be in the collection
     */
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

    /** 
     * create a container with the given name
     * will throw if a container of that name already exists
     * @param $name the name we want our container to have
     * @throws InvalidArgumentException
     * @return Centurion_Db_Table_Row_Abstract
     */
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

    /**
     * create a container with the given proxy
     * will throw if a container with that proxy already exists
     * @param $instance the model to which we want to attach this container
     * @throws InvalidArgumentException
     * @return Centurion_Db_Table_Row_Abstract
     */
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

    /**
     * not used yet.
     * will be used if the spec ever says we need more than one container for a given proxy
     * @return Centurion_Db_Table_Row_Abstract
     */
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


    /**
     * find a container with the given proxy
     * this should return only one row.
     * @param $row the proxy attached to the container we are looking for
     * @return Centurion_Db_Table_Row_Abstract
     */
    public function findOneByProxy(Centurion_Db_Table_Row_Abstract $row)
    {
        if(!$row) return null;
        $ctype = Centurion_Db::getSingleton('core/content_type')->findOneByName(get_class($row->getTable()));
        if(!$ctype) return null;
        $select = $this->select(true)->filter(array(
            'proxy_pk'          => $row->pk,
            'proxy_content_type_id' => $ctype->id
        ));
        return $select->fetchRow();
    }


    public function findOneByNameAndProxy($name, Centurion_Db_Table_Row_Abstract $row)
    {
        if(!is_string($name)) {
            throw new InvalidArgumentException('Name must be a string. '.gettype($name).' given');
        }
        $ctype = Centurion_Db::getSingleton('core/content_type')->findOneByName(get_class($row->getTable()));
        $select = $this->select(true)->filter(array(
            'proxy_pk'          => $row->pk,
            'proxy_content_type_id' => $ctype->id,
            'name'              => $name
        ));
        return $select->fetchRow();
    }


}
