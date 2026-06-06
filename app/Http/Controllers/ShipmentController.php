<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function show(string $orderNumber)
    {
        return view('shipments.tracking', ['orderNumber' => $orderNumber]);
    }
}
