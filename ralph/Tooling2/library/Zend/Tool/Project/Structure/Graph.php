<?php

class Zend_Tool_Project_Structure_Graph
{
    
    protected $_topNodes = array();

    public function __construct()
    {
        
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $searchParameters
     * @return Zend_Tool_Project_Structure_Node
     */
    public function findNodeByContext($searchParameters)
    {
       if (is_string($searchParameters)) {
            $searchParameters = array($searchParameters);
        }
        
        $orderedSearchParameters = array();
        $orderedSearchContextIndex = 0;
        
        foreach ($searchParameters as $searchParamName => $searchParamValue) {
            
            if (is_int($searchParamName)) {
                $orderedSearchParameters[$orderedSearchContextIndex]['contextName'] = $searchParamValue;
                $orderedSearchParameters[$orderedSearchContextIndex]['contextParams'] = array();
            } elseif (is_string($searchParamName) && is_array($searchParamValue)) {
                $orderedSearchParameters[$orderedSearchContextIndex]['contextName'] = $searchParamName;
                $orderedSearchParameters[$orderedSearchContextIndex]['contextParams'] = $searchParamValue;
            } else {
                throw new Exception('your search criteria doesnt make sense.');
            }
            
            $orderedSearchContextIndex++;
        }
        
        $foundNode = null;
        
        while ($currentSearchParam = array_shift($orderedSearchParameters)) {
            
            if (!$foundNode) {
                
                foreach ($this->_topNodes as $node) {
                    $foundNode = $this->_recursiveFindNode($node, $currentSearchParam['contextName'], $currentSearchParam['contextParams']);
                    if ($foundNode) {
                        break;
                    }
                }
                
            } else {
                $foundNode = $this->_recursiveFindNode($foundNode, $currentSearchParam['contextName'], $currentSearchParam['contextParams']);
            }
            
        }
        
        return $foundNode;
    }
    
    protected function _recursiveFindNode(Zend_Tool_Project_Structure_Node $searchNode, $searchContextName, $searchContextParams)
    {
        
        $searchNodeIterator = new RecursiveIteratorIterator($searchNode, RecursiveIteratorIterator::SELF_FIRST);
        
        foreach ($searchNodeIterator as $currentNode) {
            if (strtolower($currentNode->getContext()->getName()) == strtolower($searchContextName)) {
                
                /*
                if ($searchContextParams) {
                    $contextParams = $subProjectContext->getParameters();
                    
                    $foundKeysSearch = array_intersect_key($searchContextParams, $contextParams);
                    $foundKeysOrig   = array_intersect_key($contextParams, $searchContextParams);
                    
                    // a search key was missing in the contextParams
                    if ($foundKeysSearch !== $searchContextParams) {
                        continue;
                    }
                    
                    foreach ($searchContextParams as $searchContextParamName => $searchContextParamValue) {
                        if ($contextParams[$searchContextParamName] !== $searchContextParamValue) {
                            continue 2;
                        }
                    }
                    
                    return $subProjectContext;
                } else {
                    return $subProjectContext;                    
                }
                */

                return $currentNode; 

            }
        }
        
        return false;
    }
    
    public function append(Zend_Tool_Project_Structure_Node $node)
    {
        $this->_topNodes[$node->getName()] = $node;
    }
    
    public function __set($name, $value)
    {
        $this->_topNodes[$name] = $value;
    }
    
    public function __get($name)
    {
        
    }
    
    public function __isset($name)
    {
        
    }
    
    public function __unset($name)
    {
        
    }

    public function __call($methodName, $arguments)
    {
        foreach ($this->_topNodes as $topNode) {
            call_user_func_array(array($topNode, $methodName), $arguments);
        }
    }

    public function getTopNodes()
    {
        return $this->_topNodes;
    }
    
}    