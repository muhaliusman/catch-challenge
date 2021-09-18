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
                    <button class="btn btn-primary">Generate File</button>
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
                                <tr>
                                    <td colspan="4" class="text-center">- No Data -</td>
                                </tr>
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
        <script>
            $(function() {
                mapboxgl.accessToken = "{{ config('app.mapbox_key') }}";
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

                map.fitBounds([
                    [101.778689, -5.781634],
                    [116.584968, -7.072630]
                ]);
            })
        </script>
    </body>
</html>

