<?php

namespace App\Console\Commands;

use DOMDocument;
use Illuminate\Console\Command;
use Spatie\ArrayToXml\ArrayToXml;

class ConvertCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:currency {orders_xml} {currencies_xml} {currency}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert orders to match the currency';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function getConversion($date, $price) {
        $currencies_xml_file = public_path("XML/".$this->argument('currencies_xml'));
        $xmlDataString = file_get_contents($currencies_xml_file);
        $xmlObject = simplexml_load_string($xmlDataString);

        $json = json_encode($xmlObject);
        $currenciesDataArray = json_decode($json, true);

        foreach($currenciesDataArray['currency'] as $key => $value) {
            if($currenciesDataArray['currency'][$key]['code'] != $this->argument('currency')) {
                foreach($currenciesDataArray['currency'][$key]['rateHistory']['rates'] as $key1 => $value1) {
                    foreach($value1 as $key2 => $value2) {
                    }
                }
            }
        }
    }
}
