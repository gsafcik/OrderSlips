<?php
/**
 * Return / Exchange Sales PDF model
 *
 * @package    GSC_Orderslips
 *
 * TODO:
 *  - Add option for more rows in Exchange table (with maximum amount)?
 *  - Limit Return/Exchange Labels in Admin to 9 maximum (disable button?)
 *  - Fix direct logic in Numbertypes.php for number_setting. It does not work in Numbertypes.php. Then remove switch case from this file
 *  - 
 *  - 
 */
class GSC_Orderslips_Model_Order_Pdf_Order extends GSC_Orderslips_Model_Order_Pdf_Rma
{	
    public function getPdf($orders = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('order');

        $pdf = new Zend_Pdf();
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($orders as $order) {
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_LETTER);
            $pdf->pages[] = $page;

            /* Add image */
            $this->insertLogo($page, $order->getStore());

            /* Add address */
            $this->insertAddress($page, $order->getStore());

            /* Add head */
            $this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId()));


            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page);

            /* Return/Exchange Codes */
            $this->y = 630;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.9));
            $page->drawRectangle(25, $this->y, 585, $this->y-65);

            $page->drawLine(380, $this->y, 380, $this->y - 40); // Separator Line, before R or E Label

            $this->_setFontRegular($page, 6.7); 
            
            // X & Y Positions of Titles

            $titleY = $this->y -= 10; // Y Position of Title

            $title2Y = $titleY; // Y Position of Title
            
            $line = $this->y -= 10; // set the First Y Position line here
            $separation = 9; // Line/Row Height

             // $spacer set below (for dynamic count) - Space between Codes & Descriptions
            $spacer_cust = Mage::getStoreConfig('orderslips/rma_inputs/spacer_add');
            
            $col1X = 35; // Column 1 X position
            $col2X = 130; // Column 2 X position
            $col3X = 250; // Column 3 X position
            $col4X = 390; // Column 4 X position
            
            // Line Heights
            $lineHeight1 = $separation * 1;
            $lineHeight2 = $separation * 2;

            $page->setFillColor(new Zend_Pdf_Color_Html('#343434'));
            
            // Return/Exchange Code Heading

            $this->_setFontBold($page, 7); 
            $page->drawText(Mage::helper('sales')->__('Reason for Return/Exchange Codes (Code):'), 30, $titleY, 'UTF-8');

            // Return/Exchange Code Labels

            $this->_setFontBold($page, 6.7); 

            $number_setting = Mage::getStoreConfig('orderslips/rma_inputs/num_setting');

            // TODO: direct logic in Numbertypes.php does not work. This is why the switch case is here
            switch ($number_setting) {
                case 'nt1':
                    $number_setting = "";
                    break;
                case 'nt2':
                    $number_setting = "0";
                    break;
                case 'nt3':
                    $number_setting = "00";
                    break;
                case 'nt4':
                    $number_setting = "A";
                    break;
                case 'nt5':
                    $number_setting = "A0";
                    break;

            }

            $codelabels = unserialize(Mage::getStoreConfig('orderslips/rma_inputs/labels'));
            $numberCount = 1;
            foreach ($codelabels as $key => $value) {
                //print_r($value);
                foreach ($value as $cola) {
                    $colaCount = count($cola);
                    if($colaCount > 0) {
                        if($numberCount > 0 && $numberCount <= 3) {
                            $column_position = $col1X;
                        }
                        if($numberCount >= 4 && $numberCount <= 6) {
                            $column_position = $col2X;  
                            if($numberCount == 4) {
                                $line = 610; // reset y value to align tops of columns
                            }
                        }
                        if($numberCount >= 7 && $numberCount <= 9) {
                            $column_position = $col3X;
                            if($numberCount == 7) {
                                $line = 610; // reset y value to align tops of columns
                            }
                        }
                        if($numberCount >= 10) {
                            $cola = '';
                            break;
                        }

                        $countspace = strlen($number_setting)+1; // Counts number_setting length above, then adds 1 (for additional spacing in $spacer)
                        $space = strlen($number_setting)+4; // Counts number_setting length above, then adds 1 (for base spacing in $spacer)
                        $spacer = ($space*2)+$countspace; // Creates a new base spacing amount for Number Codes and Code Labels below

                        foreach (Mage::helper('core/string')->str_split($cola, 110, true, true) as $key => $part) {
                            // Set Number Type + Actual Number
                            $this->_setFontBold($page, 6.7);
                            $page->drawText(Mage::helper('sales')->__($number_setting.$numberCount.': '), $column_position, $line, 'UTF-8');
                            // Set Code Labels + Spacing (set in Admin)
                            $this->_setFontRegular($page, 6.7);
                            //$page->drawText($part.' - '.$space.' - '.$spacer.' - '.$countspace, $column_position+$spacer+$spacer_cust, $line, 'UTF-8');
                            $page->drawText($part, $column_position+$spacer+$spacer_cust, $line, 'UTF-8');
                            $line -= $separation;
                            $numberCount++;
                        }
                    } else {
                        $cola = 'Please configure the Return/Exchange Code(s) in System > Configuration > [GSC Extentions] Additional Order Slips > Return / Exchange (Printable Form) Configuration Details > Return/Exchange Code(s)';
                        foreach (Mage::helper('core/string')->str_split($cola, 110, true, true) as $key => $part) {
                            $page->drawText($part, 29, $line, 'UTF-8');
                            $line -= $separation;
                        }
                    }
                }
            }

            // Return / Exchange Heading
            
            $this->_setFontBold($page, 7); 
            $page->drawText(Mage::helper('sales')->__('Return (Ret) or Exchange (Exch):'), $col4X, $title2Y, 'UTF-8');

            // Return / Exchange Text 

            $this->_setFontRegular($page, 6.7); 
            $returntext = Mage::getStoreConfig('orderslips/rma_inputs/returntext');
            $line = 610;

            if($returntext) {
                foreach (Mage::helper('core/string')->str_split($returntext, 61, true, true) as $k => $p) {
                    $page->drawText($p, $col4X, $line, 'UTF-8');
                    $line -= $separation;
                }
            } else {
                $returntext = 'Please configure the Intro Text in System > Configuration > [GSC Extentions] Additional Order Slips > Return / Exchange (Printable Form) Input Details > Return / Exchange text area';
                foreach (Mage::helper('core/string')->str_split($returntext, 61, true, true) as $k => $p) {
                    $page->drawText($p, $col4X, $line, 'UTF-8');
                    $line -= $separation;
                }
            }

            // Return / Exchange Example
            $this->_setFontBold($page, 6.3); 
            $page->setFillColor(new Zend_Pdf_Color_Html('#707070'));
            
            // Return / Exchange Example Titles

            $this->y -= 30;
            $phline = $this->y;
            $page->drawText(Mage::helper('sales')->__('Ret'), 29, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Exch'), 44, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Code'), 65, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Product'), 105, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('SKU'), 350, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('QTY'), 490, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Item Price'), 540, $phline, 'UTF-8');
            
            // Return / Exchange Example Filler
            $example_prodname = Mage::getStoreConfig('orderslips/rma_inputs/ex_prodname');
            $example_sku = Mage::getStoreConfig('orderslips/rma_inputs/ex_sku');
            $example_price = Mage::getStoreConfig('orderslips/rma_inputs/ex_price');

            $this->_setFontRegular($page, 6.3); 
            $phline = $this->y-9;
            $page->drawText(Mage::helper('sales')->__('__'.$number_setting.'1__'), 65, $phline, 'UTF-8');
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.95)); // Sets color of box
            $page->drawRectangle(29, $phline-1, 39, $phline+7); // Return Box
            $page->drawRectangle(47, $phline-1, 57, $phline+7); // Exchange Box
            $page->setFillColor(new Zend_Pdf_Color_Html('#707070')); // Reset color to gray
            $page->drawText(Mage::helper('sales')->__('X'), 50, $phline, 'UTF-8');
            if ($example_prodname) {
                $page->drawText(Mage::helper('sales')->__($example_prodname), 105, $phline, 'UTF-8');
            } else {
                $page->drawText(Mage::helper('sales')->__('Example Product Name'), 105, $phline, 'UTF-8');
            }
            if ($example_sku) {
                $page->drawText(Mage::helper('sales')->__($example_sku), 350, $phline, 'UTF-8');
            } else {
                $page->drawText(Mage::helper('sales')->__('example-sku'), 350, $phline, 'UTF-8');
            }
            $page->drawText(Mage::helper('sales')->__('1'), 490, $phline, 'UTF-8');
            if ($example_price) {
                $page->drawText(Mage::helper('sales')->__($example_price), 540, $phline, 'UTF-8');
            } else {
                $page->drawText(Mage::helper('sales')->__('$39.00'), 540, $phline, 'UTF-8');
            }

            /* Add table head */
            $this->_setFontBold($page, 7.5);
            $this->y = 550;
            $phline = $this->y;
            $page->setFillColor(new Zend_Pdf_Color_Html('#343434'));
            $page->drawText(Mage::helper('sales')->__('Ret'), 25, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Exch'), 43, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Code'), 65, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Product'), 105, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('SKU'), 350, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('QTY'), 490, $phline, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Item Price'), 540, $phline, 'UTF-8');

            $this->y -=15;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            /* Add body */
            foreach ($order->getAllItems() as $item){
            	if ($item->getParentItem()) {
                    continue;
                }
                
                $shift = array();
                if ($this->y<30) {
                    /* Add new table head */
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_LETTER);
                    $pdf->pages[] = $page;
                    $this->y = 775;

                    $this->_setFontBold($page);
                    $this->y -=10;

                    $page->setFillColor(new Zend_Pdf_Color_Html('#343434'));
                    $page->drawText(Mage::helper('sales')->__('Ret'), 25, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Exch'), 43, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Code'), 65, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Product'), 105, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('SKU'), 350, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('QTY'), 490, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Item Price'), 540, $this->y, 'UTF-8');

                    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                    $this->y -=20;

                }

                /* Draw item */
                $this->_setFontRegular($page, 7.5);
                $page->setLineWidth(0.5);
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $this->_drawItem($item, $page, $order);
            }
            
            $this->y -= 5;

            /* PAGE BREAK: for Exchange Request Information */

            if ($this->y<35) {
                /* Add new table head */
                $page = $pdf->newPage(Zend_Pdf_Page::SIZE_LETTER);
                $pdf->pages[] = $page;
                $this->y = 750;
                $page->setLineWidth(0.5);
            }

            // Exchanges Requests Only Sentances (above Exchange Details box)

            $store_name = Mage::getStoreConfig('general/store_information/name');
            $store_phone = Mage::getStoreConfig('general/store_information/phone');
            $email_contact = Mage::getStoreConfig('orderslips/rma_inputs/email_contact');
            $hours_operation = Mage::getStoreConfig('orderslips/rma_inputs/hours_operation');

            if (!$store_name) {
                $store_name = 'Example Store';
            }
            if (!$store_phone) {
                $store_phone = '1 (888) 555-5555';
            }
            if (!$general_contact) {
                $general_contact = 'service@example.com';
            }
            if (!$hours_operation) {
                $hours_operation = 'Mon-Fri 9am-5pm';
            }

            $this->_setFontBold($page, 7.5);
            $page->drawText(Mage::helper('sales')->__('** Exchange Requests Only **'), 25, $this->y, 'UTF-8');
            $this->_setFontRegular($page, 7);
            $page->drawText(Mage::helper('sales')->__('For special exchanges, please order online or contact us at ' . $store_phone . ' ' . $hours_operation . ' or anytime at ' . $email_contact . '.'), 33, $this->y-10, 'UTF-8');
            $this->_setFontItalic($page, 7);
            $page->drawText(Mage::helper('sales')->__('Below, place the item(s) you would like in Exchange. Please note, you will be contacted for payment if you owe an additional for amount for your new item(s).'), 33, $this->y-18, 'UTF-8');

            /* PAGE BREAK: for Exchange Request Box Titles & Table */

            if ($this->y<110) {
                /* Add new table head */
                $page = $pdf->newPage(Zend_Pdf_Page::SIZE_LETTER);
                $pdf->pages[] = $page;
                $this->y = 750;
                $page->setLineWidth(0.5);
            }

            // Exchange Box Titles
            $this->_setFontBold($page, 7.5);
            $this->y -= 30;
            $page->drawText(Mage::helper('sales')->__('Product'), 25, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('SKU'), 230, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('QTY'), 350, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Color'), 385, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Size'), 450, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Item Price'), 510, $this->y, 'UTF-8');
              

            /* Exchange Box Table */

            $this->y -= 5;

            // TODO: Give option for # of boxes (up to maximum) of exchange rows?
            $total=1;
            while($total <= 4) {
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.95));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y, 585, $this->y - 15);
                $page->drawLine(230, $this->y, 230, $this->y - 15); // Before SKU
                $page->drawLine(350, $this->y, 350, $this->y - 15); // Before QTY
                $page->drawLine(385, $this->y, 385, $this->y - 15); // Before Color
                $page->drawLine(450, $this->y, 450, $this->y - 15); // Before Size
                $page->drawLine(510, $this->y, 510, $this->y - 15); // Before Size
                $this->y -= 15;
                $total++;
            }

           $this->y -= 15;

            /* PAGE BREAK: for Customer Comments Box & Office Use Only Box & Their Labels */

            if ($this->y<80) {
                /* Add new table head */
                $page = $pdf->newPage(Zend_Pdf_Page::SIZE_LETTER);
                $pdf->pages[] = $page;
                $this->y = 750;
                $this->_setFontBold($page);
                $page->setLineWidth(0.5);
            }

            // Customer Request Label & Official Use Label
            
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Customer Requests'), 25, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Office Use Only'), 385, $this->y, 'UTF-8');


            $this->y -= 5;

            // Customer Request Box & Official Use Box

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.95));
            $page->drawRectangle(25, $this->y, 350, $this->y - 60);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.9));
            $page->drawRectangle(385, $this->y, 585, $this->y - 60);

            $this->y -= 90;

            // Return & Exchange Labels

            if (Mage::getStoreConfig('orderslips/rma_inputs/shipping_enabler')) {

                /* PAGE BREAK: for Cut Line & Text */

                if ($this->y<15) {
                    /* Add new table head */
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_LETTER);
                    $pdf->pages[] = $page;
                    $this->y = 775;
                    $this->_setFontRegular($page, 7);
                }

                // Label Cut Line & Text
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.95));
                $page->drawLine(25, $this->y, 585, $this->y); // Cut line

                // Label Text BG
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(1));
                $page->drawRectangle(255, $this->y+5, 345, $this->y-5); 

                // Label Text
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $this->_setFontRegular($page, 7);
                $page->drawText(Mage::helper('sales')->__('Shipping Labels Below'), 265, $this->y-2, 'UTF-8');


                // Return & Exchange Labels Text
                
                $this->y -= 40;

                /* PAGE BREAK: for Return & Exchange Labels */

                if ($this->y<60) {
                    /* Add new table head */
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_LETTER);
                    $pdf->pages[] = $page;
                    $this->y = 750;
                    $this->_setFontBold($page, 12);
                }

                $lineA = 225; // Column 1 (Exchanges)
                $lineB = 225; // Column 2 (Returns)

                $separation = 16; // Line/Row Height
                $cl1X = 45; // Column 1 "x" position
                $cl2X = 360; // Column 2 "x" position

                $page->setFillColor(new Zend_Pdf_Color_Html('#000000'));
                
                $this->_setFontBold($page, 12);

                // Exchange Label
                $exchangelabel = Mage::getStoreConfig('orderslips/rma_inputs/exchangelabel');
                if($exchangelabel) {
                    foreach(explode("\n", $exchangelabel) as $exlab => $exchange_label) {
                        $page->drawText($exchange_label, $cl1X, $lineA, 'UTF-8');
                        $lineA -= $separation;
                    }
                } else {
                    $exchangelabel = 'Please configure the Exchange Label in System > Configuration > [GSC Extentions] Additional Order Slips > Return / Exchange (Printable Form) Input Details > Return / Exchange text area';
                    foreach (Mage::helper('core/string')->str_split($exchangelabel, 40, true, true) as $m => $n) {
                        $page->drawText($n, $cl1X, $lineA, 'UTF-8');
                        $lineA -= $separation;
                    }
                }

                // Return Label
                $exchangelabel = Mage::getStoreConfig('orderslips/rma_inputs/returnlabel');
                if($exchangelabel) {
                    foreach(explode("\n", $exchangelabel) as $exlab => $exchange_label) {
                        $page->drawText($exchange_label, $cl2X, $lineB, 'UTF-8');
                        $lineB -= $separation;
                    }
                } else {
                    $exchangelabel = 'Please configure the Return Label in System > Configuration > [GSC Extentions] Additional Order Slips > Return / Exchange (Printable Form) Input Details > Return / Exchange text area';
                    foreach (Mage::helper('core/string')->str_split($exchangelabel, 40, true, true) as $m => $n) {
                        $page->drawText($n, $cl2X, $lineB, 'UTF-8');
                        $lineB -= $separation;
                    }
                }
            }
                //end
        }

        $this->_afterGetPdf();

        return $pdf;
    }
    
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $type = $item->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

        $renderer->draw();
    }
    
}