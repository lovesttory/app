<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 7/30/12
 * Time: 9:44 AM
 * To change this template use File | Settings | File Templates.
 */


Class SM_XPos_Block_Adminhtml_Catalog_Product_Source extends Mage_Adminhtml_Block_System_Config_Form_Field {


    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $storeid = Mage::getStoreConfig('xpos/general/storeid');
        $queryLastId = "SELECT increment_last_id FROM  eav_entity_store
          inner join eav_entity_type on eav_entity_type.entity_type_id = eav_entity_store.entity_type_id
          and eav_entity_type.entity_type_code='order' and eav_entity_store.store_id = {$storeid}";

        $lastid = $readConnection->fetchOne($queryLastId);

        return '<input type="text" id="lastorderid" class="input-text" value="'.$lastid.'" disabled="disabled" />';

    }
}
?>

