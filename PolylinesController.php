<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolylinesModel;
use Illuminate\Support\Facades\DB;

class PolylinesController extends Controller
{
    protected $polylines;

    public function __construct()
    {
        $this->polylines = new PolylinesModel();
    }

    /**
     * Menampilkan halaman peta.
     */
    public function index()
    {
        return view('map', ['title' => 'Map']);
    }

    /**
     * Menyimpan data polyline ke database.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|unique:polylines,name',
        'description' => 'required',
        'geom_polyline' => 'required',
    ], [
        'name.required' => 'Name is required',
        'name.unique' => 'Name already exists',
        'description.required' => 'Description is required',
        'geom_polyline.required' => 'Geometry polyline is required',
    ]);

    $data = [
        'name' => $request->name,
        'description' => $request->description,
        'geom' => DB::raw("ST_GeomFromText('" . $request->geom_polyline . "', 4326)"),
    ];

    if (!$this->polylines->create($data)) {
        return redirect()->route('map')->with('error', 'Polyline failed to add');
    }

    return redirect()->route('map')->with('success', 'Polyline has been added');
}

// Add a method to get GeoJSON similar to points
public function getGeoJSON()
{
    $polylines = DB::table('polylines')
        ->selectRaw("id, name, description, ST_AsGeoJSON(geom) as geometry")
        ->get();

    return response()->json([
        'type' => 'FeatureCollection',
        'features' => $polylines->map(function ($polyline) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $polyline->id,
                    'name' => $polyline->name,
                    'description' => $polyline->description
                ],
                'geometry' => json_decode($polyline->geometry)
            ];
        })
    ]);
}
}
