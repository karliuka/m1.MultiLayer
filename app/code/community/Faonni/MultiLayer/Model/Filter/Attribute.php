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
class Faonni_MultiLayer_Model_Filter_Attribute 
	extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    /**
     * Retrieve resource instance
     *
     * @return Faonni_Layer_Model_Resource_Filter_Attribute
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('faonni_multilayer/filter_attribute');
        }
        return $this->_resource;
    }

    /**
     * Get options text from frontend model by option ids
     *
     * @param   array $optionIds
     * @return  array|bool
     */
    protected function _getSelectedText($optionIds)
    {
        $options = array();
		foreach ($optionIds as $optionId) {
			$text = trim($this->_getOptionText($optionId));
			if (!empty($text)) {
				$options[$optionId] = $text;
			}
		}
		return $options;
    }
	
    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Faonni_Layer_Model_Filter_Attribute
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
		
        if ($filter && 0 < count($values)) {
			$this->_getResource()->applyFilterToCollection($this, $selected);
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
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
		$attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();
		
        $key = $this->getLayer()->getStateKey() . '_' . $this->_requestVar . '_' . implode(',', $this->getSelected());
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $options = $attribute->getFrontend()->getSelectOptions();
            $optionsCount = $this->_getResource()->getCount($this);
            $data = array();
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                if (Mage::helper('core/string')->strlen($option['value'])) {
					$item = array(
						'label' => $option['label'],
						'value' => $option['value'],
						'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
					);
					// Check filter type
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
						if (!empty($optionsCount[$option['value']])) $data[] = $item;
					} else $data[] = $item;
                }
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$attribute->getId()
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }	
}