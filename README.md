# WeGetFinancing Payment Checkout for Magento 2

This is a Magento 2 module which integrates WeGetFinancing payment gateway with the Magento 2 application.

## Getting Started

In order to install the module, please use the composer package manager.


1. Install via composer:
    ```
    composer require "wegetfinancing/magento-checkout-plugin"
    ```
2. Enter into the configuration folder of the plugin; from the project directory:
   ```
   cd vendor/wegetfinancing/magento-checkout-plugin/etc/
   ```
3. Copy the configuration file:
      ```
   cp config.xml.dist config.xml
   ```
4. Edit your file config.xml as described in the file.
5. Go back to the project folder and run the following commands:
    ```
    php -d memory_limit=-1 bin/magento module:enable WeGetFinancing_Checkout
    php -d memory_limit=-1 bin/magento setup:upgrade
    php -d memory_limit=-1 bin/magento cache:flush
    php -d memory_limit=-1 bin/magento setup:di:compile
    ```


