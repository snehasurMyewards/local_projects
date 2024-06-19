<?php
/*Create Traits in Laravel
Create Traits folder in app/Http, then create Traits/TestTrait.php */
namespace App\Http\Traits;
//use App\Models\Student;
trait TestTrait {


private static function index() {
// Fetch all the students from the 'student' table. $student Student::all();
$data['student'] = "test trait !";
return $data;
}

}