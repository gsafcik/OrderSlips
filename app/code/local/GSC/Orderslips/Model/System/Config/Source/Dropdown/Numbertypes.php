<?php 

class GSC_Orderslips_Model_System_Config_Source_Dropdown_Numbertypes
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'nt1',
                'label' => '1 Place (1)',
            ),
            array(
                'value' => 'nt2',
                'label' => '2 Places (01)',
            ),
            array(
                'value' => 'nt3',
                'label' => '3 Places (001)',
            ),
            array(
                'value' => 'nt4',
                'label' => 'Letter w/ Number (A1)',
            ),
            array(
                'value' => 'nt5',
                'label' => 'Letter w/ 2 Numbers (A01)',
            ),
        );
    }
}