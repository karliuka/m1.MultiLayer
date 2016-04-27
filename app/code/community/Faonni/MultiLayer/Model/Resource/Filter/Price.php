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
class Faonni_MultiLayer_Model_Resource_Filter_Price 
	extends Mage_Catalog_Model_Resource_Layer_Filter_Price
{
    /**
     * Apply price range filter to product collection
     *
     * @param Faonni_MultiLayer_Model_Filter_Price $filter
     * @return Faonni_MultiLayer_Model_Resource_Filter_Price
     */
    public function applyPriceRange($filter)
    {
        $interval = $filter->getInterval();
        if (!$interval) {
            return $this;
        }

        list($from, $to) = $interval;
        if ($from === '' && $to === '') {
            return $this;
        }

        $select = $filter->getLayer()->getProductCollection()->getSelect();
        $priceExpr = $this->_getPriceExpression($filter, $select, false);

        if ($to !== '') {
            $to = (float)$to;
            if ($from == $to) {
                $to += self::MIN_POSSIBLE_PRICE;
            }
        }

		$where = array();
        if ($from !== '') {
			$where[] = $priceExpr . ' >= ' . $this->_getComparingValue($from, $filter);
        }
        if ($to !== '') {
			$where[] = $priceExpr . ' < ' . $this->_getComparingValue($to, $filter);
        }
        if (0 < count($where)) {
		
            $select->where(implode(' AND ', $where));
			
			foreach ($filter->getIntervals() as $interval) {
				if (!$interval) {
					continue;
				}
				
				list($from, $to) = $interval;
				if ($from === '' && $to === '') {
					continue;
				}
				
				if ($to !== '') {
					$to = (float)$to;
					if ($from == $to) {
						$to += self::MIN_POSSIBLE_PRICE;
					}
				}	

				$where = array();
				if ($from !== '') {
					$where[] = $priceExpr . ' >= ' . $this->_getComparingValue($from, $filter);
				}
				if ($to !== '') {
					$where[] = $priceExpr . ' < ' . $this->_getComparingValue($to, $filter);
				}
				
				if (0 < count($where)) {
					$select->orWhere(implode(' AND ', $where));
				}
			}				
        }
        return $this;
    }
}