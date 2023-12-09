<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aboutUs = AboutUs::latest()->first();
        return response()->json($aboutUs);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => ['required'],
        ]);

        $aboutUs = new AboutUs();
        $aboutUs->text = $request->text;
        $aboutUs->save();

        return response()->json('berhasil', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(AboutUs $aboutUs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AboutUs $aboutUs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AboutUs $aboutUs)
    {
        $validated = $request->validate([
            'text' => ['required'],
        ]);

        $aboutUs->text = $request->text;
        $aboutUs->save();

        return response()->json('berhasil', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AboutUs $aboutUs)
    {
        $aboutUs->delete();
        return response()->json('berhasil hapus', 200);
    }
}
