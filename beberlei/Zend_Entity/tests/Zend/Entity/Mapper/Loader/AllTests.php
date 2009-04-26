<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "TestCase.php";
require_once "Basic/SimpleFixtureTest.php";
require_once "Basic/ManyToOneFixtureTest.php";

class Zend_Entity_Mapper_Loader_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Mapper_Loader Tests');
        $suite->addTestSuite('Zend_Entity_Mapper_Loader_Basic_SimpleFixtureTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Loader_Basic_ManyToOneFixtureTest');
        return $suite;
    }
}
