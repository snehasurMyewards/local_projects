<?php
namespace App\Jobs;

use App\Exports\ContactsChunkExportlocal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ExportContactsChunklocal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contacts;
    protected $chunkIndex;
    protected $folderName;

    public function __construct(Collection $contacts, int $chunkIndex, string $folderName)
    {
        $this->contacts = $contacts;
        $this->chunkIndex = $chunkIndex;
        $this->folderName = $folderName;
    }

    public function handle()
    {
        $fileName = $this->folderName . '/contacts_chunk_' . $this->chunkIndex . '.xlsx';
        Log::info('fileName: ' . dirname($fileName));//exports/contacts/20240619053611  
        $filePath= storage_path('app/' . $fileName); //D:\Newproject_laravel10\demo\laravel\storage\app/exports/contacts/20240619053611/contacts_chunk_1.xlsx  
        Log::info('filePath: ' . $filePath);
        try{
            // Ensure directory exists
            if (!is_dir(dirname($filePath))) {
                    mkdir(dirname($filePath), 0777, true);
                    Log::info('Directory created: ' . dirname($filePath));
                }
                // Save the Excel file locally in the public directory
                Excel::store(new ContactsChunkExportlocal($this->contacts), $fileName, 'local');
                Log::info('File saved locally at: ' . $filePath);
                if (file_exists($filePath)) {
                    $buckturl ='https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/';
                    // Upload the file to S3
                    Storage::disk('s3')->put($fileName,file_get_contents($filePath), 'public');    
                    // Delete the local file
                    unlink($filePath);
                    Log::info('Local file deleted: ' . $filePath);
    
                    // Log the S3 file URL
                    $s3Url = $buckturl.$fileName;
                    Log::info('S3 URL: ' . $s3Url);
                } else {
                    Log::error('File could not be created at: ' . $filePath);
                }


        }
        catch(Exception $e){
            Log::info('An error occurred while handling the job: ' . $e->getMessage());
        }

    }


    
}
