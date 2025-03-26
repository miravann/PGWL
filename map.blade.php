@extends('layout.template')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

    <style>
        #map {
            width: 100%;
            height: calc(100vh - 56px);
        }
        .custom-popup .leaflet-popup-content-wrapper {
            background-color: rgba(255,255,255,0.9);
            border-radius: 8px;
        }
        .custom-popup .leaflet-popup-tip {
            background-color: rgba(255,255,255,0.9);
        }
    </style>
@endsection

@section('content')
    <div id="map"></div>

    <!-- Modal Create Point -->
    <div class="modal fade" id="CreatePointModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Create Point</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="{{ route('points.store') }}">
                    <div class="modal-body">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Fill point name here..." required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="geom_point" class="form-label fw-semibold">Geometry</label>
                            <textarea class="form-control" id="geom_point" name="geom_point" rows="3" readonly></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Create Polyline -->
    <div class="modal fade" id="CreatePolylineModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Create Polyline</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="{{ route('polylines.store') }}">
                    <div class="modal-body">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Fill polyline name here..." required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="geom_polyline" class="form-label fw-semibold">Geometry</label>
                            <textarea class="form-control" id="geom_polyline" name="geom_polyline" rows="3" readonly></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Create Polygon -->
    <div class="modal fade" id="CreatePolygonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Create Polygon</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="{{ route('polygons.store') }}">
                    <div class="modal-body">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Fill polygon name here..." required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="geom_polygon" class="form-label fw-semibold">Geometry</label>
                            <textarea class="form-control" id="geom_polygon" name="geom_polygon" rows="3" readonly></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/@terraformer/wkt"></script>

    <script>
        // Koordinat Keraton Yogyakarta
        var keraton_coords = [-7.8055, 110.3629];

        // Inisialisasi peta
        var map = L.map('map').setView(keraton_coords, 16);

        // Tambahkan layer peta OpenStreetMap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Tambahkan marker untuk Keraton Jogja
        var keraton_marker = L.marker(keraton_coords)
            .addTo(map)
            .bindPopup('Keraton Yogyakarta')
            .openPopup();

        // Digitize Function
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: {
                position: 'topleft',
                polyline: true,
                polygon: true,
                rectangle: true,
                circle: false,
                marker: true,
                circlemarker: false
            },
            edit: {
                featureGroup: drawnItems
            }
        });

        map.addControl(drawControl);

        // Handler untuk menggambar objek
        map.on('draw:created', function(e) {
            var type = e.layerType,
                layer = e.layer;

            var drawnJSONObject = layer.toGeoJSON();
            var objectGeometry = Terraformer.geojsonToWKT(drawnJSONObject.geometry);

            switch(type) {
                case 'polyline':
                    $('#geom_polyline').val(objectGeometry);
                    $('#CreatePolylineModal').modal('show');
                    break;
                case 'polygon':
                case 'rectangle':
                    $('#geom_polygon').val(objectGeometry);
                    $('#CreatePolygonModal').modal('show');
                    break;
                case 'marker':
                    $('#geom_point').val(objectGeometry);
                    $('#CreatePointModal').modal('show');
                    break;
                default:
                    console.log('Undefined geometry type');
            }

            drawnItems.addLayer(layer);
        });

        // Layer untuk GeoJSON
        function createGeoJSONLayer(route, layerStyle, popupCallback) {
            return L.geoJson(null, {
                style: layerStyle,
                onEachFeature: function(feature, layer) {
                    if (popupCallback) {
                        layer.on({
                            click: function(e) {
                                popupCallback(feature, layer);
                            },
                            mouseover: function(e) {
                                layer.bindTooltip(feature.properties.name, {
                                    permanent: false,
                                    direction: 'top'
                                }).openTooltip();
                            },
                            mouseout: function(e) {
                                layer.closeTooltip();
                            }
                        });
                    }
                }
            });
        }

        // Points Layer
        var point = createGeoJSONLayer(
            "{{ route('api.points') }}",
            null,
            function(feature, layer) {
                var popupContent =
                    "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Koordinat: " + feature.geometry.coordinates + "<br>" +
                    "Dibuat: " + feature.properties.created_at;
                layer.bindPopup(popupContent, { className: 'custom-popup' }).openPopup();
            }
        );
        $.getJSON("{{ route('api.points') }}", function(data) {
            point.addData(data);
            map.addLayer(point);
        });

        // Polylines Layer
        var polyline = createGeoJSONLayer(
            "{{ route('api.polylines') }}",
            function(feature) {
                return {
                    color: "#3388ff",
                    weight: 3,
                    opacity: 1,
                };
            },
            function(feature, layer) {
                var popupContent =
                    "Kelas Jalan: " + feature.properties.name + "<br>" +
                    "Panjang: " + feature.properties.length_km.toFixed(5) + " km";
                layer.bindPopup(popupContent, { className: 'custom-popup' }).openPopup();
            }
        );
        $.getJSON("{{ route('api.polylines') }}", function(data) {
            polyline.addData(data);
            map.addLayer(polyline);
        });

        // Polygons Layer
        var polygon = createGeoJSONLayer(
            "{{ route('api.polygons') }}",
            function(feature) {
                return {
                    color: "#3388ff",
                    fillColor: "#3388ff",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.2,
                };
            },
            function(feature, layer) {
                var popupContent =
                    "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Luas: " + feature.properties.area_hectare.toFixed(3) + " Ha";
                layer.bindPopup(popupContent, { className: 'custom-popup' }).openPopup();
            }
        );
        $.getJSON("{{ route('api.polygons') }}", function(data) {
            polygon.addData(data);
            map.addLayer(polygon);
        });
    </script>
@endsection
