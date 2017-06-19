<?php
class GSC_Orderslips_Model_Observer
{
    public function addOrderSlipsAction($observer)
    {   

        $block = $observer->getEvent()->getBlock();

           
        // Mass Action Printing option
        if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'sales_order')
        {
            // Return/Exchange
            if (Mage::getStoreConfig('orderslips/rma_general/enabler'))
            { 
                $block->addItem('rma_mass_print', array(
                    'label' => 'Print Return/Exchange Form',
                    'url' => Mage::app()->getStore()->getUrl('orderslips/order/pdfRma'),
                ));
            }
            // Gift Receipt
            if (Mage::getStoreConfig('orderslips/giftreceipt/enabler'))
            {
                $block->addItem('giftreceipt_mass_print', array(
                'label' => 'Print Gift Receipt',
                'url' => Mage::app()->getStore()->getUrl('orderslips/order/pdfGift'),
                ));
            }
        }

        // Order View Page button
        if(get_class($block) =='Mage_Adminhtml_Block_Sales_Order_View'
            && $block->getRequest()->getControllerName() == 'sales_order')
        {
            // Return/Exchange
            if (Mage::getStoreConfig('orderslips/rma_general/enabler') && Mage::getStoreConfig('orderslips/rma_general/button'))
            {
                $block->addButton('rma_print', array(
                    'label'     => 'Return/Exchange Form',
                    'onclick'   => 'setLocation(\'' . $block->getUrl('orderslips/order/printRma') . '\')',
                    'class'     => 'go'
                ));
            }
            // Gift Receipt
            if (Mage::getStoreConfig('orderslips/giftreceipt/enabler') && Mage::getStoreConfig('orderslips/giftreceipt/button'))
            {
                $block->addButton('giftreceipt_print', array(
                    'label'     => 'Gift Receipt',
                    'onclick'   => 'setLocation(\'' . $block->getUrl('orderslips/order/printGift') . '\')',
                    'class'     => 'go'
                ));
            }
        }
    }
}

        
        
    
