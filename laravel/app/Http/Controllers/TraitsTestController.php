<?php
// app/Http/Controllers/TraitsTestController.php

namespace App\Http\Controllers;

use App\Http\Traits\TestTrait;
use App\Http\Controllers\Controller;

class TraitsTestController extends Controller
{
    use TestTrait;

    public function __construct()
    {
        // Middleware, etc.
    }

    public function showStudents()
    {
        return $this->index(); // Access the index method from the trait
    }
}
