<?php
class SmartGravity_Orders_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders Products List'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('smartgravity_orders/adminhtml_sales_order'));
        $this->renderLayout();
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('smartgravity_orders/adminhtml_sales_order_grid')->toHtml()
        );
    }
    public function exportSmartGravityCsvAction()
    {
        $fileName = 'orders_smartgravity.csv';
        $grid = $this->getLayout()->createBlock('smartgravity_orders/adminhtml_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    public function exportSmartGravityExcelAction()
    {
        $fileName = 'orders_smartgravity.xml';
        $grid = $this->getLayout()->createBlock('smartgravity_orders/adminhtml_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}