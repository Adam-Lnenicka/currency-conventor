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
    public function handle()
    {
        $orders_xml_file = public_path("XML/".$this->argument('orders_xml'));
        $xmlDataString = file_get_contents($orders_xml_file);
        $xmlObject = simplexml_load_string($xmlDataString);
        $json = json_encode($xmlObject);
        $ordersDataArray = json_decode($json, true);

        foreach($ordersDataArray['order'] as $key => $value) {
            if($value['currency'] != $this->argument('currency')) {
                $ordersDataArray['order'][$key]['currency'] = $this->argument('currency');
                $ordersDataArray['order'][$key]['total'] = $this->getConversion($value['date'], $ordersDataArray['order'][$key]['total']);;
                foreach($ordersDataArray['order'][$key]['products']['product'] as $key1 => $value1) {
                    foreach($ordersDataArray['order'][$key]['products']['product'][$key1] as $key2 => $value2) {
                        $ordersDataArray['order'][$key]['products']['product'][$key1][$key2]['price'] = $this->getConversion($value['date'], $ordersDataArray['order'][$key]['products']['product'][$key1][$key2]['price']);
                    }
                }
            }
        }

        $xmlString = ArrayToXml::convert($ordersDataArray, 'orders');

        $dom = new DOMDocument;
        $dom->loadXML($xmlString);

        $dom->save(public_path("XML/orders_converted_to_".$this->argument('currency').".xml"));
    }

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
                        if (isset($currenciesDataArray['currency'][$key]['rateHistory']['rates'][$key1][$key2]['date'])) {
                            if ($currenciesDataArray['currency'][$key]['rateHistory']['rates'][$key1][$key2]['date'] == $date) {
                                foreach($currenciesDataArray['currency'][$key]['rateHistory']['rates'][$key1]['rate'] as $key3 => $value3) {
                                    foreach($value3 as $key3 => $value4) {
                                        if($value4['code'] == $this->argument('currency'))
                                            return number_format((float)($price * $value4['value']), 2, '.', '');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
