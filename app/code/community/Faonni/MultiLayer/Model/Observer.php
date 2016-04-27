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
class Faonni_MultiLayer_Model_Observer
{
    /**
     * Prepare Price Item Data
     *
     * @param   Varien_Event_Observer $observer
     * @return  Faonni_MultiLayer_Model_Observer
     */
	public function preparePriceItemData(Varien_Event_Observer $observer)
	{
		if (!Mage::helper('faonni_multilayer')->isEnabled()) {
           // return $this;
        }
		return $this;
		Mage::Log('-', null, 'observer.log');
		
		/** @var $currentCategory Mage_Catalog_Model_Category */
		$currentCategory = Mage::registry('current_category');
		$category = clone $currentCategory;
		
		$collection = $category->getProductCollection();
		$maxPrice = $collection->getMaxPrice();
		$range = null;
		
		$calculation = Mage::app()->getStore()->getConfig(Faonni_MultiLayer_Model_Filter_Price::XML_PATH_RANGE_CALCULATION);
		if ($calculation == Faonni_MultiLayer_Model_Filter_Price::RANGE_CALCULATION_AUTO) {
			$index = 1;
			do {
				$range = pow(10, (strlen(floor($maxPrice)) - $index));
				$items = $this->getRangeItemCounts($range);
				$index++;
			}
			while($range > Faonni_MultiLayer_Model_Filter_Price::MIN_RANGE_POWER && count($items) < 2);
		} else {
			$range = (float)Mage::app()->getStore()->getConfig(Faonni_MultiLayer_Model_Filter_Price::XML_PATH_RANGE_STEP);
		}		
	
		return $this;
	}
}