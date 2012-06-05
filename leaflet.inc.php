<script type="text/javascript">
$(function(){

    var map = initializeMap();
    var primaryCountry = [<?php echo $borrower_cordinates["lng"] ?>, <?php echo $borrower_cordinates["lat"] ?>];
    var coordinates = <?php echo json_encode($supplier_coordinates); ?>;

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

        if (coordinates.length == 0) {
            drawPoint(longitude, latitude);
        }

        for(var index in coordinates) {
            var destination = coordinates[index];
            var dLongitude = destination[0];
            var dLatitude = destination[1];
            var coords = [new arc.Coord(longitude, latitude), new arc.Coord(dLongitude, dLatitude)];
            draw(coords);
        }
    }

    function drawPoint(longitude, latitude) {
        var point = new L.LatLng(latitude, longitude);
        var circleOptions = {};//{color: '#f03', opacity: 0.7};
        var circle = new L.CircleMarker(point, circleOptions);
        map.addLayer(circle);
    }

    function draw(coords) {
        drawPoint(coords[0].lon, coords[0].lat);
        drawPoint(coords[1].lon, coords[1].lat);

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
