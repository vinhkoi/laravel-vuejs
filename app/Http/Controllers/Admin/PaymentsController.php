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
    public function store(Request $request){
        $payment = new Payment();
        $payment->order_id = $request->order_id;
        $payment->amount = $request->amount;
        $payment->type = $request->type;
        $payment->status = $request->status;

        $payment->save();
        return redirect()->route('admin.payments.index')->with('success', 'Payment created successfully.');
    }
    public function update(Request $request,$id){
        $payment = Payment::findOrFail($id);
        $payment->order_id = $request->order_id;
        $payment->amount = $request->amount;
        $payment->type = $request->type;
        $payment->status = $request->status;
        $payment->update();
        return redirect()->route('admin.payments.index')->with('success', 'Payment updated successfully.');
    }
    public function destroy(Request $request,$id){
        $payment = Payment::findOrFail($id)->delete();
        return redirect()->route('admin.payments.index')->with('success', 'Payment deleted successfully.');
    }
}
