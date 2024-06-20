<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phpml\Dataset\ArrayDataset;
use Phpml\Regression\LeastSquares;
use Illuminate\Support\Facades\DB;

class PredictController extends Controller
{
    public function predict(Request $request)
    {
        $sales = DB::table('monthly_merchant_store_data')
        ->where('merchant_id', 15657)
        ->where('report_duration_type', 1)
        ->where('class', 4)
        ->where('year', 2024)
        ->orderBy('month', 'ASC')
        ->pluck('revenue')
        ->toArray();

        // Sample training data: last 12 months sales (example data)
        // $sales1 = [
        //     200, 220, 210, 215, 230, 250, 240, 235, 260, 275, 280, 290
        // ];
        // $sales2=$array = explode(",", $request->sales);
        // print_r($sales1);
        //print_r($sales);

       // die("w");
        // Prepare the samples and targets
        $samples = [];
        $targets = [];
        for ($i = 0; $i < count($sales) - 1; $i++) {
            $samples[] = [$i];
            $targets[] = $sales[$i + 1];
        }

        // Train the model
        $regression = new LeastSquares();
        $regression->train($samples, $targets);

        // Predict the next 12 months sales
        $predictions = [];
        for ($i = count($sales); $i < count($sales) + 12; $i++) {
            $predictions[] = $regression->predict([$i]);
        }
        // Array of month names
        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
        $prev_sales["previous"]=array_combine($months, $sales);
        // Combine months and revenues into an associative array
        $next_monthlyRevenues["next"] = array_combine($months, $predictions);
        echo "<pre>";
        print_r($prev_sales);
        //print_r($predictions);
        print_r($next_monthlyRevenues);
        //return response()->json(['predictions' => $monthlyRevenues]);
    }

}
