<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// routes/web.php

use App\Http\Controllers\TraitsTestController;

Route::get('/traitstest', [TraitsTestController::class, 'showStudents']);

use App\Http\Controllers\PredictController;

// Route::get('/predict-form', function () {
//     return view('predict');
// });

Route::get('/predict', [PredictController::class, 'predict']);

