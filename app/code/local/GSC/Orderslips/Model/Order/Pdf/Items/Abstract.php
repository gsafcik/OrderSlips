<?php
/**
 * Orderslips Pdf Items renderer Abstract
 *
 * @category   GSC
 * @package    GSC_Orderslips
 */
abstract class GSC_Orderslips_Model_Order_Pdf_Items_Abstract extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{

    public function getItemOptions() {
        $result = array();
        if ($options = $this->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }


    public function getSku($item)
    {
        if ($this->getOrderItem($item)->getProductOptionByCode('simple_sku'))
            return $this->getOrderItem($item)->getProductOptionByCode('simple_sku');
        else
            return $item->getSku();
    }
    
    protected function getOrderItem($item = null) {
    	if($item instanceof Mage_Sales_Model_Order_Item) {
    		return $item;
    	}
    	if($item !== null) {
    		return $item->getOrderItem();
    	}
    	
    	if($this->getItem() instanceof Mage_Sales_Model_Order_Item) {
    		return $this->getItem();
    	}
    	return $this->getItem()->getOrderItem();
    }


    protected function _setFontRegular($size = 7.5)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    protected function _setFontBold($size = 7.5)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    protected function _setFontItalic($size = 7.5)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD_ITALIC);
        $this->getPage()->setFont($font, $size);
        return $font;
    }

}