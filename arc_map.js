var ArcMap = function() {
    this.map = new L.Map('map');

    var url = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var attribution = 'Map data &copy; 2011 OpenStreetMap contributors';
    var osm = new L.TileLayer(url, {maxZoom: 18, attribution: attribution});

    this.map.setView(new L.LatLng(30, -50), 2).addLayer(osm);
};

ArcMap.prototype.drawAllPaths = function(primaryCountry, coordinates) {
    var longitude = primaryCountry[0];
    var latitude = primaryCountry[1];

    if (coordinates.length == 0) {
        this.drawPoint(longitude, latitude);
    }

    for(var index in coordinates) {
        var destination = coordinates[index];
        var dLongitude = destination[0];
        var dLatitude = destination[1];
        var coords = [new arc.Coord(longitude, latitude), new arc.Coord(dLongitude, dLatitude)];
        this.draw(coords);
    }
};

ArcMap.prototype.drawPoint = function(longitude, latitude) {
    var point = new L.LatLng(latitude, longitude);
    var circleOptions = {};//{color: '#f03', opacity: 0.7};
    var circle = new L.CircleMarker(point, circleOptions);
    this.map.addLayer(circle);
};

ArcMap.prototype.draw = function(coords) {
    this.drawPoint(coords[0].lon, coords[0].lat);
    this.drawPoint(coords[1].lon, coords[1].lat);

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
    var mapObj = this.map;
    var layerFunc = function() {
        mapObj.addLayer(line);
        coords.length = 0;
    };
    setTimeout(layerFunc, 0);
};
