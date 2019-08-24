<?php


namespace Saulmoralespa\JustPay\Block;


class Success extends \Magento\Framework\View\Element\Template
{
    public function getMessage()
    {
        return __('Payment has been successfully received');
    }

    public function getUrlHome()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}