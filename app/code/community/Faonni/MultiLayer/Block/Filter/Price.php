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
class Faonni_MultiLayer_Block_Filter_Price 
	extends Faonni_MultiLayer_Block_Filter_Abstract
{
    /**
     * Initialize Price filter module
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'faonni_multilayer/filter_price';
    }

    /**
     * Prepare filter process
     *
     * @return Faonni_MultiLayer_Block_Filter_Price
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
}
