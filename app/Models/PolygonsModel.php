<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PolygonsModel extends Model
{
    protected $table = 'polygons';

    protected $guarded = ['id'];

    public function geojson_polygons(){

        $polygons = $this
       ->select(DB::raw('id, ST_AsGeoJson(geom) AS geom, ST_Area(geom, true) AS area_m2, ST_Area(geom, true)/1000000 AS area_km2, ST_Area(geom, true)/10000 AS area_hectare, name, description, created_at, updated_at, image'))

        ->get();

        $geojson = [
            'type'      => 'FeatureCollection',
            'features'  => []
        ];

        foreach ($polygons as $polygon) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($polygon->geom),
                'properties' => [
                    'id' => $polygon->id,
                    'name' => $polygon->name,
                    'description' => $polygon->description,
                    'area_m2' => $polygon->area_m2,
                    'area_km2' => $polygon->area_km2,
                    'area_hectare' => $polygon->area_hectare,
                    'created_at' => $polygon->created_at,
                    'updated_at' => $polygon->updated_at,
                    'image' => $polygon->image

                ]
            ];

            array_push($geojson['features'], $feature);
        }

        return $geojson;

    }

    public function geojson_polygon($id){

        $polygons = $this
       ->select(DB::raw('id, ST_AsGeoJson(geom) AS geom, ST_Area(geom, true) AS area_m2, ST_Area(geom, true)/1000000 AS area_km2, ST_Area(geom, true)/10000 AS area_hectare, name, description, created_at, updated_at, image'))

        ->where('id', $id)
        ->get();

        $geojson = [
            'type'      => 'FeatureCollection',
            'features'  => []
        ];

        foreach ($polygons as $polygon) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($polygon->geom),
                'properties' => [
                    'id' => $polygon->id,
                    'name' => $polygon->name,
                    'description' => $polygon->description,
                    'area_m2' => $polygon->area_m2,
                    'area_km2' => $polygon->area_km2,
                    'area_hectare' => $polygon->area_hectare,
                    'created_at' => $polygon->created_at,
                    'updated_at' => $polygon->updated_at,
                    'image' => $polygon->image

                ]
            ];

            array_push($geojson['features'], $feature);
        }

        return $geojson;

    }
}
