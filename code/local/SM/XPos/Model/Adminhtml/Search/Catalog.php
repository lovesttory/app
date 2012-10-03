<?php

class SM_XPos_Model_Adminhtml_Search_Catalog extends Mage_Adminhtml_Model_Search_Catalog {

    /**
     * Load search results
     *
     * @return Mage_Adminhtml_Model_Search_Catalog
     */
    public function load() {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::helper('catalogsearch')->getQuery()->getSearchCollection()
                ->addAttributeToSelect('*')
                ->addTaxPercents()
                ->setCurPage($this->getStart())
                ->setPageSize($this->getLimit());

        $_searching_order = Mage::getStoreConfig('xpos/general/order');
        $_is_loaded = false;
        switch ($_searching_order) {
            case 'id':
                if (is_numeric($this->getQuery())) {
                    $collection = $collection->addFieldToFilter('entity_id', array('eq' => $this->getQuery()))
                            ->load();
                    $_is_loaded = true;
                }
                break;
            case 'barcode':
                if (is_numeric($this->getQuery()) && (strlen($this->getQuery()) == 12) || (strlen($this->getQuery()) == 13)) {
                    switch (intval(Mage::getStoreConfig("barcode/product/barcode_field"))) {
                        case 0: // ID
                            $id = intval($this->getQuery()/10);
                            $collection = $collection->addFieldToFilter('entity_id', array('eq' => $id))
                                ->load();
                            $_is_loaded = true;
                            break;
                        case 1: // SKU

                            $sku = $this->getSkuFromBarcode($this->getQuery());
                            $collection = $collection->addFieldToFilter('sku', array('like' => '%' . $sku . '%'))
                                ->load();
                            $_is_loaded = true;
                            break;
                        default:
                            $attr_val = $this->getAttFromBarcode($this->getQuery());
                            $attr_id = Mage::getStoreConfig("barcode/product/barcode_source");
                            $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();

                            $collection = $collection->addFieldToFilter($attr, array('like' => '%' . $attr_val . '%'))
                                ->load();
                            $_is_loaded = true;

                    }
                }
                break;
            default: // case 'sku'

                $collection = $collection->addFieldToFilter('sku', array('like' => '%' . $this->getQuery() . '%'))
                        ->load();
                $_is_loaded = true;
                break;
        }

        if (!$_is_loaded || !$collection || !$collection->getSize()) {
            $collection = Mage::helper('catalogsearch')->getQuery()->getSearchCollection()
                    ->addAttributeToSelect('*')
                    ->addTaxPercents()
                    ->setCurPage($this->getStart())
                    ->setPageSize($this->getLimit())
                    ->addSearchFilter($this->getQuery())
                    ->load();
        }
        if ($collection->requireTaxPercent()) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest();
            foreach ($collection as $item) {
                if (null === $item->getTaxClassId()) {
                    $item->setTaxClassId($item->getMinimalTaxClassId());
                }
                if (!isset($classToRate[$item->getTaxClassId()])) {
                    $request->setProductClassId($item->getTaxClassId());
                    $classToRate[$item->getTaxClassId()] = Mage::getSingleton('tax/calculation')->getRate($request);
                }
                $item->setTaxPercent($classToRate[$item->getTaxClassId()]);
            }
        }
        foreach ($collection as $product) {
            // Get the product's tax class' ID
            $taxClassId = $product->getData("tax_class_id");
            // Get the tax rates of each tax class in an associative array
            $taxClasses = Mage::helper("core")->jsonDecode(Mage::helper("tax")->getAllRatesByProductClass());
            // Extract the tax rate from the array
            $taxRate = $taxClasses["value_" . $taxClassId];
            $description = strip_tags($product->getDescription());
            $arr[] = array(
                'id' => $product->getId(),
                'type' => Mage::helper('adminhtml')->__('Product'),
                'name' => $product->getName(),
                'tax' => $taxRate,
                //$product->getTaxPercent(),                
                'sku' => $product->getSku(),
                'price' => $product->getFinalPrice(),
                'description' => Mage::helper('core/string')->substr($description, 0, 30),
                'url' => Mage::helper('adminhtml')->getUrl('*/catalog_product/edit', array('id' => $product->getId())),
            );
        }

        $this->setResults($arr);

        return $this;
    }

    public function loadAll($limit, $page) {
        $arr = array();
        $collection = //Mage::helper('catalogsearch')->getQuery()->getSearchCollection()
                Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addTaxPercents();

        $collection = $collection->setCurPage($page)
                        ->setPageSize($limit)->load();
        // not needed since we checked with JS
        $t = ceil($collection->getSize() / $limit);
        if ($page > $t)
            return null;
/*
        if ($collection->requireTaxPercent()) {

            $request = Mage::getSingleton('tax/calculation')->getRateRequest();
            foreach ($collection as $item) {
                if (null === $item->getTaxClassId()) {
                    $item->setTaxClassId($item->getMinimalTaxClassId());
                }
                if (!isset($classToRate[$item->getTaxClassId()])) {
                    $request->setProductClassId($item->getTaxClassId());
                    $classToRate[$item->getTaxClassId()] = Mage::getSingleton('tax/calculation')->getRate($request);
                }
                $item->setTaxPercent($classToRate[$item->getTaxClassId()]);
            }
        }*/
        foreach ($collection as $product) {
            $description = strip_tags($product->getDescription());
            // Get the product's tax class' ID
            $taxClassId = $product->getData("tax_class_id");
            // Get the tax rates of each tax class in an associative array
            $taxClasses = Mage::helper("core")->jsonDecode(Mage::helper("tax")->getAllRatesByProductClass());
            // Extract the tax rate from the array
            $taxRate = $taxClasses["value_" . $taxClassId];
            // Get Barcode
            $barcode = array();
            switch (intval(Mage::getStoreConfig("barcode/product/barcode_field"))) {
                case 0: // ID
                    $field = str_pad($product->getId(), 12, "0", STR_PAD_LEFT);
                    break;
                case 1:
                    $field = substr(number_format(hexdec(substr(md5($product->getSku()), 0, 16)), 0, "", ""), 0, 12);
                    break;
                default:
                $attr_id = Mage::getStoreConfig("barcode/product/barcode_source");
                $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();
                $attr_val = $product->getResource()->getAttribute($attr)->getFrontend()->getValue($product);
                    $field = substr(number_format(hexdec(substr(md5($attr_val), 0, 16)), 0, "", ""), 0, 12);

            }

            for($i = 0;$i <=9;$i++ ) {
                $barcode[] = $field*10 + $i;
            }

            $arr[] = array(
                'id' => $product->getId(),
                'type' => Mage::helper('adminhtml')->__('Product'),
                'name' => $product->getName(),
                'tax' => $taxRate, //$product->getTaxPercent(),
                'sku' => $product->getSku(),
                'barcode' => $barcode,
                'price' => $product->getFinalPrice(),
                'description' => Mage::helper('core/string')->substr($description, 0, 30),
                'url' => Mage::helper('adminhtml')->getUrl('*/catalog_product/edit', array('id' => $product->getId())),
            );
        }

        $this->setResults($arr);

        return $this;
    }

    public function getSkuFromBarcode($barcode) {
        $products = Mage::getModel('catalog/product')->getCollection();
        foreach($products as $product) {
            $field = substr(number_format(hexdec(substr(md5($product->getSku()), 0, 16)), 0, "", ""), 0, 12);
            if ($barcode/10 > $field && ($barcode/10) - 1 < $field) {
                return $product->getSku();
            }
        }
        return false;
    }

    public function getAttFromBarcode($barcode) {
        $products = Mage::getModel('catalog/product')->getCollection();
        foreach($products as $product) {
            $attr_id = Mage::getStoreConfig("barcode/product/barcode_source");
            $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();
            $attr_val = $product->getResource()->getAttribute($attr)->getFrontend()->getValue($product);
            $field = substr(number_format(hexdec(substr(md5($attr_val), 0, 16)), 0, "", ""), 0, 12);

            if ($barcode/10 > $field && ($barcode/10) - 1 < $field) {
                return $attr_val;
            }
        }
        return false;
    }

    public function getBarcode($input) {
        $barcode = array();
        switch (intval(Mage::getStoreConfig("barcode/product/barcode_field"))) {
            case 0: // ID
                $field = str_pad($input, 12, "0", STR_PAD_LEFT);
                break;
            case 1:
                $field = substr(number_format(hexdec(substr(md5($input), 0, 16)), 0, "", ""), 0, 12);
                break;
            default:
//                $attr_id = Mage::getStoreConfig("barcode/product/barcode_source");
//                $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();
//                $attr_val = $product->getResource()->getAttribute($attr)->getFrontend()->getValue($product);
                $field = substr(number_format(hexdec(substr(md5($input), 0, 16)), 0, "", ""), 0, 12);

        }

        for($i = 0;$i <=9;$i++ ) {
            $barcode[] = $field*10 + $i;
        }

    }


}
