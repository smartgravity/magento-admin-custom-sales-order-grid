# Magento Admin Custom Sales Order Grid

This extension creates a new Sales Order Grid for the Magento Admin area. This will pull in a few other details like product names for orders to make searching for particular orders easier.

**Tested on Magento 1.8.1.0, 1.9.0.1**

## Original Work from Inchoo.net

The original tutorial that I pulled this code from can be found here:

**[Inchoo.net - How to Create a Custom Grid From Scratch](http://inchoo.net/magento/how-to-create-a-custom-grid-from-scratch/)**.


> Note: I have changed over the plugin name and directory names from inchoo to smartgravity. I do encourage others that are looking to learn more about Magento extensions to change some aspects of tutorial samples. The reason for doing this is that the Magento code structure is different than most CMS systems and going through this excercise requires you to check every instance and block of code. It will help you gain a greater understanding of what each bit of code accomplishes.

## Changes from Inchoo's Tutorial


### ACL support added:

In: /app/code/community/SmartGravity/Orders/etc/**adminhtml.xml**

The ```<acl>``` tag was added.

    <acl>
        <resources>
            <admin>
                <children>
                    <sales>
                        <children>
                            <smartgravity_orders translate="title" module="smartgravity_orders">
                                <title>Orders - Products List</title>
                            </smartgravity_orders>
                        </children>
                    </sales>
                </children>
            </admin>
        </resources>
    </acl>


### Changed database table from hardcoded with no database prefix to pull in the database prefix using Magento's built in method.

*Sam at RocketTheme added this fix which uses the:*

In: /app/code/community/SmartGravity/Orders/Block/Adminhtml/Sales/Order/**Grid.php**

Change line 30 to:

	FROM '.Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item').' x

and Change line 56 to:

	'filter_index' => '(SELECT GROUP_CONCAT(\' \', x.name) FROM '.Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item').' x WHERE main_table.entity_id = x.order_id AND x.product_type != \'configurable\')'


### Added row url to the individual order in the grid

In: /app/code/community/SmartGravity/Orders/Block/Adminhtml/Sales/Order/**Grid.php**

Add the following near the end just before the grid url around line 92:

	public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

