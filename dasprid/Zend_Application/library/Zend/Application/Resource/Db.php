<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Resource for creating database adapter
 *
 * @uses       Zend_Application_Resource_Base
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_Db extends Zend_Application_Resource_Base
{
    /**
     * Adapter to use
     *
     * @var string
     */
    protected $_adapter = null;
    
    /**
     * Parameters to use
     *
     * @var array
     */
    protected $_params = array();
    
    /**
     * Wether to register the created adapter as default table adapter
     *
     * @var boolean
     */
    protected $_isDefaultTableAdapter = true; 
    
    /**
     * Set the adapter
     * 
     * @param  $adapter string
     * @return Zend_Application_Resource_Db
     */
    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Set the adapter params
     * 
     * @param  $adapter string
     * @return Zend_Application_Resource_Db
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }
    
    /**
     * Set wether to use this as default table adapter
     *
     * @param  boolean $defaultTableAdapter
     * @return Zend_Application_Resource_Db
     */
    public function setIsDefaultTableAdapter($isDefaultTableAdapter)
    {
        $this->_isDefaultTableAdapter = $isDefaultTableAdapter;
        return $this;
    }
    
    /**
     * Defined by Zend_Application_Resource_IResource
     *
     * @return void
     */
    public function init()
    {
        if ($this->_adapter !== null) {
            $db = Zend_Db::factory($this->_adapter, $this->_params);
            
            if ($this->_isDefaultTableAdapter) {
                Zend_Db_Table::setDefaultAdapter($db);
            }
        }
    }
}
