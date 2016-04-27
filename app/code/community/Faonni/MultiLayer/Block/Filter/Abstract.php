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
abstract class Faonni_MultiLayer_Block_Filter_Abstract 
	extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Initialize filter template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('faonni/multilayer/filter.phtml');
    }
	
    /**
     * Check is available option
     *
     * @param $item Faonni_MultiLayer_Model_Filter_Item
     * @return bool
     */
    public function isAvailable(Faonni_MultiLayer_Model_Filter_Item $item)
    {
		return (bool) !$this->isSelected($item) && $item->getCount() > 0;
    }
	
	/**
     * Check is selected option
     *
     * @param $item Faonni_MultiLayer_Model_Filter_Item
     * @return bool
     */
    public function isSelected(Faonni_MultiLayer_Model_Filter_Item $item)
    {
		$values = $this->_filter->getSelected();
		
		if (!is_array($values)) {
			$values = array();
		}
		return in_array($item->getValue(), $values);
    }
	
	/**
     * Check is disabled option
     *
     * @param $item Faonni_MultiLayer_Model_Filter_Item
     * @return bool
     */
    public function isDisabled(Faonni_MultiLayer_Model_Filter_Item $item)
    {
        return (bool) !$this->isSelected($item) && $item->getCount() == 0;
    }	
}