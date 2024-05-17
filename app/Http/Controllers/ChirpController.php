<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $chirps = Chirp::get();
        return Inertia::render('User/Chirp',['chirps'=>$chirps]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        //
        $chirps = new Chirp;
        $chirps->message = $request->message;
        $chirps->save();
        // return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */


    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp)
    {
        //
    }
}
