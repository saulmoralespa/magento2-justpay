<?php


namespace Saulmoralespa\JustPay\Logger;


class Handler extends  \Magento\Framework\Logger\Handler\Base
{
    protected $fileName = '/var/log/justpay/info.log';
    protected $loggerType = \Monolog\Logger::INFO;
}