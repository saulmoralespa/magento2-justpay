<?php


namespace Saulmoralespa\JustPay\Block;


class Error extends \Magento\Framework\View\Element\Template
{
    public function getMessage()
    {
        return __('An error has occurred during payment processing');
    }

    public function getUrlHome()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}