# WeGetFinancing Payment Checkout for Magento 2

This is a Magento 2 module which integrates WeGetFinancing payment gateway with the Magento 2 application.

## Installation 

In order to install the module, please use the composer package manager.


1. Install via composer:
    ```
    composer require "wegetfinancing/magento-checkout-plugin"
    ```
2. Run the following commands to enable it:
    ```
    php -d memory_limit=-1 bin/magento module:enable WeGetFinancing_Checkout
    php -d memory_limit=-1 bin/magento setup:upgrade
    php -d memory_limit=-1 bin/magento cache:flush
    php -d memory_limit=-1 bin/magento setup:di:compile
    ```
3. Enable and configure the plugin
   1. Log in into your admin area
   2. From the left menu select Stores -> Configuration
   3. Go in the subsection Sales -> Payment Methods
   4. In the "Other payment methods" section you will find "WeGetFinancing Checkout"
   5. Click and insert the required configurations

## Update

To update the module follow those steps:

1. Run composer to update the plugin
   ```
   composer update
   ```
2. Run the magento script to upgrade, flush the cache and rebuild the dic
    ```
    php -d memory_limit=-1 bin/magento setup:upgrade
    php -d memory_limit=-1 bin/magento cache:flush
    php -d memory_limit=-1 bin/magento setup:di:compile
    ```
