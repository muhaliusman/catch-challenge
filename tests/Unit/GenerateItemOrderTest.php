<?php

namespace Tests\Unit;

use App\Services\ItemOrderServices;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GenerateItemOrderTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testDownloadFailed()
    {
        Config::set(
            'app.file_order_url',
            'https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/fake-invalid.jsonl'
        );

        $itemOrderService = new ItemOrderServices();
        $itemOrderService->generateCsv();

        $this->assertNotEmpty($itemOrderService->getErrorMessage());
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGenerateCsvSuccess()
    {
        $itemOrderService = new ItemOrderServices();
        $file = $itemOrderService->generateCsv();

        $this->assertTrue(file_exists($file));
        $this->assertSame('', $itemOrderService->getErrorMessage());

        unlink($file);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testInvalidJsonFormat()
    {
        Config::set(
            'app.file_order_url',
            'https://filesamples.com/samples/code/json/sample2.json'
        );

        $itemOrderService = new ItemOrderServices();

        $itemOrderService->generateCsv();
        $this->assertNotEmpty($itemOrderService->getErrorMessage());

        unlink($itemOrderService->getFullPathJson());
    }
}
