function initMap() {
	map = new google.maps.Map(document.getElementById("allMap"), {
		center: { lat: -2.548926, lng: 118.014863 },
		zoom: 5,
		mapTypeId: google.maps.MapTypeId.HYBRID,
	});

	// Make an Ajax request to retrieve the marker data and polygons
	$("#periodetahun").on("change", function () {
		initMap();
		var periodetahun = $(this).val();
		var url =
			periodetahun == "all"
				? "/api/getAPIAnggotaMitraAll/"
				: "/api/getAPIAnggotaMitraByYear/" + periodetahun;
		$.ajax({
			url: url,
			type: "GET",
			dataType: "json",
			success: function (data) {
				console.log(data);
				$.each(data, function (index, anggotaMitra) {
					if (anggotaMitra.latitude && anggotaMitra.longitude) {
						var marker = new google.maps.Marker({
							position: {
								lat: parseFloat(anggotaMitra.latitude),
								lng: parseFloat(anggotaMitra.longitude),
							},
							map: map,
							id: anggotaMitra.id, // Add id property to the marker object
							latitude: anggotaMitra.latitude,
							longitude: anggotaMitra.longitude,
							no_ijin: anggotaMitra.no_ijin,
							no_perjanjian: anggotaMitra.no_perjanjian,
							nama_lokasi: anggotaMitra.nama_lokasi,
							panen_pict: anggotaMitra.panen_pict,
							tanam_pict: anggotaMitra.tanam_pict,

							nama_petani: anggotaMitra.nama_petani,
							nama_kelompok: anggotaMitra.nama_kelompok,
							nama_lokasi: anggotaMitra.nama_lokasi,

							altitude: anggotaMitra.altitude,
							luas_kira: anggotaMitra.luas_kira,
							tgl_tanam: anggotaMitra.tgl_tanam,
							luas_tanam: anggotaMitra.luas_tanam,
							varietas: anggotaMitra.varietas,
							tgl_panen: anggotaMitra.tgl_panen,
							volume: anggotaMitra.volume,
						});

						marker.addListener("click", function () {
							map.setZoom(15);
							map.panTo(marker.getPosition());

							// Send an AJAX request to get the marker data
							$.ajax({
								url: "/api/getAPIAnggotaMitra/" + anggotaMitra.id,
								type: "GET",
								dataType: "json",
								success: function (data) {
									// Create a string containing the marker data
									var markerId = marker.id;
									var no_ijin = marker.no_ijin;
									var no_perjanjian = marker.no_perjanjian;
									var nama_lokasi = marker.nama_lokasi;
									var panenPictName = marker.panen_pict;
									var panenPict = marker.panen_pict;
									var tanamPictName = marker.tanam_pict;
									var tanamPict = marker.tanam_pict;

									var nama_petani = marker.nama_petani;
									var nama_kelompok = marker.nama_kelompok;
									var nama_lokasi = marker.nama_lokasi;
									var altitude = marker.altitude;
									var luas_kira = marker.luas_kira;
									var tgl_tanam = marker.tgl_tanam;
									var luas_tanam = marker.luas_tanam;
									var varietas = marker.varietas;
									var tgl_panen = marker.tgl_panen;
									var volume = marker.volume;

									// Set the modal content to the marker details
									$("#markerModal #markerId").text(markerId);
									$("#markerModal #no_ijin").text(no_ijin);
									$("#markerModal #no_perjanjian").text(no_perjanjian);
									$("#markerModal #nama_lokasi").text(nama_lokasi);

									//set the <a> element for panen
									$("#markerModal #panenPictName").html(
										`<a href="/storage/docs/pks/anggota/panen/img/${panenPict}" target="_blank">${panenPictName}</a>`
									);
									$("#markerModal #panenPict").attr(
										"src",
										"/storage/docs/pks/anggota/panen/img/" + panenPict
									);
									$("#markerModal #panenPict")
										.parent("a")
										.attr(
											"href",
											"/storage/docs/pks/anggota/panen/img/" + panenPict
										);

									//set the <a> element for tanam
									$("markerModal #tanamPictName").html(
										`<a href="/storage/docs/pks/anggota/tanam/img/${tanamPict}" target="_blank">${tanamPictName}</a>`
									);
									$("#markerModal #tanamPict").attr(
										"src",
										"/storage/docs/pks/anggota/tanam/img/" + tanamPict
									);

									$("#markerModal #tanamPict")
										.parent("a")
										.attr(
											"href",
											"/storage/docs/pks/anggota/tanam/img/" + tanamPict
										);
									// $("#markerModal #tanamPictName").text(tanamPictName);
									// $("#markerModal #tanamPict").attr(
									// 	"src",
									// 	"/storage/docs/pks/anggota/tanam/img/" + tanamPict
									// );

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
								},
							});
						});
					}

					if (anggotaMitra.polygon) {
						var polygon = new google.maps.Polygon({
							paths: JSON.parse(anggotaMitra.polygon),
							strokeColor: "#FF0000",
							strokeOpacity: 0.8,
							strokeWeight: 2,
							fillColor: "#FF0000",
							fillOpacity: 0.35,
							map: map,
						});

						polygon.addListener("click", function () {
							var bounds = new google.maps.LatLngBounds();
							polygon.getPath().forEach(function (latLng) {
								bounds.extend(latLng);
							});
							map.fitBounds(bounds);
						});
					}
				});
			},
		});
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
			// If the overlay is a polygon, iterate over its paths and add any markers to the list
			overlay.getPath().forEach(function (path) {
				if (path instanceof google.maps.Marker) {
					markers.push(path);
				}
			});
		}
	}
	return markers;
};

// Extend the Map object to add a getMarkers() method
google.maps.Map.prototype.getMarkers = function () {
	var markers = [];
	for (var i = 0; i < this.overlayMapTypes.length; i++) {
		var overlay = this.overlayMapTypes.getAt(i);
		if (overlay instanceof google.maps.Marker) {
			markers.push(overlay);
		} else if (overlay instanceof google.maps.Polygon) {
			// If the overlay is a polygon, iterate over its paths and add any markers to the list
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
initMap();
