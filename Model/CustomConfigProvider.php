<?php


namespace Saulmoralespa\JustPay\Model;


class CustomConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo
    )
    {
        $this->_assetRepo = $assetRepo;
    }

    public function getConfig()
    {
        return [
            'payment' => [
                'justpay_cash' => [
                    'logoUrl' => $this->_assetRepo->getUrl("Saulmoralespa_JustPay::images/cash.png")
                ],
                'justpay_online' => [
                    'logoUrl' => $this->_assetRepo->getUrl("Saulmoralespa_JustPay::images/online.png")
                ],
                'justpay_cards' => [
                    'logoUrl' => $this->_assetRepo->getUrl("Saulmoralespa_JustPay::images/cards.jpg")
                ]
            ]
        ];
    }
}