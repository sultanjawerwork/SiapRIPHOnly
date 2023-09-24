// Declare global variables
var map;
var markers = [];

// Initialize the map
function initMap() {
	map = new google.maps.Map(document.getElementById("allMap"), {
		center: { lat: -2.548926, lng: 118.014863 },
		zoom: 5,
		mapTypeId: google.maps.MapTypeId.HYBRID,
	});

	// Handle the change event of #periodetahun element
	$("#periodetahun").on("change", function () {
		var periodetahun = $(this).val();
		var url =
			periodetahun === "all"
				? "http://127.0.0.1:8000/api/getAPIAnggotaMitraAll/"
				: "http://127.0.0.1:8000/api/getAPIAnggotaMitraByYear/" +
				  periodetahun;

		// Make an AJAX request to retrieve marker data and polygons
		$.ajax({
			url: url,
			type: "GET",
			dataType: "json",
			success: function (data) {
				handleMarkerData(data);
			},
		});
	});
}

// Handle marker data and create markers and polygons
function handleMarkerData(data) {
	// Remove existing markers and polygons from the map
	removeMarkers();
	removePolygons();

	// Iterate over the data to create markers and polygons
	$.each(data, function (index, anggotaMitra) {
		if (anggotaMitra.latitude && anggotaMitra.longitude) {
			createMarker(anggotaMitra);
		}

		if (anggotaMitra.polygon) {
			createPolygon(anggotaMitra);
		}
	});

	// Check for polygon intersections
	var intersections = map.findPolygonIntersections();
	if (intersections.length > 0) {
		console.log("There are intersections between polygons!");
		// Perform additional actions for intersecting polygons
		// You can access the intersecting polygons using intersections array
	}
}

// Create a marker on the map
function createMarker(anggotaMitra) {
	var marker = new google.maps.Marker({
		position: {
			lat: parseFloat(anggotaMitra.latitude),
			lng: parseFloat(anggotaMitra.longitude),
		},
		map: map,
		// Set other properties of the marker here
	});
	// Assign the id property from anggotaMitra to the marker object
	marker.id = anggotaMitra.id;
	marker.npwp = anggotaMitra.npwp;
	marker.periodetahun = anggotaMitra.periodetahun;
	marker.latitude = anggotaMitra.latitude;
	marker.longitude = anggotaMitra.longitude;
	marker.no_ijin = anggotaMitra.no_ijin;
	marker.no_perjanjian = anggotaMitra.no_perjanjian;
	marker.nama_lokasi = anggotaMitra.nama_lokasi;
	marker.panen_pict = anggotaMitra.panen_pict;
	marker.tanam_pict = anggotaMitra.tanam_pict;

	marker.nama_petani = anggotaMitra.nama_petani;
	marker.nama_kelompok = anggotaMitra.nama_kelompok;
	marker.nama_lokasi = anggotaMitra.nama_lokasi;

	marker.altitude = anggotaMitra.altitude;
	marker.luas_kira = anggotaMitra.luas_kira;
	marker.tgl_tanam = anggotaMitra.tgl_tanam;
	marker.luas_tanam = anggotaMitra.luas_tanam;
	marker.varietas = anggotaMitra.varietas;
	marker.tgl_panen = anggotaMitra.tgl_panen;
	marker.volume = anggotaMitra.volume;

	marker.company = anggotaMitra.company;

	// Add a click event listener to the marker
	marker.addListener("click", function () {
		showMarkerDetails(marker);
	});
}

// Create a polygon on the map
function createPolygon(anggotaMitra) {
	var polygon = new google.maps.Polygon({
		paths: JSON.parse(anggotaMitra.polygon),
		strokeColor: "#FF0000",
		strokeOpacity: 0.8,
		strokeWeight: 2,
		fillColor: "#FF0000",
		fillOpacity: 0.35,
		map: map,
	});

	// Add a click event listener to the polygon
	polygon.addListener("click", function () {
		zoomToPolygon(polygon);
	});
}

// Show marker details in a modal
function showMarkerDetails(marker) {
	// Send an AJAX request to get the marker data
	$.ajax({
		url: "http://127.0.0.1:8000/api/getAPIAnggotaMitra/" + marker.id,
		type: "GET",
		dataType: "json",
		success: function (data) {
			// Set the modal content with the marker details
			var markerId = marker.id;
			var npwp = marker.npwp;
			var no_ijin = marker.no_ijin;
			var periodetahun = marker.periodetahun;
			var no_perjanjian = marker.no_perjanjian;
			var nama_lokasi = marker.nama_lokasi;
			var panenPictName = marker.panen_pict;
			var panenPict = marker.panen_pict;
			var tanamPictName = marker.tanam_pict;
			var tanamPict = marker.tanam_pict;

			var nama_petani = marker.nama_petani;
			var nama_kelompok = marker.nama_kelompok;
			var altitude = marker.altitude;
			var luas_kira = marker.luas_kira;
			var tgl_tanam = marker.tgl_tanam;
			var luas_tanam = marker.luas_tanam;
			var varietas = marker.varietas;
			var tgl_panen = marker.tgl_panen;
			var volume = marker.volume;

			var company = marker.company;

			// Update the modal elements with the marker data
			$("#markerModal #markerId").text(markerId);
			$("#markerModal #no_ijin").text(no_ijin);
			$("#markerModal #no_perjanjian").text(no_perjanjian);
			$("#markerModal #nama_lokasi").text(nama_lokasi);
			$("#markerModal #npwp").text(npwp);
			$("#markerModal #company").text(company);

			//set the <a> element for panen
			if (panenPict) {
				$("#markerModal #panenPictName").html(
					`<a href="/storage/uploads/${npwp}/${periodetahun}/${panenPict}" target="_blank">${panenPictName}</a>`
				);
				$("#markerModal #panenPict").attr(
					"src",
					"/storage/uploads/" +
						npwp +
						"/" +
						periodetahun +
						"/" +
						panenPict
				);

				$("#markerModal #panenPict")
					.parent("a")
					.attr(
						"href",
						"/storage/uploads/" +
							npwp +
							"/" +
							periodetahun +
							"/" +
							panenPict
					);
			} else {
				// Jika panenPict tidak ada, sembunyikan elemen gambar dan tautannya
				$("#markerModal #panenPictName").html("");
				$("#markerModal #panenPict").attr("src", "").hide();
				$("#markerModal #panenPict")
					.parent("a")
					.attr("href", "")
					.hide();
			}

			//set the <a> element for tanam
			if (tanamPict) {
				$("#markerModal #tanamPictName").html(
					`<a href="/storage/uploads/${npwp}/${periodetahun}/${tanamPict}" target="_blank">${tanamPictName}</a>`
				);
				$("#markerModal #tanamPict").attr(
					"src",
					"/storage/uploads/" +
						npwp +
						"/" +
						periodetahun +
						"/" +
						tanamPict
				);

				$("#markerModal #tanamPict")
					.parent("a")
					.attr(
						"href",
						"/storage/uploads/" +
							npwp +
							"/" +
							periodetahun +
							"/" +
							tanamPict
					);
			} else {
				// Jika tanamPict tidak ada, sembunyikan elemen gambar dan tautannya
				$("#markerModal #tanamPictName").html("");
				$("#markerModal #tanamPict").attr("src", "").hide();
				$("#markerModal #tanamPict")
					.parent("a")
					.attr("href", "")
					.hide();
			}

			$("#markerModal #nama_petani").text(nama_petani);
			$("#markerModal #nama_kelompok").text(nama_kelompok);
			$("#markerModal #nama_lokasi").text(nama_lokasi);
			$("#markerModal #altitude").text(altitude);
			$("#markerModal #luas_kira").text(luas_kira);
			$("#markerModal #tgl_tanam").text(tgl_tanam);
			$("#markerModal #luas_tanam").text(luas_tanam);
			$("#markerModal #varietas").text(varietas);
			$("#markerModal #tgl_panen").text(tgl_panen);
			$("#markerModal #volume").text(volume);

			// Show the modal
			$("#markerModal").modal("show");
			zoomToMarker(marker);
		},
	});
}

// Zoom the map to fit the marker
function zoomToMarker(marker) {
	map.setZoom(18);
	map.setCenter(marker.getPosition());
}

// Zoom the map to fit the polygon bounds
function zoomToPolygon(polygon) {
	var bounds = new google.maps.LatLngBounds();
	polygon.getPath().forEach(function (latLng) {
		bounds.extend(latLng);
	});
	map.fitBounds(bounds);
}

// Remove all markers from the map
function removeMarkers() {
	map.getMarkers().forEach(function (marker) {
		marker.setMap(null);
	});
}

// Remove all polygons from the map
function removePolygons() {
	map.getPolygons().forEach(function (polygon) {
		polygon.setMap(null);
	});
}

// Extend the Map object to add a getMarkers() method
google.maps.Map.prototype.getMarkers = function () {
	var markers = [];
	for (var i = 0; i < this.overlayMapTypes.length; i++) {
		var overlay = this.overlayMapTypes.getAt(i);
		if (overlay instanceof google.maps.Marker) {
			markers.push(overlay);
		} else if (overlay instanceof google.maps.Polygon) {
			overlay.getPath().forEach(function (path) {
				if (path instanceof google.maps.Marker) {
					markers.push(path);
				}
			});
		}
	}
	return markers;
};

// Extend the Map object to add a getPolygons() method
google.maps.Map.prototype.getPolygons = function () {
	var polygons = [];
	for (var i = 0; i < this.overlayMapTypes.length; i++) {
		var overlay = this.overlayMapTypes.getAt(i);
		if (overlay instanceof google.maps.Polygon) {
			polygons.push(overlay);
		}
	}
	return polygons;
};

// Call the initMap function to initialize the map
initMap();
