<?php
/**
 * Return / Exchange Pdf default items renderer
 *
 * @category   GSC
 * @package    GSC_Orderslips
 */
class GSC_Orderslips_Model_Order_Pdf_Items_Order_Rma extends GSC_Orderslips_Model_Order_Pdf_Items_Abstract
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

        // Reason Code
        $page->drawText(Mage::helper('sales')->__('_______'), 65, $pdf->y-$shift[0], 'UTF-8');

        // Return Box
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.95));
        $page->drawRectangle(26, $pdf->y-1, 36, $pdf->y+9);

        // Exchange Box
        $page->drawRectangle(44, $pdf->y-1, 54, $pdf->y+9);

        // Reset color to Black
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

        // Qty
        $page->drawText($item->getQtyOrdered()*1, 490, $pdf->y-$shift[0], 'UTF-8');

        // Product Name
        /* in case Product name is longer than 95 chars - it is written in a few lines */
        foreach (Mage::helper('core/string')->str_split($item->getName(), 60, true, true) as $key => $part) {
            $page->drawText($part, 105, $pdf->y-$shift[0], 'UTF-8');
            $shift[0] += 10;
        }

        // Product Options
        $options = $this->getItemOptions();
        if (isset($options)) {
            foreach ($options as $option) {
                // draw options label
                foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 60, false, true) as $_option) {
                    $this->_setFontBold();
                    $page->drawText($_option . ":", 105, $pdf->y-$shift[0], 'UTF-8');
                    $shift[0] += 5;
                }
                // draw options value
                if ($option['value']) {
                    $this->_setFontRegular();
                    $values = explode(', ', strip_tags($option['value']));
                    foreach ($values as $value) {
                        foreach (Mage::helper('core/string')->str_split($value, 60,true,true) as $_value) {
                            $page->drawText($_value, 130, $pdf->y-$shift[0]+5, 'UTF-8');
                            $shift[0] += 5;
                        }
                    }
                }
            }
        }

        // Product Description
        foreach ($this->_parseDescription() as $description){
            $page->drawText(strip_tags($description), 105, $pdf->y-$shift[1], 'UTF-8');
            $shift[1] += 10;
        }

        // Product Price
        $font = $this->_setFontBold();
        $price = $order->formatPriceTxt($item->getPrice());
        $page->drawText($price, 566-$pdf->widthForStringUsingFontSize($price, $font, 7), $pdf->y-$shift[2], 'UTF-8');

        $this->_setFontRegular();
        // Product SKU
        /* in case Product SKU is longer than 36 chars - it is written in a few lines */
        foreach (Mage::helper('core/string')->str_split($this->getSku($item), 25) as $key => $part) {
            if ($key > 0) {
                $shift[2] += 10;
            }
            $page->drawText($part, 350, $pdf->y-$shift[2], 'UTF-8');
        }

        

        //$row_total = $order->formatPriceTxt($item->getRowTotal());
        //$page->drawText($row_total, 565-$pdf->widthForStringUsingFontSize($row_total, $font, 7), $pdf->y, 'UTF-8');



        //$tax = $order->formatPriceTxt($item->getTaxAmount());
        //$page->drawText($tax, 495-$pdf->widthForStringUsingFontSize($tax, $font, 7), $pdf->y, 'UTF-8');

        $pdf->y -= max($shift)+10; // Amount of space between each Set of details
    }
    
}