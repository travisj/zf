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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/** Zend_Controller_Router_Exception */
require_once 'Zend/Controller/Router/Exception.php';

/** Zend_Controller_Router_Route_Interface */
require_once 'Zend/Controller/Router/Route/Interface.php';

/**
 * Module Route
 *
 * Default route for module functionality
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Zend_Controller_Router_Route_Module implements Zend_Controller_Router_Route_Interface
{
    /**
     * @const string URI delimiter
     */
    const URI_DELIMITER = '/';
    
    /**
     * Default values for the route (ie. module, controller, action, params)
     * @var array
     */
    protected $_defaults;

    protected $_values = array();
    protected $_moduleValid = false;
    
    /**#@+
     * Array keys to use for module, controller, and action. Should be taken out of request.
     * @var string
     */
    protected $_moduleKey     = 'module';
    protected $_controllerKey = 'controller';
    protected $_actionKey     = 'action';
    /**#@-*/    

    /**
     * Constructor
     *
     * @param array Defaults for map variables with keys as variable names
     * @param Zend_Controller_Dispatcher_Interface Dispatcher object
     * @param Zend_Controller_Request_Abstract Request object
     */
    public function __construct(array $defaults = array(), 
                Zend_Controller_Dispatcher_Interface $dispatcher = null, 
                Zend_Controller_Request_Abstract $request = null)
    {
        $this->_defaults = $defaults;

        if (isset($dispatcher)) {
            $this->_defaults += array(
                    'controller' => $dispatcher->getDefaultControllerName(), 
                    'action'     => $dispatcher->getDefaultAction(),
                    'module'     => 'default'
            );
            $this->_dispatcher = $dispatcher;
        }

        if (isset($request)) {
            $this->_moduleKey     = $request->getModuleKey();
            $this->_controllerKey = $request->getControllerKey();
            $this->_actionKey     = $request->getActionKey();
        }
    }

    /**
     * Matches a user submitted path. Assigns and returns an array of variables 
     * on a successful match.  
     *
     * If a request object is registered, it uses its setModuleName(), 
     * setControllerName(), and setActionName() accessors to set those values. 
     * Always returns the values as an array.
     *
     * @param string Path used to match against this routing map 
     * @return array An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
        $values = array();
        $params = array();
        $path   = trim($path, self::URI_DELIMITER);

        if ($path != '') {

            $path = explode(self::URI_DELIMITER, $path);
        
            if ($this->_dispatcher && $this->_dispatcher->isValidModule($path[0])) {
                $values[$this->_moduleKey] = array_shift($path);
                $this->_moduleValid = true;
            }
            
            if (count($path) && !empty($path[0])) {
                $values[$this->_controllerKey] = array_shift($path);
            }

            if (count($path) && !empty($path[0])) {
                $values[$this->_actionKey] = array_shift($path);
            }

            if ($numSegs = count($path)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
                    $val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
                    $params[$key] = $val;
                }
            }
        }
        
        $this->_values = $values + $params; 
        
        return $this->_values + $this->_defaults;
    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route 
     *
     * @param array An array of variable and value pairs used as parameters 
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false)
    {
        
        $params = $data + $this->_values + $this->_defaults;
        
        $url = '';
        
        if ($this->_moduleValid || isset($data[$this->_moduleKey])) {
            $module = $params[$this->_moduleKey];
        } 
        unset($params[$this->_moduleKey]);
        
        $controller = $params[$this->_controllerKey];
        unset($params[$this->_controllerKey]);

        $action = $params[$this->_actionKey];
        unset($params[$this->_actionKey]);
        
        foreach ($params as $key => $value) {
            $url .= '/' . $key;
            $url .= '/' . $value;
        }
        
        if (!empty($url) || $action !== $this->_defaults[$this->_actionKey]) {
            $url = '/' . $action . $url;
        }
        
        if (!empty($url) || $controller !== $this->_defaults[$this->_controllerKey]) {
            $url = '/' . $controller . $url;
        }
        
        if (isset($module) && (!empty($url) || $module !== $this->_defaults[$this->_moduleKey])) {
            $url = '/' . $module . $url;
        }
        
        return ltrim($url, self::URI_DELIMITER);
    }
    
}
