<?php
/**
 * Adminhtml sales orders controller with order printing (Order Slips) action
 *
 * @category    GSC
 * @package     GSC_Orderslips
 */
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class GSC_Orderslips_OrderController extends Mage_Adminhtml_Sales_OrderController
{   
    /**
     * Print RMA
     */
	public function printRmaAction(){
        $order = $this->_initOrder();
        if (!empty($order)) {
			$order->setOrder($order);
            $pdf = Mage::getModel('GSC_Orderslips/order_pdf_order')->getPdf(array($order));
            return $this->_prepareDownloadResponse('rma'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print RMA's for selected orders (Mass Action/Bulk Printing)
     */
    public function pdfRmaAction() {
    $orderIds = $this->getRequest()->getPost('order_ids');
    $flag = false;
    if (!empty($orderIds)) {
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $flag = true;
            $order->setOrder($order);
            if (!isset($pdf)) {
                $pdf = Mage::getModel('GSC_Orderslips/order_pdf_order')->getPdf(array($order));
            } else {
                $pages = Mage::getModel('GSC_Orderslips/order_pdf_order')->getPdf(array($order));
                $pdf->pages = array_merge($pdf->pages, $pages->pages);
            }
        }
        if ($flag) {
            return $this->_prepareDownloadResponse(
                'rma'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                'application/pdf'
            );
        } else {
            $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
            $this->_redirect('*/*/history');
        }
    }
        $this->_redirect('*/*/history');
    }

    /**
     * Print Gift Receipt
     */
    public function printGiftAction(){
        $order = $this->_initOrder();
        if (!empty($order)) {
            $order->setOrder($order);
            $pdf = Mage::getModel('GSC_Orderslips/order_pdf_giftreceipt')->getPdf(array($order));
            return $this->_prepareDownloadResponse('giftreceipt'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print Gift Receipt's for selected orders (Mass Action/Bulk Printing)
     */
    public function pdfGiftAction() {
    $orderIds = $this->getRequest()->getPost('order_ids');
    $flag = false;
    if (!empty($orderIds)) {
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $flag = true;
            $order->setOrder($order);
            if (!isset($pdf)) {
                $pdf = Mage::getModel('GSC_Orderslips/order_pdf_giftreceipt')->getPdf(array($order));
            } else {
                $pages = Mage::getModel('GSC_Orderslips/order_pdf_giftreceipt')->getPdf(array($order));
                $pdf->pages = array_merge($pdf->pages, $pages->pages);
            }
        }
        if ($flag) {
            return $this->_prepareDownloadResponse(
                'giftreceipt'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                'application/pdf'
            );
        } else {
            $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
            $this->_redirect('*/*/history');
        }
    }
        $this->_redirect('*/*/history');
    }
    
}