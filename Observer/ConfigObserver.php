<?php


namespace Saulmoralespa\JustPay\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ConfigObserver implements ObserverInterface
{

    protected $_scopeConfig;

    protected $_helperData;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Saulmoralespa\JustPay\Helper\Data $helperData
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_helperData = $helperData;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $this->validateNoEmptyFields();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateNoEmptyFields()
    {
        if (($this->_helperData->getActiveCash() ||
            $this->_helperData->getActiveOnline() ||
            $this->_helperData->getActiveCards()) &&
            (!$this->_helperData->getPublicKey() ||
                !$this->_helperData->getSecureKey() ||
                !$this->_helperData->getEndPoint()))
            throw new \Magento\Framework\Exception\LocalizedException(__('Just Pay: requires the fields are not empty'));
    }
}