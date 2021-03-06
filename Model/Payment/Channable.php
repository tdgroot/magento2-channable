<?php
/**
 * Copyright © 2018 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\Channable\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class Channable
 *
 * @package Magmodules\Channable\Model\Payment
 */
class Channable extends AbstractMethod
{

    const CODE = 'channable';
    /**
     * @var string
     */
    protected $_code = self::CODE;
    /**
     * @var bool
     */
    protected $_isOffline = true;
    /**
     * @var bool
     */
    protected $_canUseCheckout = false;
    /**
     * @var bool
     */
    protected $_canUseInternal = true;

    /**
     * @param CartInterface|null $quote
     *
     * @return bool
     */
    public function isAvailable(CartInterface $quote = null)
    {
        return parent::isAvailable($quote);
    }
}
