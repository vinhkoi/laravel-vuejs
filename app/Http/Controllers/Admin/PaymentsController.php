<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentsController extends Controller
{
    //
    public function index(){
        $payments = Payment::get();
        return Inertia::render("Admin/Product/PaymentList", ['payments' => $payments]);
    }
}
