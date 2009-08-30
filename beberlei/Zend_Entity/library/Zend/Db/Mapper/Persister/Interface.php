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

interface Zend_Db_Mapper_Persister_Interface
{
    /**
     * Initialize is called once on each persister to gather information on how to perform the persist operation.
     * 
     * @param  Zend_Entity_Definition_Entity $entityDef
     * @param  Zend_Db_Mapper_Mapping[] $mappingInstruction
     * @return void
     */
    public function initialize(Zend_Db_Mapper_Mapping $mappingInstruction);

    /**
     * Save entity into persistence based on the persisters scope
     *
     * @param  object $entity
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    public function save($entity, Zend_Entity_Manager_Interface $entityManager);

    /**
     * Remove entity from persistence based on the persisters scope
     *
     * @param  object $entity
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    public function delete($entity, Zend_Entity_Manager_Interface $entityManager);
}