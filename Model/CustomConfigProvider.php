<?php


namespace Saulmoralespa\JustPay\Model;


class CustomConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    protected $_storeManager;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_assetRepo = $assetRepo;
        $this->_storeManager = $storeManager;
    }

    public function getConfig()
    {

        $code = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        return [
            'payment' => [
                'justpay_cash' => [
                    'logoUrl' => $this->_assetRepo->getUrl("Saulmoralespa_JustPay::images/cash-$code.png")
                ],
                'justpay_online' => [
                    'logoUrl' => $this->_assetRepo->getUrl("Saulmoralespa_JustPay::images/online-$code.png")
                ],
                'justpay_cards' => [
                    'logoUrl' => $this->_assetRepo->getUrl("Saulmoralespa_JustPay::images/cards-$code.png")
                ]
            ]
        ];
    }
}