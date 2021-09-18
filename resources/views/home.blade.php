<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <link href="{{ asset('css/mapbox.min.css') }}" rel='stylesheet' />

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .map {
                width: 100%;
                height: 300px;
                border-radius: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row my-3">
                <div class="col-12">
                    <h1 class="text-center">CATCH CHALLENGE</h1>
                </div>
            </div>
            <div class="row my-3">
                <div class="col-12 text-center">
                    <form action="{{ route('item-order.generate-csv') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Generate File</button>
                    </form>
                    @if(session('error'))
                    <p class="text-danger">{{ session('error') }}</p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered" aria-label="Table Item Order">
                            <thead>
                                <tr>
                                    <th scope="col">Last Generated</th>
                                    <th scope="col">File Name</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($generatedFiles as $key => $item)
                                <tr>
                                    <td>{{ $item['generated_at'] }}</td>
                                    <td>{{ $item['filename'] }}</td>
                                    <td>{{ $item['status'] }}</td>
                                    <td>@if($item['status'] === 'success') <a href="{{ route('item-order.download-csv', ['id' => $key]) }}">Download</a> @else - @endif</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">- No Data -</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div id="map" class="map"></div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="{{ asset('js/mapbox.js') }}"></script>
        <script src="https://unpkg.com/@mapbox/mapbox-sdk/umd/mapbox-sdk.min.js"></script>
        <script>
            $(function() {
                mapboxgl.accessToken = "{{ config('app.mapbox_key') }}";
                const mapboxClient = mapboxSdk({ accessToken: mapboxgl.accessToken });
                let map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/streets-v11?optimize=true',
                    center: [-74.5, 40],
                    zoom: 9,
                    maxzoom: 7,
                    minzoom: 5
                });

                let arrLngLat = [];
                let popup = null;
                let lat = 0;
                let long = 0;

                let geolocations = [];

                @foreach($addresses as $address)
                mapboxClient.geocoding
                    .forwardGeocode({
                        query: "{{ $address }}",
                        autocomplete: false,
                        limit: 1
                    })
                    .send()
                    .then((response) => {
                        if (
                        !response ||
                        !response.body ||
                        !response.body.features ||
                        !response.body.features.length
                        ) {
                            console.error('Invalid response:');
                            console.error(response);
                            return;
                        }
                        const feature = response.body.features[0];

                        // Create a marker and add it to the map.
                        new mapboxgl.Marker().setLngLat(feature.center).addTo(map);
                        geolocations.push(feature.center);
                        map.fitBounds(geolocations);
                    });
                @endforeach
            })
        </script>
    </body>
</html>

