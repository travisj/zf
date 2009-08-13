<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

abstract class Zend_Entity_MapperAbstract
{
    /**
     * Zend Database adapter
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Entity Resource Map (of all the other entities)
     *
     * @var Zend_Entity_MetadataFactory_Interface
     */
    protected $_metadataFactory = null;

    /**
     * Loader
     *
     * @var Zend_Entity_Mapper_Loader_Interface
     */
    protected $_loader = null;

    /**
     * Persister
     *
     * @var Zend_Entity_Mapper_Persister_Interface
     */
    protected $_persister = null;

    /**
     * @var Zend_Entity_Mapper_MappingInstruction[]
     */
    protected $_mappingInstructions = array();

    /**
     * Construct DataMapper
     *
     * @param  Zend_Db_Adapter_Abstract  $db
     * @param  Zend_Entity_MetadataFactory_Interface $metadataFactory
     * @param  Zend_Entity_Mapper_MappingInstruction[] $mappingInstructions
     */
    public function __construct(Zend_Db_Adapter_Abstract $db, Zend_Entity_MetadataFactory_Interface $metadataFactory, array $mappingInstructions=array())
    {
        $this->_db = $db;
        $this->_metadataFactory = $metadataFactory;
        $this->_mappingInstructions = $mappingInstructions;
    }

    /**
     *
     * @param  string $entityName
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @param  mixed $keyValue
     * @return object
     */
    public function load($entityName, $entityManager, $keyValue)
    {
        $mi = $this->_mappingInstructions[$entityName];

        $tableName = $mi->table;
        $key = $mi->primaryKey->getColumnName();
        $query = $entityManager->createNativeQuery($mi->class);
        $cond = $this->_db->quoteIdentifier($tableName.".".$key);
        $query->where($cond." = ?", $keyValue);

        return $query->getSingleResult();
    }

    /**
     * Save a entity into persistence.
     *
     * @throws Exception
     * @param  array $entity
     * @return void
     */
    public function save(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager)
    {
        if($entity instanceof Zend_Entity_LazyLoad_Entity) {
            $className = $entity->__ze_getClassName();
        } else if($entity instanceof Zend_Entity_Interface) {
            $className = get_class($entity);
        }

        $persister = $this->getPersister($className);
        $persister->save($entity, $entityManager);
    }

    /**
     * Delete a entity from persistence
     *
     * @throws Exception
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager )
    {
        if($entity instanceof Zend_Entity_LazyLoad_Entity) {
            $className = $entity->__ze_getClassName();
        } else if($entity instanceof Zend_Entity_Interface) {
            $className = get_class($entity);
        }

        $this->getPersister($className)->delete($entity, $entityManager);
    }

    /**
     * Return select statement object
     *
     * @return Zend_Db_Select
     */
    public function select()
    {
        return new Zend_Entity_Mapper_Select( $this->getAdapter() );
    }
    
    /**
     * Return DB Adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getAdapter()
    {
        return $this->_db;
    }

    /**
     * Return responsible Persister class
     *
     * @param string $className
     * @return Zend_Entity_Mapper_Persister_Interface
     */
    protected function getPersister($className)
    {
        if(!isset($this->_persister[$className])) {
            $this->_persister[$className] = new Zend_Entity_Mapper_Persister_Simple();
            $this->_persister[$className]->initialize($this->_mappingInstructions[$className]);
        }

        return $this->_persister[$className];
    }

    /**
     * Return Resultprocessor and Object Loader
     * 
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    public function getLoader($className)
    {
        if(!isset($this->_loader[$className])) {
            $this->_loader[$className] = new Zend_Entity_Mapper_Loader_Basic(
                $this->_metadataFactory->getDefinitionByEntityName($className),
                $this->_mappingInstructions[$className]
            );
        }
        return $this->_loader[$className];
    }
}