<?php 

class GSC_Orderslips_Model_System_Config_Source_Dropdown_Emailcontact
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage::getStoreConfig('trans_email/ident_general/email'),
                'label' => 'General Contact',
            ),
            array(
                'value' => Mage::getStoreConfig('trans_email/ident_sales/email'),
                'label' => 'Sales Representative',
            ),
            array(
                'value' => Mage::getStoreConfig('trans_email/ident_support/email'),
                'label' => 'Customer Support',
            ),
            array(
                'value' => Mage::getStoreConfig('trans_email/ident_custom1/email'),
                'label' => 'Custom Email 1',
            ),
            array(
                'value' => Mage::getStoreConfig('trans_email/ident_custom2/email'),
                'label' => 'Custom Email 2',
            ),
        );
    }
}