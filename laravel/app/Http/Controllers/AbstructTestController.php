<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbstructTestController extends Controller
{
    //
}
abstract class PaymentMethod {
    abstract public function processPayment($amount); 
} 
class CreditCard extends PaymentMethod {
    public function processPayment($amount) { 
    //implementation to process payment using credit card 
} 
} 
class BankTransfer extends PaymentMethod { 
    public function processPayment($amount) { 
    //implementation to process payment using bank transfer    
    }
}
