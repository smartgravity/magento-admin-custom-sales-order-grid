<?php
class SmartGravity_Orders_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('smartgravity_order_grid');
        $this->setDefaultSort('purchased_on');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_collection')
            ->join(array('a' => 'sales/order_address'), 'main_table.entity_id = a.parent_id AND a.address_type != \'billing\'', array(
                'city'       => 'city',
                'country_id' => 'country_id'
            ))
            ->join(array('c' => 'customer/customer_group'), 'main_table.customer_group_id = c.customer_group_id', array(
                'customer_group_code' => 'customer_group_code'
            ))
            ->addExpressionFieldToSelect(
                'fullname',
                'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})',
                array('customer_firstname' => 'main_table.customer_firstname', 'customer_lastname' => 'main_table.customer_lastname'))
            ->addExpressionFieldToSelect(
                'products',
                '(SELECT GROUP_CONCAT(\' \', x.name)
                    FROM '.Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item').' x
                    WHERE {{entity_id}} = x.order_id
                        AND x.product_type != \'configurable\')',
                array('entity_id' => 'main_table.entity_id')
            )
        ;
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    protected function _prepareColumns()
    {
        $helper = Mage::helper('smartgravity_orders');
        $currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('increment_id', array(
            'header' => $helper->__('Order #'),
            'index'  => 'increment_id'
        ));
        $this->addColumn('purchased_on', array(
            'header' => $helper->__('Purchased On'),
            'type'   => 'datetime',
            'index'  => 'created_at'
        ));
        $this->addColumn('products', array(
            'header'       => $helper->__('Products Purchased'),
            'index'        => 'products',
            'filter_index' => '(SELECT GROUP_CONCAT(\' \', x.name) FROM '.Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item').' x WHERE main_table.entity_id = x.order_id AND x.product_type != \'configurable\')'
        ));
        $this->addColumn('fullname', array(
            'header'       => $helper->__('Name'),
            'index'        => 'fullname',
            'filter_index' => 'CONCAT(customer_firstname, \' \', customer_lastname)'
        ));
        $this->addColumn('country', array(
            'header'   => $helper->__('Country'),
            'index'    => 'country_id',
            'renderer' => 'adminhtml/widget_grid_column_renderer_country'
        ));
        $this->addColumn('customer_group', array(
            'header' => $helper->__('Customer Group'),
            'index'  => 'customer_group_code'
        ));
        $this->addColumn('grand_total', array(
            'header'        => $helper->__('Grand Total'),
            'index'         => 'grand_total',
            'type'          => 'currency',
            'currency_code' => $currency
        ));
        $this->addColumn('shipping_method', array(
            'header' => $helper->__('Shipping Method'),
            'index'  => 'shipping_description'
        ));
        $this->addColumn('order_status', array(
            'header'  => $helper->__('Status'),
            'index'   => 'status',
            'type'    => 'options',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
        $this->addExportType('*/*/exportSmartGravityCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportSmartGravityExcel', $helper->__('Excel XML'));
        return parent::_prepareColumns();
    }
	public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}