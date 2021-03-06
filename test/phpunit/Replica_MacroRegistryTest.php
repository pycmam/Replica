<?php
require_once dirname(__FILE__).'/../bootstrap.php';

/**
 * Replica: macro registry test
 *
 * @author  Maxim Oleinik <maxim.oleinik@gmail.com>
 */
class Replica_Replica_MacroRegistryTest extends ReplicaTestCase
{
    /**
     * TearDown
     */
    protected function _teardown()
    {
        Replica::removeAll();
    }


    /**
     * Set/Get macro
     */
    public function testSetGetMacro()
    {
        $this->assertFalse(Replica::hasMacro('macro_name'));

        Replica::setMacro('macro_name', $macro = new Replica_Macro_Null);
        $this->assertTrue(Replica::hasMacro('macro_name'));
        $this->assertSame($macro, Replica::getMacro('macro_name'));
    }


    /**
     * Set macro with invalid name
     */
    public function testSetMacroWithInvalidName()
    {
        $macro = new Replica_Macro_Null;

        $str = ' :;\\/~`!@#$%^&*()-=+[]{}\'\"|?<>,.я';
        for ($i=0, $n=strlen($str); $i<$n; $i++) {
            try {
                Replica::setMacro($name = 'name_'.$str[$i], $macro);
                $this->fail("Expected exception for macro name `{$name}`");
            } catch (Replica_Exception $e) {}
        }
    }


    /**
     * Get unknown macro
     */
    public function testGetUnknownMacro()
    {
        $this->setExpectedException('Replica_Exception', 'Unknown macro');
        Replica::getMacro('unknown');
    }


    /**
     * Reset registry
     */
    public function testResetRegistry()
    {
        Replica::setMacro('macro_name', $macro = new Replica_Macro_Null);
        Replica::removeAll();
        $this->assertFalse(Replica::hasMacro('macro_name'));

        $this->setExpectedException('Replica_Exception', 'Unknown macro');
        Replica::getMacro('macro_name');
    }


    /**
     * Apply macro
     */
    public function testApplyMacro()
    {
        $image = new Replica_Image_Gd;
        $image->loadFromFile($this->getFileNameInput('gif_16x14'));

        $macro = $this->getMock('Replica_Macro_Null', array('run'));
        $macro->expects($this->once())
              ->method('run')
              ->with($this->equalTo($image));

        Replica::setMacro('macro_name', $macro);
        Replica::applyMacro('macro_name', $image);
    }


    /**
     * Apply unknown macro
     */
    public function testApplyUnknownMacro()
    {
        $this->setExpectedException('Replica_Exception', 'Unknown macro');
        Replica::applyMacro('macro_name', new Replica_Image_Gd);
    }

}
