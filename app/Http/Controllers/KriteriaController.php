<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Kriteria;
use App\Models\ValueKriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kriterias = Kriteria::with('values')->get();
        return response()->json($kriterias);
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
            'name' => ['required', 'max:255'],
            'value' => ['required', 'array'],
        ]);

        $kriteria = new Kriteria();
        $kriteria->name = $request->name;
        $kriteria->save();

        foreach ($request->value as $key => $value) {
            $valueKriteria = new ValueKriteria();
            $valueKriteria->kriteria_id = $kriteria->id;
            $valueKriteria->value = $key + 1;
            $valueKriteria->detail = $value;
            $valueKriteria->save();
        }

        $cars = Car::all();
        foreach ($cars as $key => $car) {
            $car->kriterias()->attach($kriteria->id);
        }
        return response()->json('berhasil', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kriteria = Kriteria::with('values')->find($id);
        return response()->json($kriteria);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kriteria $kriteria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kriteria $kriteria)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255'],
            'value' => ['required', 'array'],
        ]);

        $kriteria->name = $request->name;
        $kriteria->save();

        foreach ($kriteria->values as $key => $value) {
            $valueKriteria = ValueKriteria::find($value->id);
            $valueKriteria->kriteria_id = $kriteria->id;
            $valueKriteria->value = $key + 1;
            $valueKriteria->detail = $request->value[$key];
            $valueKriteria->save();
        }
        return response()->json('berhasil edit', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();
        return response()->json('berhasil hapus', 200);
    }
}
