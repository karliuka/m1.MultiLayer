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
class Faonni_MultiLayer_Block_View 
	extends Mage_Catalog_Block_Layer_View
{
    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
		if (Mage::helper('faonni_multilayer')->isEnabled()) {
			$this->_stateBlockName           = 'faonni_multilayer/state';
			$this->_categoryBlockName        = 'faonni_multilayer/filter_category';
			$this->_attributeFilterBlockName = 'faonni_multilayer/filter_attribute';
			$this->_priceFilterBlockName     = 'faonni_multilayer/filter_price';
			$this->_decimalFilterBlockName   = 'catalog/layer_filter_decimal';
		} 
		else parent::_initBlocks();   	
    }

    /**
     * Check availability display layer options
     *
     * @return bool
     */
    public function canShowOptions()
    {
        if (Mage::helper('faonni_multilayer')->isEnabled()) {
			return true;
		}        
		return parent::canShowOptions();
    }	
}