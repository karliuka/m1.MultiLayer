<?php
/**
 * Faonni
 *  
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade module to newer
 * versions in the future.
 * 
 * @package     Faonni_MultiLayer
 * @copyright   Copyright (c) 2015 Karliuka Vitalii(karliuka.vitalii@gmail.com) 
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Faonni_MultiLayer_Model_Filter_Category 
	extends Mage_Catalog_Model_Layer_Filter_Category
{
    /**
     * Get categories text from frontend model by category ids
     *
     * @param   array $categoryIds
     * @return  array|bool
     */
    protected function _getSelectedText($categoryIds)
    {
        $categories = array();
		$collection = Mage::getModel('catalog/category')
			->setStore(Mage::app()->getStore()->getId())
			->getCollection()
			->addAttributeToSelect('*')
			->addFieldToFilter('entity_id', array('in' => $categoryIds));
			
		foreach ($collection as $category) {
			$categories[$category->getId()] = $category->getName();
		}
		return $categories;
    }
	
    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
		$filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }
		
		$values = explode(',', $filter);
		$values = $this->_getSelectedText($values);
		$selected = (0 < count($values)) ? array_keys($values) : array();
		$this->setSelected($selected);

		$this->_categoryId = $this->getLayer()->getCurrentCategory()->getId();
		
		if ($filter && 0 < count($values)) {
			$collection = $this->getLayer()->getProductCollection();
			$collection->distinct(true)->joinField(
				'category', 
				'catalog/category_product', 
				null, 
				'product_id=entity_id', 
				'at_category.category_id IN (' . implode(',', $selected) . ')'
			);
			$this->getLayer()->getState()->addFilter($this->_createItem($values, $selected));
        }		
        return $this;
    }
	
    /**
     * Create filter item object
     *
     * @param   mixed $options
     * @param   mixed $value
     * @param   int $count
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($options, $value, $count=0)
    {
		$filter = Mage::getModel('faonni_multilayer/filter_item')
            ->setFilter($this)
            ->setLabel($options)
            ->setValue($value)
            ->setCount($count);
		
		if (is_array($options)) {
			$items = array();
			foreach ($options as $optionId => $label) {
				$items[] = $this->_createItem($label, $optionId, $count);
			}
			$filter->setItems($items);
		}			
		return $filter;
    }
	
    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $key = $this->getLayer()->getStateKey() . '_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $categoty = $this->getCategory();
            /* @var $categoty Mage_Catalog_Model_Category */
            $categories = $categoty->getChildrenCategories();
            $collection = $this->getLayer()->getProductCollection();
			// clean filter
			$fullParts = $parts = $collection->getSelect()->getPart(Zend_Db_Select::FROM);
			foreach ($parts as $alias => $table) {
				if ($alias == 'at_category') {
					unset($parts[$alias]);
				}
			}	
			// math counts
			$collection->getSelect()->setPart(Zend_Db_Select::FROM, $parts);				
            $collection->addCountToCategories($categories);
				
            $data = array();
            foreach ($categories as $category) {
                if ($category->getIsActive()) {
                    $data[] = array(
                        'label' => Mage::helper('core')->htmlEscape($category->getName()),
                        'value' => $category->getId(),
                        'count' => $category->getProductCount(),
                    );
                }
            }
			// restore filter
			$collection->getSelect()->setPart(Zend_Db_Select::FROM, $fullParts);
			
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }	
}