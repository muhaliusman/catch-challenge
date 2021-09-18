<?php

namespace App\Http\Controllers;

use App\Services\ItemOrderServices;
use Illuminate\Support\Facades\Cache;

class ItemOrderController extends Controller
{
    /**
     * Home page
     *
     * @return void
     */
    public function index()
    {
        $generatedFiles = Cache::get('generated_files', []);
        $generatedFiles = array_reverse($generatedFiles);
        $lastGenerated = !empty($generatedFiles) ? $generatedFiles[array_key_first($generatedFiles)] : [];
        $addresses = isset($lastGenerated['addresses']) ? $lastGenerated['addresses'] : [];

        return view('home', compact('generatedFiles', 'addresses'));
    }

    /**
     * Generate CSV File
     *
     * @param \App\Services\ItemOrderServices $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateCsv(ItemOrderServices $service)
    {
        $path = $service->generateCsv();
        if (!$path) {
            return redirect()->back()->with('error', $service->getErrorMessage());
        }

        $csvFileName = $service->getCsvFilename();
        $addresses = $service->getCustomerAddresses();
        $status = 'success';
        $id = uniqid();

        $generatedFiles = Cache::get('generated_files', []);

        $generatedFiles[$id] = [
            'filename' => $csvFileName,
            'status' => $status,
            'path' => $path,
            'generated_at' => date('Y-m-d H:i:s'),
            'addresses' => $addresses
        ];

        Cache::forever('generated_files', $generatedFiles);

        return redirect()->back();
    }

    /**
     * Download CSV File
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCsv($id)
    {
        $generatedFiles = Cache::get('generated_files', []);

        if (!isset($generatedFiles[$id])) {
            return redirect()->back()->with('error', "File doesn't exist");
        }

        return response()->download($generatedFiles[$id]['path']);
    }
}
