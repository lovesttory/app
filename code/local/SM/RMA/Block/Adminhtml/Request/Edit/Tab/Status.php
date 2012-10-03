<?php

class SM_RMA_Block_Adminhtml_Request_Edit_Tab_Status extends Mage_Adminhtml_Block_Widget_Form{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
        
        $fieldset = $form->addFieldset('request_options', array('legend'=>Mage::helper('rma')->__('Request Options')));
        
        $fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('rma')->__('Set Status To'),
			'name'		=> 'status',
            'options'   => Mage::getModel('rma/request')->getAllStatuses(),
		));

        $fieldset->addField('set_status_only', 'checkbox', array(
				//'required'  => true,
				'label'     => Mage::helper('rma')->__('Set Status Only'),
				'name'      => 'set_status_only'
			));
        
        $request_id = $this->getRequest()->getParam('id');
        $request = Mage::getSingleton('rma/request')->load($request_id);
        $form->setValues(array_merge(
                            $request->getData(),
                            array(  'id'=>$request_id,
                                    'rma_id'=>$request_id,
                                    'package_opened'=>$request->getPackageOpened()?'Yes':'No',
                                )
                            )
                        );
        
		return parent::_prepareForm();
	}
}
