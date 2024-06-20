1.custom laravel package use->contact
git-
local-D:\Newproject_laravel10\demo\LaravelPackage
codecaptain/laravelcontactpackage from https://packagist.org/
https://github.com/snehasurMyewards/laravel-contact-package
2.trait 
3.laravel chunk,xls file create on,uplode on aws s3 bucket return link,job quaue,delete local
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
4.ai prediction with laravel 
php artisan make:controller PredictController
<!-- in controler -->
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phpml\Dataset\ArrayDataset;
use Phpml\Regression\LeastSquares;

class PredictController extends Controller
{
    public function predict(Request $request)
    {
        // Sample training data: [bedrooms, bathrooms, square footage]
        $samples = [
            [2, 1, 1500],
            [3, 2, 2000],
            [4, 3, 2500],
            [5, 2, 3000],
            [3, 1, 1800]
        ];
        $targets = [300000, 400000, 500000, 600000, 350000];

        // Create dataset
        $dataset = new ArrayDataset($samples, $targets);

        // Train the model
        $regression = new LeastSquares();
        $regression->train($dataset->getSamples(), $dataset->getTargets());

        // Get input data from request
        $bedrooms = $request->input('bedrooms');
        $bathrooms = $request->input('bathrooms');
        $sqft = $request->input('sqft');

        // Make a prediction
        $predictedPrice = $regression->predict([$bedrooms, $bathrooms, $sqft]);

        return response()->json(['price' => $predictedPrice]);
    }
}

composer require php-ai/php-ml
<!-- in route -->

use App\Http\Controllers\PredictController;

Route::get('/predict-form', function () {
    return view('predict');
});

Route::post('/predict', [PredictController::class, 'predict']);
<!-- predict blade -->
<!DOCTYPE html>
<html>
<head>
    <title>House Price Predictor</title>
</head>
<body>
    <form action="/predict" method="POST">
        @csrf
        <label for="bedrooms">Bedrooms:</label>
        <input type="number" id="bedrooms" name="bedrooms" required><br><br>
        <label for="bathrooms">Bathrooms:</label>
        <input type="number" id="bathrooms" name="bathrooms" required><br><br>
        <label for="sqft">Square Feet:</label>
        <input type="number" id="sqft" name="sqft" required><br><br>
        <button type="submit">Predict Price</button>
    </form>
</body>
</html>

php artisan serve

