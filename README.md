Just Pay for magento2
============================================================

## Description ##
Just Pay gateway payment available for Chile

## Table of Contents

* [Installation](#installation)
* [Configuration](#configuration)

## Installation ##

Use composer package manager

```bash
composer require saulmoralespa/magento2-justpay
```

Execute the commands

```bash
php bin/magento module:enable Saulmoralespa_JustPay --clear-static-content
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy en_US #on i18n
```
## Configuration ##

### 1. Enter the configuration menu of the payment method ###
![Enter the configuration menu of the payment method]
