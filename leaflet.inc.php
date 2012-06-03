<script type="text/javascript">
$(function(){

   var map = initializeMap();
        var primaryCountry = [<?php echo $borrower_cordinates["lng"] ?>, <?php echo $borrower_cordinates["lat"] ?>];
        var coordinates = <?php echo json_encode($supplier_coordinates); ?>;

        // Draw from US
        drawAllPaths(primaryCountry, coordinates);


        function initializeMap() {
            var map = new L.Map('map');

            var url = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            var attribution = 'Map data &copy; 2011 OpenStreetMap contributors';
            var osm = new L.TileLayer(url, {maxZoom: 18, attribution: attribution});

            map.setView(new L.LatLng(30, -50), 2).addLayer(osm);

            return map;
        }

        function drawAllPaths(primaryCountry, coordinates) {
            var longitude = primaryCountry[0];
            var latitude = primaryCountry[1];
            for(var index in coordinates) {
                var destination = coordinates[index];
                var dLongitude = destination[0];
                var dLatitude = destination[1];
                var coords = [new arc.Coord(longitude, latitude), new arc.Coord(dLongitude, dLatitude)];
                draw(coords);
            }
        }

        function draw(coords) {
            var len = coords.length;
                var start = new L.LatLng(coords[0].lat, coords[0].lon);
                var circleOptions = {};//{color: '#f03', opacity: 0.7};
                var circle = new L.CircleMarker(start, circleOptions);
                map.addLayer(circle);

                var end = new L.LatLng(coords[1].lat, coords[1].lon);
                var circleOptions = {};//{color: '#f03', opacity: 0.7};
                var circle = new L.CircleMarker(end, circleOptions);
                map.addLayer(circle);

                try {
                    var greatCircle = new arc.GreatCircle(coords[0],coords[1]);
                } catch (e) {
                    // catch possible antipodes error
                    alert(e.message);
                    coords.length = 0;
                    return;
                }

                // number of intermediate arc points
                var npoints = 50;
                var gc = greatCircle.Arc(npoints);
                var line = new L.GeoJSON(gc.json());
                //line.setStyle({color: '#00ff00'});

                line.bindPopup("great circle from " + coords[0].view() + " to " + coords[1].view());
                setTimeout(function() {
                    map.addLayer(line);
                      coords.length = 0;
                },0)
        }

});

</script>