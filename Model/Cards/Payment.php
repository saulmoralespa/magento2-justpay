<?php


namespace Saulmoralespa\JustPay\Model\Cards;

use Magento\Payment\Model\Method\AbstractMethod;

class Payment extends AbstractMethod
{
    const CODE = 'justpay_cards';

    protected $_code = self::CODE;

    protected $_isGateway = true;

    protected $_canOrder = true;

    protected $_canAuthorize = true;

    protected $_canCapture = true;

    protected $_canCapturePartial = true;

    protected $_canRefund = false;

    protected $_canRefundInvoicePartial = false;

    protected $_canVoid = true;

    protected $_canFetchTransactionInfo = true;

    protected $_canReviewPayment = true;

    protected $_supportedCurrencyCodes = ['CLP'];

    protected $_helperData;

    public function __construct(
        \Saulmoralespa\JustPay\Helper\Data $helperData,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_helperData = $helperData;
    }

    public function isActive($storeId = null)
    {
        if ($this->_helperData->getActiveCards()) return true;
        return false;
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if ($quote && (
                $quote->getBaseGrandTotal() < $this->_helperData->getMinOrderTotal()
                || ($this->_helperData->getMaxOrderTotal() && $quote->getBaseGrandTotal() > $this->_helperData->getMaxOrderTotal()))
        ) {
            return false;
        }

        if (!$this->_helperData->getPublicKey() ||
            !$this->_helperData->getSecureKey() ||
            !$this->_helperData->getEndPoint()){
            return false;
        }

        return true;
    }

    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes))
            return false;
        return true;
    }

}