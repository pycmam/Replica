<?php

/**
 * Macro cache manager
 *
 * @package    Replica
 * @subpackage Cache
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
class Replica_Macro_CacheManager
{
    /**
     * Save dir
     */
    private $_dir;


    /**
     * Set cache dir
     *
     * @param  string $dir
     * @return void
     */
    public function __construct($dir)
    {
        $this->_dir = (string) $dir;

        if (!strlen($this->_dir)) {
            throw new InvalidArgumentException(__METHOD__.": Expected save dir");
        }
    }


    /**
     * Get macro result
     *
     * @param  string|Replica_Macro_Abstract  $macro
     * @param  Replica_ImageProxy             $imageProxy
     * @param  string                         $mimeType
     * @return void
     */
    public function get($macro, Replica_ImageProxy_Abstract $imageProxy)
    {
        // Get macro
        if ($macro instanceof Replica_Macro_Abstract) {
            $macroName = get_class($macro);
        } else {
            $macroName = (string) $macro;
            $macro = Replica::getMacro((string)$macroName);
        }

        // Make UID for macro result
        $fileName = sha1(md5(
              $imageProxy->getUid()
            . (int) $imageProxy->getQuality()
            . $macroName
            . get_class($macro)
            . serialize($macro->getParameters())
        ));

        $fileName .= $this->_getExtension($imageProxy->getMimeType());

        // Define image save path
        $relativeDir = $macroName   . DIRECTORY_SEPARATOR
                     . $fileName[0] . DIRECTORY_SEPARATOR
                     . $fileName[1] . DIRECTORY_SEPARATOR
                     . $fileName[2];
        $fileDir  = $this->_dir . DIRECTORY_SEPARATOR . $relativeDir;
        $filePath = $fileDir . DIRECTORY_SEPARATOR . $fileName;

        // Run macro and cache
        if (!file_exists($filePath)) {

            $image = $imageProxy->getImage();
            $macro->run($image);

            $this->_checkDir($fileDir);
            $image->saveAs($filePath);
        }

        return $relativeDir . DIRECTORY_SEPARATOR . $fileName;
    }


    /**
     * Get file extension by mime type
     *
     * @param  string $mimeType
     * @return string
     */
    private function _getExtension($mimeType)
    {
        switch ($mimeType) {
            case Replica_Image_Abstract::TYPE_PNG:
                return '.png';
                break;

            case Replica_Image_Abstract::TYPE_GIF:
                return '.gif';
                break;

            case Replica_Image_Abstract::TYPE_JPEG:
                return '.jpg';
                break;

            default:
                throw new Replica_Exception(__METHOD__.": Unknown image type `{$mimeType}`");
        }
    }


    /**
     * Check and create dir if not exists
     *
     * @throws Replica_Exception if failed to create dir
     *
     * @param  string $dir
     * @return void
     */
    private function _checkDir($dir)
    {
        $errorLevel = error_reporting(0);
        try {
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    throw new Replica_Exception(__CLASS__.": Failed to create directory `{$dir}`");
                }
            }
        } catch (Exception $e) {}
        error_reporting($errorLevel);

        if (isset($e)) {
            throw $e;
        }
    }

}
