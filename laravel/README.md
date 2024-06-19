1.custom laravel package use->contact
git-
local-D:\Newproject_laravel10\demo\LaravelPackage
codecaptain/laravelcontactpackage from https://packagist.org/
https://github.com/snehasurMyewards/laravel-contact-package
2.trait 
3.laravel chunk,xls file create,uplode on aws s3 bucket,job quaue
//local
<!-- ->run -->
->composer require maatwebsite/excel --igre-platfonorm-req=ext-gd
<!-- add on config/app.php -->
->'providers' => [
    /*
     * Package Service Providers...
     */
    Maatwebsite\Excel\ExcelServiceProvider::class,
]
->'aliases' => [
    ...
    'Excel' => Maatwebsite\Excel\Facades\Excel::class,
]
<!-- ->run -->
->php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
->php artisan make:export ContactsChunkExport

<!-- ->env file -->
QUEUE_CONNECTION=database
<!-- ->run -->
php artisan queue:table
php artisan migrate
php artisan make:job ExportContactsChunk
<!-- ->add on controller -->
<?php
namespace App\Http\Controllers;

use App\Jobs\ExportContactsChunk;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    public function export()
    {
        $chunkSize = 2; // Define your chunk size
        $chunkIndex = 1;
        $folderName = 'exports/contacts/' . now()->format('YmdHis');

        // Create the folder if it doesn't exist
        Storage::makeDirectory($folderName);

        Contact::chunk($chunkSize, function($contacts) use (&$chunkIndex, $folderName) {
            ExportContactsChunk::dispatch($contacts, $chunkIndex, $folderName);
            $chunkIndex++;
        });

        return response()->json(['message' => 'Export started', 'folder' => $folderName]);
    }
}
<!-- ->in export file -->
<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsChunkExport implements FromCollection, WithHeadings
{
    protected $contacts;

    public function __construct(Collection $contacts)
    {
        $this->contacts = $contacts;
    }

    public function collection()
    {
        return $this->contacts;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Created At',
            'Updated At',
        ];
    }
}

<!-- ->add on jobs -->
<?php
namespace App\Jobs;

use App\Exports\ContactsChunkExport;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class ExportContactsChunk implements ShouldQueue
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
        $fileName = "{$this->folderName}/contacts_chunk_{$this->chunkIndex}.xlsx";
        Excel::store(new ContactsChunkExport($this->contacts), $fileName, 'local');
    }
}

<!-- ->config/filesystems.php -->


    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
    ],

<!-- ->run -->
php artisan queue:work
<!-- //for s3 -->
<!-- run -->
composer require aws/aws-sdk-php --ignore-platform-req=ext-gd
composer require league/flysystem-aws-s3-v3:^3.0 --ignore-platform-req=ext-gd

<!-- in .env -->
QUEUE_CONNECTION=sqs

AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_DEFAULT_REGION=your-region
AWS_BUCKET=your-bucket-name
AWS_URL=https://s3.your-region.amazonaws.com
SQS_PREFIX=https://sqs.your-region.amazonaws.com/your-account-id
SQS_QUEUE=your-queue-name
<!--config/queue.php  -->
'connections' => [
    'sqs' => [
        'driver' => 'sqs',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
        'queue' => env('SQS_QUEUE', 'your-queue-name'),
        'suffix' => env('SQS_SUFFIX'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    // Other connections...
],
<!-- run -->
php artisan make:job ExportContactsChunk
<!-- add in app/Jobs/ExportContactsChunk.php -->
namespace App\Jobs;

use App\Exports\ContactsChunkExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class ExportContactsChunk implements ShouldQueue
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
        $fileName = "{$this->folderName}/contacts_chunk_{$this->chunkIndex}.xlsx";
        Excel::store(new ContactsChunkExport($this->contacts), $fileName, 's3');
    }
}
<!-- run -->
php artisan make:export ContactsChunkExport
<!-- add in app/Exports/ContactsChunkExport.php -->
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsChunkExport implements FromCollection, WithHeadings
{
    protected $contacts;

    public function __construct(Collection $contacts)
    {
        $this->contacts = $contacts;
    }

    public function collection()
    {
        return $this->contacts;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Created At',
            'Updated At',
        ];
    }
}
<!-- in controller -->
namespace App\Http\Controllers;

use App\Jobs\ExportContactsChunk;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    public function export()
    {
        $chunkSize = 1000; // Define your chunk size
        $chunkIndex = 1;
        $folderName = 'exports/contacts/' . now()->format('YmdHis');

        Contact::chunk($chunkSize, function($contacts) use (&$chunkIndex, $folderName) {
            ExportContactsChunk::dispatch($contacts, $chunkIndex, $folderName);
            $chunkIndex++;
        });

        return response()->json(['message' => 'Export started', 'folder' => $folderName]);
    }
}
use App\Http\Controllers\ContactController;
<!-- in route -->
Route::get('export-contacts', [ContactController::class, 'export']);
<!-- run -->
composer clear-cache
composer install
composer dump-autoload
php artisan queue:work sqs

