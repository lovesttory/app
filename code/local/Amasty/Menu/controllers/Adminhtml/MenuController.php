<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Adminhtml_MenuController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('ammenu/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Manager'), Mage::helper('adminhtml')->__('Menu Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		
		$this->_title($this->__('Flexible Menu'))
             ->_title($this->__('Manage Content'));
		
		$this->_initAction();	 
        $this->_addContent($this->getLayout()->createBlock('ammenu/adminhtml_menu')); 
 	    $this->renderLayout();              
             
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('ammenu/menu')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('menu_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('ammenu/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('ammenu/adminhtml_menu_edit'))
				->_addLeft($this->getLayout()->createBlock('ammenu/adminhtml_menu_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ammenu')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
    /**
     * Get list of menu items and return it as options for <select>
     */
	public function menutreeAction()
    {
    	/* @var $menuModel Amasty_Menu_Model_Menu */
        $menuModel = Mage::getModel('ammenu/menu');
        
        if ($this->getRequest()->isPost()) {
            $stores = $this->getRequest()->getParam('stores');
        }
        
        $pages = $menuModel->getPagesTree($stores);
        
        $html = "";
        foreach ($pages as $page) {
            $html .= '<option value="' . $page->menu_id . '">' .  str_repeat('--', $page->level) . Mage::helper('ammenu')->getMenuItemName($page) . '</option>';   
        }
        
        $this->getResponse()->setBody($html);
        
    }
    
    /**
     * Get list of pages and return it as options for <select>
     */
    public function pagesAction()
    {
        if ($this->getRequest()->isPost()) {
            $stores = $this->getRequest()->getParam('stores');
        }
         
        $page = Mage::getSingleton('cms/page');
        $pages = $page->getCollection()->addStoreFilter($stores);
        
        $html = "";
        foreach ($pages as $page) {
            $html .= '<option value="' . $page->getId() . '">' . $page->getTitle() . '</option>';   
        }
        
        $this->getResponse()->setBody($html);
    }
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
		    
			$model = Mage::getModel('ammenu/menu');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
			    
				$model->modified = time();		
				$model->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ammenu')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ammenu')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('ammenu/menu');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $menuIds = $this->getRequest()->getParam('menu');
        if(!is_array($menuIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($menuIds as $menuId) {
                    $menu = Mage::getModel('ammenu/menu')->load($menuId);
                    $menu->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($menuIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
	
    public function massStatusAction()
    {
        $menuIds = $this->getRequest()->getParam('menu');
        if(!is_array($menuIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($menuIds as $menuId) {
                    $menu = Mage::getSingleton('ammenu/menu')
                        ->load($menuId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($menuIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'menu.csv';
        $content    = $this->getLayout()->createBlock('ammenu/adminhtml_menu_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'menu.xml';
        $content    = $this->getLayout()->createBlock('ammenu/adminhtml_menu_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}