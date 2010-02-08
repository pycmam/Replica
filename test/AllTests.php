<?php
require_once(dirname(__FILE__) . '/bootstrap.php');

/**
 * All Tests
 */
class Replica_AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * TestSuite
     */
    public static function suite()
    {
        $runner = new PHPUnit_TextUI_TestRunner(new PHPUnit_Runner_StandardTestSuiteLoader);
        return $runner->getTest(dirname(__FILE__).'/phpunit');
    }

}