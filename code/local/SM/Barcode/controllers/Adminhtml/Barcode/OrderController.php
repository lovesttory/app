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
class SM_Barcode_Adminhtml_Barcode_OrderController extends SM_Barcode_Controller_Adminhtml_Action {

    public function indexAction() {
        if ($this->_validated) {
            $this->loadLayout()
                    ->_setActiveMenu('barcode/manage')
                    ->renderLayout();
        }
    }

    public function gridAction() {
        $this->loadLayout(false);
        $this->renderLayout();
    }

}

