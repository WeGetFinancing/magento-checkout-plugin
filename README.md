# WeGetFinancing Payment Checkout for Magento 2

This is a Magento 2 module which integrates WeGetFinancing payment gateway with the Magento 2 application.

## Getting Started

In order to install the module, please use the composer package manager.


1. Install via composer:
    ```
    composer require "wegetfinancing/magento-checkout-plugin"
    ```
2. Copy the configuration file etc/config.xml.dist to etc/config.xml
3. Edit your file etc/config.xml as described in the file
4. Rebuild the cache and the dic with the following command:
    ```
    php -d memory_limit=-1 bin/magento cache:flush && php -d memory_limit=-1 bin/magento setup:di:compile
    ```


