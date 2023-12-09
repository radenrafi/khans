<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Kriteria;
use App\Models\ValueKriteria;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cars = Car::with('kriterias')->get();
        return response()->json($cars);
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
            'description' => ['required'],
            'value' => ['required', 'array'],
        ]);

        // $extPicture = $request->picture->getClientOriginalExtension();
        // $pathPicture = "car-" . $request->name . '-' . time() . "." . $extPicture;
        // $pathStore = $request->picture->move(public_path('images/car'), $pathPicture);

        $car = new Car();
        $car->name = $request->name;
        $car->description = $request->description;
        $car->save();

        $kriterias = Kriteria::all();
        foreach ($kriterias as $key => $kriteria) {
            $valueKriteria = $kriteria->values->find($request->value[$key]);
            $car->kriterias()->attach($kriteria->id, ['value' => $valueKriteria->value, 'detail' => $valueKriteria->detail]);
        }

        return response()->json(['car_id' => $car->id, 'message' => 'berhasil'], 200);
    }

    public function storeImage(Request $request)
    {
        $validated = $request->validate([
            'car_id' => ['required'],
            'picture' => ['file', 'image', 'max:1000'],
        ]);

        $car = Car::find($request->car_id);

        $extPicture = $request->picture->getClientOriginalExtension();
        $pathPicture = "car-" . $car->name . '-' . time() . "." . $extPicture;
        $pathStore = $request->picture->move(public_path('images/car'), $pathPicture);

        $car->picture = $pathPicture;
        $car->save();

        return response()->json('berhasil', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $car = Car::with('kriterias')->find($id);
        return response()->json($car);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255'],
            'value' => ['required', 'array'],
        ]);

        $car->name = $request->name;
        $car->save();

        // $car->kriterias()->detach();
        $kriterias = Kriteria::all();
        foreach ($kriterias as $key => $kriteria) {
            $valueKriteria = $kriteria->values->find($request->value[$key]);
            // $car->kriterias()->attach($kriteria->id, ['value' => $valueKriteria->value, 'detail' => $valueKriteria->detail]);
            $car->kriterias()->updateExistingPivot($kriteria->id, [
                'value' => $valueKriteria->value,
                'detail' => $valueKriteria->detail,
            ]);
        }

        return response()->json('berhasil edit', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        $car->delete();
        return response()->json('berhasil hapus', 200);
    }
}
