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
 * @version    $Id: Layout.php 14329 2009-03-16 16:20:26Z dolf $
 */

/**
 * Resource for setting navigation structure
 *
 * @uses       Zend_Application_Resource_Base
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Dolf Schimmel
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_Navigation extends Zend_Application_Resource_Base
{
    const DEFAULT_REGISTRY_KEY = 'Zend_Navigation';

    /**
     * @var Zend_Navigation
     */
    protected $_container;

    /**
     * Defined by Zend_Application_Resource_IResource
     *
     * @return void
     */
    public function init()
    {
        if (!$this->_container) {
            $options = $this->getOptions();
            $pages = isset($options['pages']) ? $options['pages'] : array();
            $this->_container = new Zym_Navigation($pages);
        }

        $this->store();
    }

    /**
     * Stores navigation container in registry or Navigation view helper
     *
     * @return void
     */
    public function store()
    {
        $options = $this->getOptions();
        if (isset($options['storage']['registry']) &&
            $options['storage']['registry'] == true) {
            $this->_storeRegistry();
        } else {
            $this->_storeHelper();
        }
    }

    /**
     * Stores navigation container in the registry
     *
     * @return void
     */
    protected function _storeRegistry()
    {
        $options = $this->getOptions();
        $key = !is_numeric($options['storage']['registry']['key'])
             ?  $options['storage']['registry']['key']
             : self::DEFAULT_REGISTRY_KEY;
        Zend_Registry::set($key,$this->getNavigation());
    }

    /**
     * Stores navigation container in the Navigation helper
     *
     * @return void
     */
    protected function _storeHelper()
    {
        $this->getBootstrap()->bootstrap('view');
        $view = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;
        $view->getHelper('navigation')->navigation($this->getContainer());
    }

    /**
     * Returns navigation container
     *
     * @return Zend_Navigation
     */
    public function getContainer()
    {
        return $this->_container;
    }
}
