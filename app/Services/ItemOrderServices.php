<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;

class ItemOrderServices
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $csvFilename;

    /**
     * @var string
     */
    protected $fileUrl;

    /**
     * @var string
     */
    protected $folderName = 'item_order';

    /**
     * @var string
     */
    protected $csvFolderName = 'item_order_csv';

    /**
     * @var string
     */
    protected $fullPath;

    /**
     * @var string
     */
    protected $fullPathCsv;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->filename = basename(config('app.file_order_url'));
        $this->fileUrl = config('app.file_order_url');
        $this->fullPath = storage_path('app/' . $this->folderName . '/' . $this->filename);
        $this->csvFilename = strtotime(now()) . '_result.csv';
        $this->fullPathCsv = storage_path('app/' . $this->csvFolderName . '/' . $this->csvFilename);
        $this->downloadFile();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    private function downloadFile()
    {
        if (!Storage::exists($this->folderName)) {
            Storage::makeDirectory($this->folderName);
        }

        return copy($this->fileUrl, $this->fullPath);
    }

    /**
     * Generate csv with fast excel package
     * using generator to handle big file
     *
     * @return mixed
     */
    public function generateCsv()
    {
        return (new FastExcel($this->fileGenerator()))
            ->export($this->fullPathCsv);
    }

    /**
     * File generator
     *
     * @return mixed
     */
    private function fileGenerator()
    {
        $content = fopen($this->fullPath, 'r');

        if (!$content) {
            return false;
        }

        while (($line = fgets($content)) !== false) {
            $jsonRow = json_decode($line);
            $orderVal = $this->generateOrderValue($jsonRow->items, $jsonRow->discounts);
            if ($orderVal['total_order_value'] > 0) {
                $csvData = collect([
                    'order_id' => $jsonRow->order_id,
                    'order_datetime' => date('c', strtotime($jsonRow->order_date)),
                    'total_order_value' => $orderVal['total_order_value'],
                    'average_unit_price' => $orderVal['average_unit_price'],
                    'distinct_unit_count' => $orderVal['distinct_unit_count'],
                    'total_units_count' => $orderVal['total_units_count'],
                    'customer_state' => $jsonRow->customer->shipping_address->state
                ]);

                yield $csvData;
            }
        }
    }

    /**
     * Generate value based on catch challenge rule
     *
     * @param array $items
     * @param array $discounts
     * @return array
     */
    private function generateOrderValue(array $items, array $discounts)
    {
        $totalOrderValue = 0;
        $totalPriceItem = 0;
        $totalUnit = 0;
        $uniqueProducts = [];

        foreach ($items as $item) {
            $totalOrderValue += $item->quantity * $item->unit_price;

            $totalPriceItem +=  $item->quantity > 0 ? $item->unit_price : 0;

            $totalUnit += $item->quantity;

            if (!in_array($item->product->product_id, $uniqueProducts)) {
                $uniqueProducts[] = $item->product->product_id;
            }
        }

        $avgPrice = round($totalPriceItem / count($items), 2);
        $distinctProduct = count($uniqueProducts);

        usort($discounts, function($a, $b) {
            return $a->priority <=> $b->priority;
        });

        foreach ($discounts as $disc) {
            if (strtoupper($disc->type) === 'PERCENTAGE') {
                $totalOrderValue -= $totalOrderValue * ($disc->value / 100);
            } else {
                $totalOrderValue -= $disc->value;
            }
        }

        return [
            'total_order_value' => $totalOrderValue,
            'average_unit_price' => $avgPrice,
            'distinct_unit_count' => $distinctProduct,
            'total_units_count' => $totalUnit
        ];
    }
}