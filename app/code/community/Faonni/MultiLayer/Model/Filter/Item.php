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
class Faonni_MultiLayer_Model_Filter_Item 
	extends Mage_Catalog_Model_Layer_Filter_Item
{
    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
		$values = $this->getFilter()->getSelected();
		$values[] = $this->getValue();
		$query = array(
			$this->getFilter()->getRequestVar() => implode(',', $values),
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );
        return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
    }
	
    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
		$value = $this->getValue();
		$query = array($this->getFilter()->getRequestVar() => $this->getFilter()->getResetValue());
		if (!is_array($value)) {
			$values = array();
			foreach ($this->getFilter()->getSelected() as $id) {
				if ($value == $id) continue;
				$values[$id] = $id;
			}
			if (0 < count($values)) {
				$query = array($this->getFilter()->getRequestVar() => implode(',', $values));
			}
		}
        return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
    }	
}
