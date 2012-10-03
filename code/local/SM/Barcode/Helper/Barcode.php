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
class SM_Barcode_Helper_Barcode extends SM_Barcode_Helper_Abstract {

    protected $_width = 330;
    protected $_height = 120;
    protected $_barcodeWidth = 330;
    protected $_barcodeHeight = 120;
    protected $_logo = null;
    protected $_y = 0;
    protected $font;
    protected $bfont;

    protected $finalWidth;
    protected $finalHeight;

    protected function _initProductBarcode() {
        if (Mage::getStoreConfig("barcode/product/bcodelayout") == 0 ) {

            $this->_width = intval(Mage::getStoreConfig("barcode/product/width")) > 0 ? intval(Mage::getStoreConfig("barcode/product/width")) : 330;
            $this->_height = intval(Mage::getStoreConfig("barcode/product/height")) > 0 ? intval(Mage::getStoreConfig("barcode/product/height")) : 200;
            $this->_barcodeWidth = intval(Mage::getStoreConfig("barcode/product/barcode_width")) > 0 ? intval(Mage::getStoreConfig("barcode/product/barcode_width")) : 180;    //150
            $this->_barcodeHeight = Mage::getStoreConfig("barcode/product/barcode_height") > 0 ? $this->_barcodeHeight = Mage::getStoreConfig("barcode/product/barcode_height") : 50;                          //45
        } else {

            $this->_barcodeWidth = 180;
            $this->_barcodeHeight = 50;
        }

        $this->finalHeight = 0;
        $this->finalHeight += 10; // for padding bottom

        $this->finalWidth = 0;
        $this->finalWidth += 10; // for padding right
        $this->finalWidth += 20; //without using font


        $ifont = 4;  // actual font when font are disable on backend

        if (Mage::getStoreConfig('barcode/product/use_font_for_text')) {
            $ifont = Mage::getStoreConfig('barcode/product/font_size');
            $this->finalWidth -= 20;
        }
        else {
             $this->finalHeight += $ifont * 2;
        }

        if (Mage::getStoreConfig("barcode/product/name_visible")){
            $this->finalHeight += $ifont*1.5;
        }

        if (Mage::getStoreConfig("barcode/product/new_slot_visible1") != '') {
            $this->finalHeight += $ifont*1.5;
        }

        if (Mage::getStoreConfig("barcode/product/new_slot_visible2") != '') {
            $this->finalHeight += $ifont*1.5;
        }

        if (Mage::getStoreConfig("barcode/product/new_slot_visible3") != '') {
            $this->finalHeight += $ifont*1.5;
        }

        if (Mage::getStoreConfig("barcode/product/new_slot_visible4") != '') {
            $this->finalHeight += $ifont*1.5;
        }

        if (Mage::getStoreConfig("barcode/product/price_visible")) {
            $this->finalHeight += $ifont*1.5;
        }

        if (Mage::getStoreConfig("barcode/product/text_padding_top")) {
            $this->finalHeight += Mage::getStoreConfig("barcode/product/text_padding_top");
        }


//        $this->finalWidth += $this->_barcodeWidth;
        $this->finalWidth += $ifont*($ifont-8); // for name display



        if (Mage::getStoreConfig("barcode/product/include_logo") && strlen(Mage::getStoreConfig("barcode/product/logo_image_file")) > 0) {
            $logoFile = is_file("media/barcode/" . Mage::getStoreConfig("barcode/product/logo_image_file")) ? "media/barcode/" . Mage::getStoreConfig("barcode/product/logo_image_file") : "barcode/logo.png";
            if (!is_file($logoFile)) {
//Mage::app()->_getSession()->addError($this->__("Please, config a logo file."));
//Mage::app()->_redirect("adminhtml/system_config/edit/section/barcode");
                return false;
            }

            $logoSize = getimagesize($logoFile);
            $this->_logo["file"] = $logoFile;
            $this->_logo["file_width"] = intval($logoSize["0"]);
            $this->_logo["file_height"] = intval($logoSize["1"]);




            if (Mage::getStoreConfig("barcode/product/bcodelayout") == 0) {
                $this->_logo["width"] = intval(Mage::getStoreConfig("barcode/product/logo_width")) > 0 ? intval(Mage::getStoreConfig("barcode/product/logo_width")) : $logoSize["0"];
                $this->_logo["height"] = intval(Mage::getStoreConfig("barcode/product/logo_height")) > 0 ? intval(Mage::getStoreConfig("barcode/product/logo_height")) : $logoSize["1"];
            }
            else{
                $this->_logo["width"] = $logoSize["0"];
                $this->_logo["height"] = $logoSize["1"];
            }


            // check if posible to display logo
            if ($this->_logo["height"] < 0 || $this->_logo["width"] < 0)
                $this->_logo["height"] = $this->_logo["width"] = 0;

            $this->_logo["position"] = Mage::getStoreConfig("barcode/product/logo_position");
            $this->_logo["resize"] = false;
            if ($this->_logo["width"] != $this->_logo["file_width"] || $this->_logo["height"] != $this->_logo["file_height"]) {
                $this->_logo["resize"] = true;
            }
            // Recalculate follow by position of logo



            if (Mage::getStoreConfig("barcode/product/logo_position") == "0" || Mage::getStoreConfig("barcode/product/logo_position") == "3" ) {
                if (Mage::getStoreConfig("barcode/product/logo_position") == "3" )  $this->finalHeight += 25; // for more padding
                if (Mage::getStoreConfig("barcode/product/logo_position") == "0")   $this->finalHeight += 15;
                $this->finalWidth +=  $this->_barcodeWidth > $this->_logo["width"] ? $this->_barcodeWidth :  $this->_logo["width"];

            }elseif (Mage::getStoreConfig("barcode/product/logo_position") == "1"  || Mage::getStoreConfig("barcode/product/logo_position") == "2" ) {
                $this->finalHeight += 20;
                $this->finalWidth +=  $this->_barcodeWidth + $this->_logo["width"];
            }
        }

        else {

             $this->finalWidth += $this->_barcodeWidth;
             $this->finalHeight += 25;
        }
    }

    public function createProductBarcode($productId) {
        $dir = Mage::getBaseDir("media") . DS . "barcode" . DS;
        if (!is_dir_writeable($dir)) {
            $file = new Varien_Io_File;
            $file->checkAndCreateFolder($dir);
        }

        $product = Mage::getModel("catalog/product")->load($productId);
        if (!$product->getId())
            return false;

// Creating some Color (arguments are R, G, B)
        $color_black = new FColor(0, 0, 0);
        $color_white = new FColor(255, 255, 255);

        /* Here is the list of the arguments:
          1 - Thickness
          2 - Color of bars
          3 - Color of spaces
          4 - Resolution
          5 - Text
          6 - Text Font (0-5) */
        if (intval(Mage::getStoreConfig("barcode/product/conversion") == 1)):
            switch (intval(Mage::getStoreConfig("barcode/product/barcode_field"))) {
                case 0:
                    $field = str_pad($productId, 12, "0", STR_PAD_LEFT);
                    break;
                case 1:
                    $field = substr(number_format(hexdec(substr(md5($product->getSku()), 0, 16)), 0, "", ""), 0, 12);
                    break;
                case 2:
                    $attr_id = Mage::getStoreConfig("barcode/product/barcode_source");
                    $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();
                    $attr_val = $product->getResource()->getAttribute($attr)->getFrontend()->getValue($product);
                    $field = substr(number_format(hexdec(substr(md5($attr_val), 0, 16)), 0, "", ""), 0, 12);
                    break;
            }
        else:
            $attr_id = Mage::getStoreConfig("barcode/product/barcode_value");
            $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();
            $attr_val = $product->getResource()->getAttribute($attr)->getFrontend()->getValue($product);
            $field = $attr_val;

        endif;

        switch (intval(Mage::getStoreConfig("barcode/general/symbology"))) {
            case 1:
                $code_generated = new code128(30, $color_black, $color_white, 1, $field, 3, "A");
                break;
            case 2:
                $code_generated = new code128(30, $color_black, $color_white, 1, $field, 3, "B");
                break;
            case 3:
                $code_generated = new code128(30, $color_black, $color_white, 1, $field, 3, "C");
                break;
            case 4:
                $code_generated = new code39(30, $color_black, $color_white, 1, $field, 3);
                break;
            case 5:
                $code_generated = new i25(30, $color_black, $color_white, 1, $field, 3);
                break;
            default:
                $code_generated = new ean13(30, $color_black, $color_white, 1, $field, 3);
                break;
        }

        $this->_initProductBarcode();
        /* Here is the list of the arguments
          1 - Width
          2 - Height
          3 - Filename (empty : display on screen)
          4 - Background color */
        $path = $dir . $productId . ".png";
        $drawing = new FDrawing($this->_width, $this->finalHeight, $path, $color_white);
        $drawing->init(); // You must call this method to initialize the image
        $drawing->add_barcode($code_generated);
        $drawing->draw_all();

        $im = $drawing->get_im();
        $imgBarcode = imagecreate($this->_barcodeWidth, $this->_barcodeHeight);
        imagecopyresized($imgBarcode, $im, 0, 0, 0, 0, $this->_barcodeWidth, $this->_barcodeHeight, $code_generated->lastX, $code_generated->lastY);
        if (Mage::getStoreConfig("barcode/product/bcodelayout") == 1 )
        $im2 = imagecreate($this->finalWidth, $this->finalHeight);
        else $im2 = imagecreate($this->_width, $this->_height);
        imagecolorallocate($im2, 255, 255, 255);
        $textcolor = imagecolorallocate($im2, 0, 0, 0);
        $this->_y = $this->_barcodeHeight;

//INSERT LOGO
        if ($this->_logo) {

            $logoType = exif_imagetype($this->_logo["file"]);
            if ($logoType == 1) {
                $logoImage = imagecreatefromgif($this->_logo["file"]);
            } elseif ($logoType == 2) {
                $logoImage = imagecreatefromjpeg($this->_logo["file"]);
            } elseif ($logoType == 3) {
                $logoImage = imagecreatefrompng($this->_logo["file"]);
            } else {
//Mage::app()->_getSession()->addError($this->__("The logo file is not valid"));
//Mage::app()->_redirect("adminhtml/system_config/edit/section/barcode");
                return false;
            }

            $logoResized = imagecreatetruecolor($this->_logo["width"], $this->_logo["height"]);
            if ($this->_logo["resize"]) {
                imagecopyresampled($logoResized, $logoImage, 0, 0, 0, 0, $this->_logo["width"], $this->_logo["height"], $this->_logo["file_width"], $this->_logo["file_height"]);
            } else {
                $logoResized = $logoImage;
            }

            switch ($this->_logo["position"]) {
                case 1:  // left
                    imagecopymerge($im2, $imgBarcode, $this->_logo["width"] + 3, 10, 0, 0, $this->_barcodeWidth, $this->_barcodeHeight, 100);
                    imagecopymerge($im2, $logoResized, 0, 0, 0, 0, $this->_logo["width"], $this->_logo["height"], 100);
                    break;
                case 2:  // right
                    imagecopymerge($im2, $imgBarcode, 3, 3, 0, 0, $this->_barcodeWidth, $this->_barcodeHeight, 100);
                    imagecopymerge($im2, $logoResized, $this->_barcodeWidth + 10, 0, 0, 0, $this->_logo["width"], $this->_logo["height"], 100);
                    break;
                case 3:  // bottom
                    imagecopymerge($im2, $imgBarcode, 3, 3, 0, 0, $this->_barcodeWidth, $this->_barcodeHeight, 100);
                    imagecopymerge($im2, $logoResized, 0, $this->_barcodeHeight + 10, 0, 0, $this->_logo["width"], $this->_logo["height"], 100);
                    $this->_y += $this->_logo["height"] + 10;
                    break;
                default: // top
                    imagecopymerge($im2, $imgBarcode, 3, $this->_logo["height"] + 10, 0, 0, $this->_barcodeWidth, $this->_barcodeHeight, 100);
                    imagecopymerge($im2, $logoResized, 0, 0, 0, 0, $this->_logo["width"], $this->_logo["height"], 100);
                    $this->_y += $this->_logo["height"] + 10;
                    break;
            }
        } else {
            imagecopymerge($im2, $imgBarcode, 7, 7, 0, 0, $this->_barcodeWidth, $this->_barcodeHeight, 100);
        }


        // Init information for write text
        $xText = 0;
        $yText = 0;
        $i = 0; // for inscrease content height

        $ifont = 0; // without font

        if (Mage::getStoreConfig('barcode/product/use_font_for_text')) {
            $ifont = Mage::getStoreConfig('barcode/product/font_size') > 0 ? Mage::getStoreConfig('barcode/product/font_size') : 12;
        }
        else {
            $ifont = 9;
        }

//        die(intval($ifont));
        $ifont = intval($ifont)*1.7;




        if (Mage::getStoreConfig("barcode/product/text_padding_left")) {
            $xText = intval(Mage::getStoreConfig("barcode/product/text_padding_left"));
        } else {
            $xText = 0;
        }

        if (Mage::getStoreConfig("barcode/product/text_padding_top")) {
            $yText = intval(Mage::getStoreConfig("barcode/product/text_padding_top"));
        } else {
            $yText = $this->_y + 10;
        }

        $this->font = Mage::getBaseDir("skin") . "/adminhtml/default/default/sm/fonts/arial.ttf";
        $this->bfont =  Mage::getBaseDir("skin") . "/adminhtml/default/default/sm/fonts/barial.ttf";

        if (Mage::getStoreConfig("barcode/product/name_visible")) {
            $productName = $product->getName();
            if (strlen($productName) > 31) {
                $nameLine2 = "";
                $nameLine1 = Mage::helper("core/string")->truncate($productName, 31, '', $nameLine2, false);
            } else {
                $nameLine2 = "";
                $nameLine1 = $productName;
            }

//write product name , manufacturer code and price


            if (Mage::getStoreConfig("barcode/product/use_font_for_text")) {
                if (Mage::getStoreConfig("barcode/product/font_size")) {
                    $fontSize = intval(Mage::getStoreConfig("barcode/product/font_size"));
                } else {
                    $fontSize = 12;
                }
                $yText += 10;
                imagettftext($im2, $fontSize, 0, $xText, $yText, $textcolor,  $this->font, $nameLine1);
                imagettftext($im2, $fontSize, 0, $xText, ($yText + $ifont), $textcolor,  $this->font, $nameLine2);
                $i = $nameLine2 ? $ifont : 0;

            } else {

                imagestring($im2, 4, $xText, $yText,  $nameLine1, $textcolor);
                imagestring($im2, 4, $xText, ($yText + $ifont), $nameLine2, $textcolor);
                $i = $nameLine2 ? $ifont : 0;
            }
        }



        if (Mage::getStoreConfig("barcode/product/new_slot_visible1")) {
            $attr_id = Mage::getStoreConfig("barcode/product/new_slot_visible1");
            $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();
            $attr_val = $product->getResource()->getAttribute($attr)->getFrontend()->getValue($product);
//                    $field = substr(number_format(hexdec(substr(md5($attr_val), 0, 16)), 0, "", ""), 0, 12);
            if ($attr_val != '') {
                if (Mage::getStoreConfig("barcode/product/use_font_for_text")) {
                    if (Mage::getStoreConfig("barcode/product/font_size")) {
                        $fontSize = intval(Mage::getStoreConfig("barcode/product/font_size"));
                    } else {
                        $fontSize = 12;
                    }
//                    $yText += 10;
                    imagettftext($im2, $fontSize, 0, $xText, ($yText + $i +  $ifont), $textcolor,  $this->font, $attr_val);
                    $i += $ifont;
                } else {
                    imagestring($im2, 4, $xText, ($yText + $i +  $ifont),  $attr_val, $textcolor);
                    $i += $ifont;
                }
            }
        }

        if (Mage::getStoreConfig("barcode/product/new_slot_visible2")) {
            $attr_id = Mage::getStoreConfig("barcode/product/new_slot_visible2");
            $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();
            $attr_val = $product->getResource()->getAttribute($attr)->getFrontend()->getValue($product);
//                    $field = substr(number_format(hexdec(substr(md5($attr_val), 0, 16)), 0, "", ""), 0, 12);

            if ($attr_val != '')  {
                if (Mage::getStoreConfig("barcode/product/use_font_for_text")) {
                    if (Mage::getStoreConfig("barcode/product/font_size")) {
                        $fontSize = intval(Mage::getStoreConfig("barcode/product/font_size"));
                    } else {
                        $fontSize = 12;
                    }
//                    $yText += 10;
                    imagettftext($im2, $fontSize, 0, $xText, ($yText + $i +  $ifont), $textcolor,  $this->font, $attr_val);
                    $i += $ifont;
                } else {
                    imagestring($im2, 4, $xText, ($yText + $i +  $ifont),  $attr_val, $textcolor);
                    $i += $ifont;
                }
            }
        }

        if (Mage::getStoreConfig("barcode/product/new_slot_visible3")) {
            $attr_id = Mage::getStoreConfig("barcode/product/new_slot_visible3");
            $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();
            $attr_val = $product->getResource()->getAttribute($attr)->getFrontend()->getValue($product);
//                    $field = substr(number_format(hexdec(substr(md5($attr_val), 0, 16)), 0, "", ""), 0, 12);
            if ($attr_val != '')  {
                if (Mage::getStoreConfig("barcode/product/use_font_for_text")) {
                    if (Mage::getStoreConfig("barcode/product/font_size")) {
                        $fontSize = intval(Mage::getStoreConfig("barcode/product/font_size"));
                    } else {
                        $fontSize = 12;
                    }
//                    $yText += 10;
                    imagettftext($im2, $fontSize, 0, $xText, ($yText + $i +  $ifont), $textcolor,  $this->font, $attr_val);
                    $i += $ifont;
                } else {
                    imagestring($im2, 4, $xText, ($yText + $i +  $ifont),  $attr_val, $textcolor);
                    $i += $ifont;
                }
            }
        }

        if (Mage::getStoreConfig("barcode/product/new_slot_visible4")) {
            $attr_id = Mage::getStoreConfig("barcode/product/new_slot_visible4");
            $attr = Mage::getModel('eav/entity_attribute')->load($attr_id)->getAttributeCode();
            $attr_val = $product->getResource()->getAttribute($attr)->getFrontend()->getValue($product);
//                    $field = substr(number_format(hexdec(substr(md5($attr_val), 0, 16)), 0, "", ""), 0, 12);
            if ($attr_val != '')  {
                if (Mage::getStoreConfig("barcode/product/use_font_for_text")) {
                    if (Mage::getStoreConfig("barcode/product/font_size")) {
                        $fontSize = intval(Mage::getStoreConfig("barcode/product/font_size"));
                    } else {
                        $fontSize = 12;
                    }
//                    $yText += 10;
                    imagettftext($im2, $fontSize, 0, $xText, ($yText + $i +  $ifont), $textcolor,  $this->font, $attr_val);
                    $i += $ifont;
                } else {
                    imagestring($im2, 4, $xText, ($yText + $i +  $ifont),  $attr_val, $textcolor);
                    $i += $ifont;
                }
            }
        }

        if($product->getPrice() && Mage::getStoreConfig("barcode/product/price_visible")) {
            if (Mage::getStoreConfig("barcode/product/use_font_for_text")) {
                if (Mage::getStoreConfig("barcode/product/font_size")) {
                    $fontSize = intval(Mage::getStoreConfig("barcode/product/font_size"));
                } else {
                    $fontSize = 12;
                }

                // if($product->getFinalPrice() != $product->getPrice()) {
                //     $formattedPrice = Mage::helper('core')->currency($product->getPrice(),true,false);
                //     $priceLenght = strlen($formattedPrice);
                //     imagettftext($im2, $fontSize, 0, $xText, ($yText + $i +  $ifont), $textcolor,  $this->font, $formattedPrice);
                //     $i += $ifont;

                //     imageline($im2, 15, $yText + $i - (($fontSize)/2), $priceLenght*(($fontSize+1)/2), $yText + $i - (($fontSize)/2), $textcolor);
                //     $formattedPrice = Mage::helper('core')->currency($product->getFinalPrice(),true,false);
                //     imagettftext($im2, $fontSize, 0, $xText + ($priceLenght*(($fontSize*1.8)/2)), ($yText + $i), $textcolor,  $this->bfont, $formattedPrice);
                //     $i += $ifont;
                // }
                // else {
                    $formattedPrice = Mage::helper('core')->currency($product->getPrice(),true,false);
                    $priceLenght = strlen($formattedPrice);
                    imagettftext($im2, $fontSize, 0, $xText, ($yText + $i + $ifont), $textcolor, $this->bfont, $formattedPrice);
                    $i += $ifont;
                // }
            }
            else {
//                 if($product->getFinalPrice() != $product->getPrice()) {
//                     $formattedPrice = Mage::helper('core')->currency($product->getPrice(),true,false);
//                     $priceLenght = strlen($formattedPrice);
// //                    imagettftext($im2, $fontSize, 0, $xText, ($yText + $i +  30), $textcolor,  $this->font, $formattedPrice);
//                     imagestring($im2, 4, $xText, ($yText + $i +  $ifont),  $formattedPrice, $textcolor);
//                     $i += $ifont;

//                     imageline($im2, 15, $yText + $i + 8, $priceLenght*6, $yText + $i + 8, $textcolor);
//                     $formattedPrice = Mage::helper('core')->currency($product->getFinalPrice(),true,false);
// //                    imagettftext($im2, $fontSize, 0, $xText + ($priceLenght*10), ($yText + $i + 10), $textcolor,  $this->bfont, $formattedPrice);
//                     imagestring($im2, 4, $xText + $priceLenght*11, ($yText + $i),  $formattedPrice, $textcolor);
//                     $i += $ifont;
//                 }
//                 else {
                    $formattedPrice = Mage::helper('core')->currency($product->getPrice(),true,false);
                    $priceLenght = strlen($formattedPrice);
//                    imagettftext($im2, $fontSize, 0, $xText, ($yText + $i +  30), $textcolor, $this->bfont, $formattedPrice);
                    imagestring($im2, 4, $xText, ($yText + $i +  $ifont),$formattedPrice, $textcolor);
                    $i += $ifont;
                // }
            }



        }

// write manufacturer code


// Draw (or save) the image into PNG format.
        $drawing->set_im($im2);
        if ($drawing->finish(IMG_FORMAT_PNG)) {
            return $path;
        }

        return false;
    }

    public function createOrderBarcode($order_id) {
        $dir = Mage::getBaseDir("media") . DS . "barcode" . DS . "order" . DS;
        if (!is_dir_writeable($dir)) {
            $file = new Varien_Io_File;
            $file->checkAndCreateFolder($dir);
        }

// Creating some Color (arguments are R, G, B)
        $color_black = new FColor(0, 0, 0);
        $color_white = new FColor(255, 255, 255);
        $number_of_char = strlen($order_id);
        // to process with editted order, with order_id like 100000005-1 
        if ($number_of_char > 9)
            $order_id = str_replace("-", "", $order_id);

        $field = str_pad($order_id, 12, "0", STR_PAD_LEFT);
        $width = 172;
        $height = 62;
        switch (intval(Mage::getStoreConfig("barcode/general/symbology"))) {
            case 1:
                $code_generated = new code128(30, $color_black, $color_white, 1, $field, 3, "A");
                break;
            case 2:
                $code_generated = new code128(30, $color_black, $color_white, 1, $field, 3, "B");
                break;
            case 3:
                $code_generated = new code128(30, $color_black, $color_white, 1, $field, 3, "C");
                break;
            case 4:
                $code_generated = new code39(30, $color_black, $color_white, 1, $field, 3);
                break;
            case 5:
                $code_generated = new i25(30, $color_black, $color_white, 1, $field, 3);
                break;
            default:
                $code_generated = new ean13(30, $color_black, $color_white, 1, $field, 3);
                $width = 110;
                break;
        }

        /* Here is the list of the arguments
          1 - Width
          2 - Height
          3 - Filename (empty : display on screen)
          4 - Background color */
        $path = $dir . $order_id . ".png";
        $imageUrl = Mage::getBaseUrl("media") . DS . "barcode" . DS . "order" . DS . $order_id . ".png";
        $drawing = new FDrawing($width, $height, $path, $color_white);
        $drawing->init(); // You must call this method to initialize the image
        $drawing->add_barcode($code_generated);
        $drawing->draw_all();
        $im = $drawing->get_im();

// Next line create the little picture, the barcode is being copied inside
//		$im2 = imagecreate(330,120);
//
//		imagecopyresized($im2, $im, 189, 10, 0, 0, $code_generated->lastX, $code_generated->lastY, $code_generated->lastX, $code_generated->lastY);
// Draw (or save) the image into PNG format.
        $drawing->finish(IMG_FORMAT_PNG);
        return $path;
    }

}

;

