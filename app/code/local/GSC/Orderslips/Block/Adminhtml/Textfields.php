<?php

class GSC_Orderslips_Block_Adminhtml_Textfields extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{


    public function __construct()
    {
        $this->addColumn('retexch_codes', array(
            'label' => Mage::helper('adminhtml')->__('Return / Exchange Code Labels'),
            'style' => 'width:200px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Code Label');
        parent::__construct();
    }

    
}
