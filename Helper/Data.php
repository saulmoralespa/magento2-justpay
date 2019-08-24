<?php


namespace Saulmoralespa\JustPay\Helper;

use Magento\Framework\View\LayoutFactory;

class Data extends \Magento\Payment\Helper\Data
{
    protected $_justPayLogger;

    protected $_enviroment;

    public function __construct(
        \Saulmoralespa\JustPay\Logger\Logger $justPayLogger,
        \Magento\Framework\App\Helper\Context $context,
        LayoutFactory $layoutFactory,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Framework\App\Config\Initial $initialConfig
    )
    {
        parent::__construct(
            $context,
            $layoutFactory,
            $paymentMethodFactory,
            $appEmulation,
            $paymentConfig,
            $initialConfig
        );

        $this->_justPayLogger = $justPayLogger;
        $this->_enviroment = (int)$this->scopeConfig->getValue('payment/justpay/environment',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getActiveCash()
    {
        return (int)$this->scopeConfig->getValue('payment/justpay_cash/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getActiveOnline()
    {
        return (int)$this->scopeConfig->getValue('payment/justpay_online/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getActiveCards()
    {
        return (int)$this->scopeConfig->getValue('payment/justpay_cards/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function log($message)
    {
        if (is_array($message) || is_object($message))
            $message = print_r($message, true);

        $this->_justPayLogger->debug($message);
    }

    public function getPublicKey()
    {
        if ($this->_enviroment)
            return $this->scopeConfig->getValue('payment/justpay/environment_g/development/public_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->scopeConfig->getValue('payment/justpay/environment_g/production/public_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSecureKey()
    {
        if ($this->_enviroment)
            return $this->scopeConfig->getValue('payment/justpay/environment_g/development/secure_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->scopeConfig->getValue('payment/justpay/environment_g/production/secure_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getChannel()
    {
        if ($this->_enviroment)
            return $this->scopeConfig->getValue('payment/justpay/environment_g/development/channel', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->scopeConfig->getValue('payment/justpay/environment_g/production/channel', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getEndPoint()
    {
        if ($this->_enviroment)
            return $this->scopeConfig->getValue('payment/justpay/environment_g/development/endpoint', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->scopeConfig->getValue('payment/justpay/environment_g/production/endpoint', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }

    public function getMinOrderTotal()
    {
        return $this->scopeConfig->getValue('payment/justpay/min_order_total', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getMaxOrderTotal()
    {
        return $this->scopeConfig->getValue('payment/justpay/max_order_total', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAmount($order)
    {
        $amount = $order->getGrandTotal();
        return $amount;
    }

    public function getOrderStates()
    {
        return [
            'pending' => $this->scopeConfig->getValue('payment/justpay/pending', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'approved' => $this->scopeConfig->getValue('payment/justpay/approved', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'rejected' => $this->scopeConfig->getValue('payment/justpay/rejected', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ];
    }
}