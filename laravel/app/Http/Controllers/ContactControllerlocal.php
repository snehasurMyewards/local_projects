<?php
namespace App\Http\Controllers;

use App\Jobs\ExportContactsChunklocal;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;

class ContactControllerlocal extends Controller
{
    public function export()
    {
        $chunkSize = 5; // Define your chunk size
        $chunkIndex = 1;
        $folderName = 'exports/contacts/' . now()->format('YmdHis');

        Contact::chunk($chunkSize, function($contacts) use (&$chunkIndex, $folderName) {
            ExportContactsChunklocal::dispatch($contacts, $chunkIndex, $folderName);
            $chunkIndex++;
        });

        return response()->json(['message' => 'Local Export started', 'folder' => $folderName]);
    }
}