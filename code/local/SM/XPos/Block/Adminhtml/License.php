<?php
class SM_XPos_Block_Adminhtml_License extends Mage_Adminhtml_Block_Template{
    public function __construct(){
        $this->_blockGroup = 'xpos';
		$this->_controller = 'adminhtml_license';

		parent::__construct();
        
        $this->setTemplate('sm/xpos/license.phtml');
    }
    
}
 
