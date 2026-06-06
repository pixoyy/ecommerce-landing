<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(string $orderNumber)
    {
        return view('payments.create', ['orderNumber' => $orderNumber]);
    }
}
