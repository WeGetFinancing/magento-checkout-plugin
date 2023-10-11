# Update WeGetFinancing Checkout

To keep your module up-to-date, just follow these simple steps:

1. Open your command-line interface and run the following Composer command:
    ```
    composer update
    ```
   This will ensure that the plugin is updated to the latest version available.
2. After updating with Composer, let's make sure your Magento store is up to speed. Execute the following commands one by one:
    ```
    php -d memory_limit=-1 bin/magento setup:upgrade
    php -d memory_limit=-1 bin/magento cache:flush
    php -d memory_limit=-1 bin/magento setup:di:compile
    ```
    These commands will handle the necessary updates, clear out any cached data, and compile the Dependency Injection Container (DIC) for improved performance.


That's it! Your module should now be up-to-date and ready to roll.
