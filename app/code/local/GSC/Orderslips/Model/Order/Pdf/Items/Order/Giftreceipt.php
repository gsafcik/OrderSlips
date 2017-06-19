<?php
/**
 * Gift Receipt Pdf default items renderer
 *
 * @category   GSC
 * @package    GSC_Orderslips
 */
class GSC_Orderslips_Model_Order_Pdf_Items_Order_Giftreceipt extends GSC_Orderslips_Model_Order_Pdf_Items_Abstract
{
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $shift  = array(0, 10, 0);

        //$pdf->y = 500;
        $this->_setFontRegular();

        // Reset color to Black
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

        // Qty
        $page->drawText($item->getQtyOrdered()*1, 530, $pdf->y-$shift[0], 'UTF-8');

        // Product Name
        /* in case Product name is longer than 60 chars - it is written in a few lines */
        foreach (Mage::helper('core/string')->str_split($item->getName(), 60, true, true) as $key => $part) {
            $page->drawText($part, 35, $pdf->y-$shift[0], 'UTF-8');
            $shift[0] += 10;
        }

        // Product Options
        $options = $this->getItemOptions();
        if (isset($options)) {
            foreach ($options as $option) {
                // draw options label
                foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 60, false, true) as $_option) {
                    $this->_setFontBold();
                    $page->drawText($_option . ":", 35, $pdf->y-$shift[0], 'UTF-8');
                    $shift[0] += 5;
                }
                // draw options value
                if ($option['value']) {
                    $this->_setFontRegular();
                    $values = explode(', ', strip_tags($option['value']));
                    foreach ($values as $value) {
                        foreach (Mage::helper('core/string')->str_split($value, 60,true,true) as $_value) {
                            $page->drawText($_value, 60, $pdf->y-$shift[0]+5, 'UTF-8');
                            $shift[0] += 5;
                        }
                    }
                }
            }
        }

        // Product Description
        foreach ($this->_parseDescription() as $description){
            $page->drawText(strip_tags($description), 35, $pdf->y-$shift[1], 'UTF-8');
            $shift[1] += 10;
        }

        $this->_setFontRegular();
        // Product SKU
        /* in case Product SKU is longer than 60 chars - it is written in a few lines */
        foreach (Mage::helper('core/string')->str_split($this->getSku($item), 60) as $key => $part) {
            if ($key > 0) {
                $shift[2] += 10;
            }
            $page->drawText($part, 255, $pdf->y-$shift[2], 'UTF-8');
        }

        $pdf->y -= max($shift)+10; // Amount of space between each Set of details
    }
    
}