<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class SpkController extends Controller
{
    public function index()
    {
        $kriterias = Kriteria::all();

        $perbandinganKriterias = collect();
        for ($i = 0; $i < $kriterias->count(); $i++) {
            for ($j = $i + 1; $j < $kriterias->count(); $j++) {
                $temp = ["leftKriteria" => $kriterias[$i]->id, "left" => 1, "right" => 1, "rightKriteria" => $kriterias[$j]->id];
                $perbandinganKriterias->push($temp);
            }
        }

        return response()->json($perbandinganKriterias);
    }

    public function algorithmAhp(Request $request)
    {
        // dump($request[0]);
        $kriterias = Kriteria::all();
        // $kriterias = Kriteria::latest()->take(5)->get();
        $kriteriaCount = $kriterias->count();

        $initialMatriks = collect();
        for ($i = 0; $i < $kriteriaCount; $i++) {
            $matriks = collect();
            for ($j = 0; $j < $kriteriaCount; $j++) {
                $temp = collect(["leftKriteria" => $kriterias[$i]->id, "upKriteria" => $kriterias[$j]->id, "value" => 0]);
                $matriks->push($temp);
            }
            $initialMatriks->push($matriks);
        }

        $perbandingans = $request->request;

        foreach ($initialMatriks as $key => $row) {
            foreach ($row as $key => $value) {
                if ($value['leftKriteria'] == $value['upKriteria']) {
                    $value['value'] = 1;
                } else {
                    foreach ($perbandingans as $key => $perbandingan) {
                        if ($value['leftKriteria'] == $perbandingan['leftKriteria'] && $value['upKriteria'] == $perbandingan['rightKriteria']) {
                            $value['value'] = $perbandingan['left'] / $perbandingan['right'];
                        } else if ($value['leftKriteria'] == $perbandingan['rightKriteria'] && $value['upKriteria'] == $perbandingan['leftKriteria']) {
                            $value['value'] = $perbandingan['right'] / $perbandingan['left'];
                        }
                    }
                }
            }
        }

        $initialTotalMatriks = collect();
        foreach ($kriterias as $kriteria) {
            $total = 0;
            foreach ($initialMatriks as $matrik) {
                foreach ($matrik as $value) {
                    if ($kriteria->id == $value['upKriteria']) {
                        $total += $value['value'];
                    }
                }
            }
            $initialTotalMatriks->push(["kriteria" => $kriteria->id, "value" => $total]);
        }

        $initialNormalisasi = collect();
        for ($i = 0; $i < $kriteriaCount; $i++) {
            $matriks = collect();
            for ($j = 0; $j < $kriteriaCount; $j++) {
                $totalMatrik = $initialTotalMatriks->firstWhere('kriteria', $kriterias[$j]->id);
                $value = $initialMatriks[$i][$j]['value'] / $totalMatrik['value'];
                $temp = ["leftKriteria" => $kriterias[$i]->id, "upKriteria" => $kriterias[$j]->id, "value" => $value];
                $matriks->push($temp);
            }
            $initialNormalisasi->push($matriks);
        }

        $initialAvarage = collect();
        foreach ($initialNormalisasi as $row) {
            $average = $row->avg('value');
            $initialAvarage->push(["kriteria" => $row[0]['leftKriteria'], "value" => $average]);
        }

        $averageTotal = $initialAvarage->sum('value');

        $lamda = 0;
        foreach ($initialTotalMatriks as $key => $totalMatrik) {
            $total = $initialAvarage->firstWhere('kriteria', $totalMatrik['kriteria']);
            $tempLamda = $totalMatrik['value'] * $total['value'];
            $lamda += $tempLamda;
        }

        $ci = ($lamda - $kriteriaCount) / ($kriteriaCount - 1);

        $indeksRandom = [
            [3, 0.58],
            [4, 0.9],
            [5, 1.12],
            [6, 1.24],
            [7, 1.32],
            [8, 1.41],
            [9, 1.45],
            [10, 1.49],
            [11, 1.51],
            [12, 1.58],
        ];

        $cr = 0;
        foreach ($indeksRandom as $key => $indeks) {
            if ($kriteriaCount == $indeks[0]) {
                $cr = $ci / $indeks[1];
            }
        }
        $result = [
            "matriks" => $initialMatriks,
            "totalMatriksKriteria" => $initialTotalMatriks,
            "normalisasi" => $initialNormalisasi,
            "bobot" => $initialAvarage,
            "totalBobot" => $averageTotal,
            "lamdaMax" => $lamda,
            "ci" => $ci,
            "indeksRandom" => $indeksRandom,
            "cr" => $cr,
        ];
        return response()->json($result);
    }

    public function confirmBobot(Request $request)
    {
        $kriterias = Kriteria::all();
        $bobots = $request->request;

        foreach ($kriterias as $key => $kriteria) {
            foreach ($bobots as $key => $bobot) {
                if ($kriteria->id == $bobot['kriteria']) {
                    $kriteria->bobot = $bobot['value'];
                    $kriteria->save();
                }
            }
        }
        return response()->json('bobot berhasil disimpan');
    }

    public function algorithmMaut()
    {
        $kriterias = Kriteria::all();
        $cars = Car::all();

        $nilaiKriteria = collect();
        foreach ($cars as $car) {
            $row = collect();
            foreach ($kriterias as $kriteria) {
                $carKriteria = $car->kriterias->firstWhere('id', $kriteria->id);
                $temp = collect([
                    "car" => $car->id,
                    "kriteria" => $kriteria->id,
                    "value" => $carKriteria->pivot->value
                ]);
                $row->push($temp);
            }
            $nilaiKriteria->push($row);
        }

        $flattened = $nilaiKriteria->flatten(1);
        $groupedByKriteria = $flattened->groupBy('kriteria');

        $minimumValues = $groupedByKriteria->map(function ($items) {
            return $items->min('value');
        })->mapWithKeys(function ($minValue, $kriteria) {
            return [$kriteria => ['kriteria' => $kriteria, 'value' => $minValue]];
        });

        $maximumValues = $groupedByKriteria->map(function ($items) {
            return $items->max('value');
        })->mapWithKeys(function ($maxValue, $kriteria) {
            return [$kriteria => ['kriteria' => $kriteria, 'value' => $maxValue]];
        });

        $utilitas = collect();
        foreach ($cars as $i => $car) {
            $row = collect();
            foreach ($kriterias as $j => $kriteria) {
                $min = $minimumValues[$kriteria->id];
                $max = $maximumValues[$kriteria->id];
                if ($min['value'] == $max['value']) {
                    $value = 1;
                } else {
                    $value = ($nilaiKriteria[$i][$j]['value'] - $min['value']) / ($max['value'] - $min['value']);
                }
                $temp = collect([
                    "car" => $car->id,
                    "kriteria" => $kriteria->id,
                    "value" => $value
                ]);
                $row->push($temp);
            }
            $utilitas->push($row);
        }

        $prefrensi = collect();
        foreach ($cars as $i => $car) {
            $value = 0;
            foreach ($kriterias as $j => $kriteria) {
                $value += $utilitas[$i][$j]['value'] * $kriteria->bobot;
            }
            $car->prefrensi = $value;
            $car->save();
            $prefrensi->push(['car' => $car->id, 'value' => $value]);
        }

        $rankCar = $prefrensi->sortByDesc('value');

        $result = [
            "tabelNilaiKriteria" => $nilaiKriteria,
            "min" => $minimumValues,
            "maks" => $maximumValues,
            "tabelUtilitas" => $utilitas,
            "preferensi" => $prefrensi,
            "ranking" => $rankCar,
        ];
        return response()->json($result);
    }
}
