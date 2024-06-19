<?php
namespace App\Http\Controllers;

use App\Jobs\ExportContactsChunk;
//use App\Models\Contact;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function export()
    {
        $chunkSize = 3000; // Define your chunk size
        $chunkIndex = 1;
        $folderName = 'exports/contacts/' . now()->format('YmdHis');

        // Retrieve only the first 100 records  
        $contacts = DB::table('users')
            ->select('id', 'name')
            ->orderBy('id')
            ->limit(10000)
            ->get();

        // Chunk the contacts and dispatch jobs
        $contacts->chunk($chunkSize)->each(function($chunk) use (&$chunkIndex, $folderName) {
            ExportContactsChunk::dispatch($chunk->toArray(), $chunkIndex, $folderName);
            $chunkIndex++;
        });



        return response()->json(['message' => 'Export started', 'folder' => $folderName]);
    }
}