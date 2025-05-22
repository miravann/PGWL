<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolygonsModel;

class PolygonsController extends Controller
{
    public function __construct()
    {
        $this->polygons = new PolygonsModel();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //Validate request
        $request->validate(
            [
                'name' => 'required|unique:polygons,name',
                'description' => 'required',
                'geom_polygon' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exist',
                'description.required' => 'Description is required',
                'geom_polygon.required' => 'Geometry polygons is required',
            ]
        );

        //Create image directory if not exist
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);
        }

        //Get image file
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }

        $data = [
            'geom' => $request->geom_polygon,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
        ];

        // Create data
        if (!$this->polygons->create($data)) {
            return redirect()->route('map')->with('error', 'Polygon failed to add');
        }

        //Redirect data
        return redirect()->route('map')->with('success', 'Polygon has been added');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
      public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Polygon',
            'id' => $id
        ];

        return view('editpolygon', $data);
    }


    /**
     * Update the specified resource in storage.
     */
   // Di dalam method update pada PolygonsController:
public function update(Request $request, string $id)
{
    $request->validate(
        [
            'name' => 'required|unique:polygons,name,' . $id,
            'description' => 'required',
            'geom_polygon' => 'required',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],
        [
            'name.required' => 'Name is required',
            'name.unique' => 'Name already exist',
            'description.required' => 'Description is required',
            'geom_polygon.required' => 'Geometry polygon is required',
        ]
    );

    if (!is_dir('storage/images')) {
        mkdir('./storage/images', 0777, true);
    }

    $old_image = $this->polygons->find($id)->image;

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
        $image->move('storage/images', $name_image);

        if ($old_image != null && file_exists('./storage/images/' . $old_image)) {
            unlink('./storage/images/' . $old_image);
        }
    } else {
        $name_image = $old_image;
    }

    $data = [
        'geom' => $request->geom_polygon, // FIXED here
        'name' => $request->name,
        'description' => $request->description,
        'image' => $name_image,
    ];

    if (!$this->polygons->find($id)->update($data)) {
        return redirect()->route('map')->with('error', 'Polygon failed to update');
    }

    return redirect()->route('map')->with('success', 'Polygon has been updated');
}



    /**
     * Remove the specified resource from storage.
     */
   public function destroy(string $id)
{
    $imagefile = $this->polygons->find($id)->image;

    if (!$this->polygons->destroy($id)){
        return redirect()->route('map')->with('error', 'Polygon failed to delete');
    }

    if ($imagefile != null) {
        if (file_exists('./storage/image/' . $imagefile)) { 
            unlink('./storage/image/' . $imagefile);
        }
    }

    return redirect()->route('map')->with('success', 'Polygon deleted successfully');
}

}
