<?php
require_once dirname(__FILE__).'/../bootstrap.php';

/**
 * ImageProxy_FromFile test
 *
 * @author  Maxim Oleinik <maxim.oleinik@gmail.com>
 */
class Replica_ImageProxy_FromFileTest extends ReplicaTestCase
{
    /**
     * Get UID
     */
    public function testGetUid()
    {
        $proxy = new Replica_ImageProxy_FromFile($path = $this->getFileNameInput('gif_16x14'));
        $this->assertEquals($path, $proxy->getUid());
    }


    /**
     * Get Image
     */
    public function testGetImage()
    {
        $proxy = new Replica_ImageProxy_FromFile($path = $this->getFileNameInput('gif_16x14'));
        $image = $proxy->getImage();

        $this->assertInstanceOf('Replica_Image_Gd', $image);
        $this->assertImage($image, 16, 14, 'image/png'); // !!! PNG
    }

}
