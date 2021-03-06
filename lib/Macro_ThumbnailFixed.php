<?php

/**
 * Make thumbnail with fixed dimensions, crops if necessary
 *
 * @package    Replica
 * @subpackage Macro
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
class Replica_Macro_ThumbnailFixed extends Replica_Macro_Abstract
{
    private $_width;
    private $_height;
    private $_cropWidth;
    private $_cropHeight;

    const POS_LEFT   = 'left';
    const POS_RIGHT  = 'right';
    const POS_TOP    = 'top';
    const POS_BOTTOM = 'bottom';
    const POS_CENTER = 'center';


    /**
     * Construct
     *
     * @param  int    $width
     * @param  int    $height
     * @param  string $cropWidth  - left|center|right
     * @param  string $cropHeight - top|center|bottom
     * @return void
     */
    public function __construct($width, $height, $cropWidth = self::POS_LEFT, $cropHeight = self::POS_TOP)
    {
        $this->_width  = $width;
        $this->_height = $height;

        $this->_cropWidth  = $cropWidth;
        $this->_cropHeight = $cropHeight;
    }


    /**
     * Get macro parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return array(
            'width'      => $this->_width,
            'height'     => $this->_height,
            'cropWidth'  => $this->_cropWidth,
            'cropHeight' => $this->_cropHeight,
        );
    }


    /**
     * Run
     *
     * @param  Replica_Image_Abstract $image
     * @return void
     */
    protected function _doRun(Replica_Image_Abstract $image)
    {
        // Check source size
        $sourceWidth  = $image->getWidth();
        $sourceHeight = $image->getHeight();
        if ($sourceWidth >= $this->_width && $sourceHeight >= $this->_height) {

            $ratioSource = $sourceWidth / $sourceHeight;
            $ratioTarget = $this->_width / $this->_height;

            // Resize width
            if ($ratioSource < $ratioTarget) {
                $image->scale($this->_width);

            // Resize height
            } else {
                $image->scale(null, $this->_height);
            }
        }


        // Crop
        $sourceWidth  = $image->getWidth();
        $sourceHeight = $image->getHeight();
        if ($sourceWidth > $this->_width || $sourceHeight > $this->_height) {

            switch ($this->_cropWidth) {
                case self::POS_CENTER:
                    $leftX = round(($sourceWidth - $this->_width) / 2);
                    break;
                case self::POS_RIGHT:
                    $leftX = $sourceWidth - $this->_width;
                    break;
                case self::POS_LEFT:
                default:
                    $leftX = 0;
            }

            switch ($this->_cropHeight) {
                case self::POS_CENTER:
                    $leftY = round(($sourceHeight - $this->_height) / 2);
                    break;
                case self::POS_BOTTOM:
                    $leftY = $sourceHeight - $this->_height;
                    break;
                case self::POS_TOP:
                default:
                    $leftY = 0;
            }

            $image->crop($leftX, $leftY, $this->_width, $this->_height);
        }
    }

}
