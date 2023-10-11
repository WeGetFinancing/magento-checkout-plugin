# Install WeGetFinancing Checkout

To install the module, please follow these steps using the Composer package manager:

1. **Install via Composer**:
    ```bash
    composer require "wegetfinancing/magento-checkout-plugin"
    ```

2. **Enable the module and update your Magento instance** by running the following commands:
    ```bash
    php -d memory_limit=-1 bin/magento module:enable WeGetFinancing_Checkout
    php -d memory_limit=-1 bin/magento setup:upgrade
    php -d memory_limit=-1 bin/magento cache:flush
    php -d memory_limit=-1 bin/magento setup:di:compile
    ```

Feel free to reach out if you have any questions or need further assistance with the installation process.
