The command line generates new file with the values converted to the desired currency.

The input in the command line is based on the function below:
protected $signature = 'convert:currency {orders_xml} {currencies_xml} {currency}';

The input can be for example:
php artisan convert:currency orders.xml currencies.xml GBP