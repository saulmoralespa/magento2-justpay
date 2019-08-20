<?php


namespace Saulmoralespa\JustPay\Block;


class Finalize extends \Magento\Framework\View\Element\Template
{
    public function getMessage()
    {
        return __('Just Pay: payment finalized');
    }

    public function getUrlHome()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}