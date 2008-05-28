<?php

abstract class Zend_Tool_Project_Provider_Abstract implements Zend_Tool_Rpc_Provider_Interface 
{
    
    private static $_builtInClassesLoaded = false;
    
    protected $_loadedStructureGraph = null;
    
    final public function __construct()
    {
        
        if ($contextClasses = $this->getContextClasses()) {
            $this->_loadContextClassesIntoRegistry($contextClasses);
        }
        
        if (!self::$_builtInClassesLoaded) {
            $this->_loadContextClassesIntoRegistry(array(
                'Zend_Tool_Project_Structure_Context_Zf_ProjectDirectory',
                'Zend_Tool_Project_Structure_Context_Zf_ApplicationDirectory',
                'Zend_Tool_Project_Structure_Context_Zf_BootstrapFile',
                'Zend_Tool_Project_Structure_Context_Zf_HtaccessFile',
                'Zend_Tool_Project_Structure_Context_Zf_LibraryDirectory',
                'Zend_Tool_Project_Structure_Context_Zf_ModelsDirectory',
                'Zend_Tool_Project_Structure_Context_Zf_ModulesDirectory',
                'Zend_Tool_Project_Structure_Context_Zf_ProjectProfileFile',
                'Zend_Tool_Project_Structure_Context_Zf_ProvidersDirectory',
                'Zend_Tool_Project_Structure_Context_Zf_PublicDirectory',
                'Zend_Tool_Project_Structure_Context_Zf_PublicIndexFile'
                ));
        }
        
        
        
    }
    
    public function getContextClasses()
    {
        return array();
    }
    
    /**
     * _getProject is designed to find if there is project file in the context of where
     * the endpoint has been called from..   The search order is as follows..
     *    - traversing downwards from (PWD) - current working directory
     *    - if an enpoint variable has been registered in teh endpoint registry - key=workingDirectory
     *    - if an ENV variable with the key ZFPROJECT_PATH is found
     * 
     * @return Zend_Tool_Project_Structure_Graph
     */
    protected function _loadExistingStructureGraph($path = null)
    {
        
        

        if ($path == null) {
            $path = $_SERVER['PWD'];
        }
        
        $structureDataFile = $path . '/.zfproject.xml'; // @todo make this non-hard coded?
        
        if (file_exists($structureDataFile)) {
            $structureData = file_get_contents($structureDataFile);
        }
                
        $structureGraph = false;
        
        if (isset($structureData)) {
            
            $structureGraphParser = new Zend_Tool_Project_Structure_Parser_Xml();
            $structureGraph = $structureGraphParser->unserialize($structureData);

            $structureGraph->recursivelySetBaseDirectory($path);
            
        }

        $this->_loadedStructureGraph = $structureGraph;
        return $structureGraph;
    }
    
    
    public function storeLoadedStructureGraph()
    {
        $projectProfileFile = $this->_loadedStructureGraph->findNodeByContext('ProjectProfileFile');
        
        echo 'Updating project profile \'' . $name . '\'' . PHP_EOL;
        $projectProfileFile->create();
    }
    
    private function _loadContextClassesIntoRegistry($contextClasses)
    {
        
        $registry = Zend_Tool_Project_Structure_Context_Registry::getInstance();
        foreach ($contextClasses as $contextClass) {
            $registry->addContextClass($contextClass);
        }
    }
    
}