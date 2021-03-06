<?php
/**
 * SmartOSC Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * 
 * @category   SM
 * @package    SM_Barcode
 * @version    2.0
 * @author     hoadx@smartosc.com
 * @copyright  Copyright (c) 2010-2011 SmartOSC Co. (http://www.smartosc.com)
 */
class SM_Barcode_Block_Adminhtml_Return_Grid_Renderer_Valid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $img_valid = '';
        return '<div id="qty-valid-'.$row->getProductId().'" style="text-align:center;">'.$img_valid.'</div>';
    }
}
 
