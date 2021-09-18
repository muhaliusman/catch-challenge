<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateCsvCommandTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGenerateWithoutSendingEmail()
    {
        $fileBefore = Storage::allFiles('item_order_csv');
        $this->artisan('item-order:generate-csv')
            ->assertExitCode(0);
        $fileAfter = Storage::allFiles('item_order_csv');

        $newFile = array_values(array_diff($fileAfter, $fileBefore));

        $this->assertSame((count($fileBefore)+1), count($fileAfter));

        Storage::delete($newFile[0]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGenerateWithSendingEmail()
    {
        $fileBefore = Storage::allFiles('item_order_csv');
        $this->artisan('item-order:generate-csv --with-mail aliusman7177@gmail.com')
            ->assertExitCode(0);
        $fileAfter = Storage::allFiles('item_order_csv');

        $newFile = array_values(array_diff($fileAfter, $fileBefore));

        $this->assertSame((count($fileBefore)+1), count($fileAfter));

        $response = Http::withHeaders([
            'Api-Token' => Config::get('mail.mailtrap_api_token'),
        ])->get('https://mailtrap.io/api/v1/inboxes/' . Config::get('mail.mailtrap_inbox_id') . '/messages?search=&page=1');

        $dataMail = $response->json();

        $this->assertIsArray($dataMail);
        $this->assertNotEmpty($dataMail[0]);
        $this->assertTrue(isset($dataMail[0]['from_email']));
        $this->assertTrue(isset($dataMail[0]['to_email']));
        $this->assertSame('test-system@catch.com', $dataMail[0]['from_email']);
        $this->assertSame('aliusman7177@gmail.com', $dataMail[0]['to_email']);

        Storage::delete($newFile[0]);
    }
}
