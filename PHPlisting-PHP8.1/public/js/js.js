/*
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

function adjustHeight(textarea) {
    $(textarea)
        .css({
            'height':'auto',
            'overflow-y':'hidden'
        })
        .height((textarea.scrollHeight < 150) ? 150 : textarea.scrollHeight);
}

$(document).ready(function(){ 
    $('[data-toggle="confirmation"]').confirmation();

    $('textarea').each(function () {
        adjustHeight(this);
    }).on('input', function () {
        adjustHeight(this);
    });

    $('[data-action="bookmark"]').on('click', function() {
        var element = $(this);

        if (element.data('url') !== undefined && element.data('id') !== undefined) {
            $.ajax({
                'type': 'POST',
                'url': element.data('url'),
                'data': {id: element.data('id'), type: element.data('type')},
                'cache': false,
                'dataType': 'text',
                'success': function(response) {
                    $('[data-action="bookmark"]').each( function (idx, el) {
                        if ($(el).data('id') !== undefined && $(el).data('id') == element.data('id')) {
                            $(el).html(response);
                        }
                    });
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 5000
            });
        }
    });

    $('[data-action="click-to-call"]').on('click', function() {
        var element = $(this);

        if (element.data('url') !== undefined && element.data('id') !== undefined) {
            $.ajax({
                'type': 'POST',
                'url': element.data('url'),
                'data': {id: element.data('id')},
                'cache': false,
                'dataType': 'text',
                'success': function(response) {
                    $('[data-action="click-to-call"]').each( function (idx, el) {
                        if ($(el).data('id') !== undefined && $(el).data('id') == element.data('id')) {
                            element.off();
                            $(el).html('<a href="tel:'+response+'">'+response+'</a>');
                        }
                    });
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 5000
            });
        }
    });

    $('[data-action="visit-website"]').on('click', function(event) {
        event.preventDefault();

        var element = $(this);       

        if (element.data('url') !== undefined && element.data('id') !== undefined) {
            $.ajax({
                'type': 'POST',
                'url': element.data('url'),
                'data': {id: element.data('id')},
                'cache': false,
                'dataType': 'text',
                'success': function() {
                    window.location = element.attr('href');
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 5000
            });
        }
    });
});

(function($) {
    $.fn.slug = function(options) {

        var instance = this;

        var settings = $.extend({
            'url': '',
            'source': 'name'
        }, options);

        var source = $('input[type="text"][name^="' + settings.source + '"');

        if (typeof source !== 'undefined') {
            source.on('input', function() {
                setTimeout(function() {
                    slugify();
                }, 1000);
            });
        }

        function slugify()
        {
            return $.ajax({
                'type': 'POST',
                'url': settings.url,
                'data': {value: source.val()},
                'cache': false,
                'dataType': 'text',
                'success': function(response) {
                    if ('' !== response) {
                        instance.val(response);
                    }
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 5000
            });
        }
    }
}(jQuery));

(function($) {
    $.fn.hash = function(options) {

        var instance = this;

        var settings = $.extend({
            'url': '',
        }, options);

        var trigger = $('#' + this.attr('id') + '-refresh');

        trigger.on('click', function () {
            $.ajax({
                'type': 'GET',
                'url': settings.url,
                'cache': false,
                'dataType': 'text',
                'success': function (response) {
                    if ('' !== response) {
                        instance.val(response);
                    }
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 5000
            });
        });
    }
}(jQuery));

(function($) {
    $.fn.count = function(options) {
        var instance = this;

        var settings = $.extend({
            'editor': '',
            'limit': '0',
            'template': '{count} / {total}'
        }, options);

        var editor = settings.editor;
        var counter = $('#' + this.attr('id') + '-counter');

        if (typeof editor === 'object') {
            update(editor.document.getBody().getText().trim().length);

            editor.on('change', function() {
                setTimeout(function() {
                    update(editor.document.getBody().getText().trim().length);
                }, 500);
            });

            editor.on('mode', function() {
                if (this.mode === 'source') {
                    counter.html('');
                } else {
                    update(editor.document.getBody().getText().trim().length);
                }
            });
        } else {
            update(instance.val().length);

            instance.on('keyup', function() {
                setTimeout(function () {
                    update(instance.val().length);
                }, 200);
            });
        }

        function update(length)
        {
            setTimeout(function() {counter.html(settings.template.replace('{count}', length).replace('{total}', settings.limit).replace('{left}', Number(settings.limit)-Number(length)));}, 500);

            if (Number(length) > Number(settings.limit)) {
                counter.removeClass('text-muted');
                counter.addClass('text-danger');
            } else {
                counter.removeClass('text-danger');
                counter.addClass('text-muted');
            }
        }
    }
}(jQuery));

(function($) {
    $.fn.cascading = function(options) {

        var instance = this;

        var settings = $.extend({
            'url': '',
            'source': 'location',
            'hide_inactive': 0,
            'hide_empty': 0,
            'type_id': 0
        }, options);

        var container = $('#'+(this.attr('id'))+'-container');

        deferred = request();
        deferred.done(function(response) {
            process(response);
        });

        container.on('change', 'div[class^="index_"] > select', function() {
            instance.val($(this).find('option:selected').val()).trigger('change');
            deferred = request();
            deferred.done(function(response) {
                process(response);
            });
        });

        function process(response)
        {
            var last = 0;

            $.each(response, function(index, html) {
                if (index != 'zoom' && index != 'latitude' && index != 'longitude' && index != 'location') {
                    if (container.find(".index_"+index).length == 0) {
                        container.append('<div class="index_'+index+' mb-1"></div>');
                    }
                    container.find(".index_"+index).html(html);
                    last = index;
                }
            });

            container.find(".index_"+last).nextAll('div').remove();

            if (container.find(".index_1").length == 0) {
                Object.values(container.find(".index_0"))[0].classList.remove("mb-1");
            } else {
                Object.values(container.find(".index_0"))[0].classList.add("mb-1");
            }
        }

        function request()
        {
            return $.ajax({
                'type': 'POST',
                'url': settings.url,
                'data': {id: instance.attr('id'), source: settings.source, type_id: settings.type_id, value: instance.val(), hide_inactive: settings.hide_inactive, hide_empty: settings.hide_empty},
                'cache': false,
                'dataType': 'json',
                'success': function(response) {
                    return response;
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 5000
            });
        }
    }
}(jQuery));

(function($) {
    $.fn.mappicker = function(options) {

        var instance = this;

        var settings = $.extend({
            'latitude': '39.8283',
            'longitude': '-98.5795',
            'zoom': 4,
            'message': 'Click the map or drag the marker to select location',
            'provider': 'osm',
            'accessToken': '',
        }, options);

        var latitudeContainer = $('#latitude');
        var longitudeContainer = $('#longitude');
        var zoomContainer = $('#zoom');
        var latitude;
        var longitude;
        var zoom;

        latitude = settings.latitude;
        longitude = settings.longitude;
        zoom = settings.zoom;

        if (isCoordinate(latitudeContainer.val().replace(',', '.')) && isCoordinate(longitudeContainer.val().replace(',', '.'))) {
            latitude = latitudeContainer.val().replace(',', '.');
            longitude = longitudeContainer.val().replace(',', '.');
            zoom = zoomContainer.val();
        }

        if (settings.provider == 'osm') {
            var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
                maxZoom: 19,
                noWrap: true
            });
        } else if (settings.provider == 'mapbox') {
            var tiles = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 25,
                id: 'mapbox/streets-v11',
                accessToken: settings.accessToken,
                tileSize: 512,
                zoomOffset: -1,
                noWrap: true
            });
        }

        var myMap = L.map(instance.attr('id') + '-map', { dragging: !L.Browser.mobile, tap: !L.Browser.mobile })
            .setView([latitude, longitude], zoom)
            .addLayer(tiles)
            .addControl(new L.Control.Fullscreen());

        myMap.scrollWheelZoom.disable();

        var zoomTimer;

        myMap.on('mouseover', function() {
            zoomTimer = setTimeout(function(){
                myMap.scrollWheelZoom.enable();
            }, 3000);
        });

        myMap.on('mouseout', function() {
            clearTimeout(zoomTimer);
            myMap.scrollWheelZoom.disable();
        });

        var popup =  L.popup().setContent(settings.message);

        var marker = L.marker([latitude, longitude], {
            draggable: true,
            autoPan: true,
        });

        marker.bindPopup(popup).addTo(myMap).openPopup();
 
        myMap.on('click', function(event) {
            mapClick(event);
        });

        myMap.on('zoom', function() {
            zoomContainer.val(myMap.getZoom());
        });

        marker.on('dragend', markerReposition);

        function isCoordinate(value)
        {
            var coord_regex = /^[-|+]?[0-9]{1,3}(.[0-9]{0,15})?$/;

            return (coord_regex.test(value) && !isNaN(value));
        }

        function render(latitude, longitude, zoom, message) {
            if (isCoordinate(latitude) && isCoordinate(longitude)) {
                myMap.setView([latitude, longitude], zoom);
                marker.setLatLng([latitude, longitude]);
                latitudeContainer.val(latitude);
                longitudeContainer.val(longitude);
                zoomContainer.val(zoom);
            }
        }

        function mapClick(event)
        {
            marker.setLatLng(event.latlng);
            myMap.flyTo(event.latlng);
            latitudeContainer.val(event.latlng.lat);
            longitudeContainer.val(event.latlng.lng);
        }
        
        function markerReposition()
        {
            var latlng = marker.getLatLng()

            myMap.setView(latlng);
            latitudeContainer.val(latlng.lat);
            longitudeContainer.val(latlng.lng);
        }
    }
}(jQuery));

(function($) {
    $.fn.location = function(options) {

        var instance = this;

        var settings = $.extend({
            'url': '',
            'latitude': '39.8283',
            'longitude': '-98.5795',
            'zoom': 4,
            'message': 'Click the map or drag the marker to select location',
            'provider': 'osm',
            'accessToken': ''
        }, options);

        var container = $('#'+(this.attr('id'))+'-container');
        var latitudeContainer = $('#latitude');
        var longitudeContainer = $('#longitude');
        var zoomContainer = $('#zoom');
        var latitude;
        var longitude;
        var zoom;

        latitude = settings.latitude;
        longitude = settings.longitude;
        zoom = settings.zoom;

        if (isCoordinate(latitudeContainer.val().replace(',', '.')) && isCoordinate(longitudeContainer.val().replace(',', '.'))) {
            latitude = latitudeContainer.val().replace(',', '.');
            longitude = longitudeContainer.val().replace(',', '.');
            zoom = zoomContainer.val();
        }

        if (settings.provider == 'osm') {
            var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
                maxZoom: 19,
                noWrap: true
            });
        } else if (settings.provider == 'mapbox') {
            var tiles = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 25,
                id: 'mapbox/streets-v11',
                accessToken: settings.accessToken,
                tileSize: 512,
                zoomOffset: -1,
                noWrap: true
            });
        }

        var myMap = L.map(instance.attr('id') + '-map', { dragging: !L.Browser.mobile, tap: !L.Browser.mobile })
            .setView([latitude, longitude], zoom)
            .addLayer(tiles)
            .addControl(new L.Control.Fullscreen());

        myMap.scrollWheelZoom.disable();

        var zoomTimer;

        myMap.on('mouseover', function() {
            zoomTimer = setTimeout(function(){
                myMap.scrollWheelZoom.enable();
            }, 3000);
        });

        myMap.on('mouseout', function() {
            clearTimeout(zoomTimer);
            myMap.scrollWheelZoom.disable();
        });

        var popup =  L.popup().setContent(settings.message);

        var marker = L.marker([latitude, longitude], {
            draggable: true,
            autoPan: true,
        });

        marker.bindPopup(popup).addTo(myMap).openPopup();
 
        deferred = request();
        deferred.done(function(response) {
            process(response, false);
        });

        myMap.on('click', function(event) {
            mapClick(event);
        });

        myMap.on('zoom', function() {
            zoomContainer.val(myMap.getZoom());
        });

        marker.on('dragend', markerReposition);

        container.on('change', 'div[class^="index_"] > select', function() {
            instance.val($(this).find('option:selected').val()).trigger('change');
            deferred = request();
            deferred.done(function(response) {
                process(response, true);
            });
        });

        function isCoordinate(value)
        {
            var coord_regex = /^[-|+]?[0-9]{1,3}(.[0-9]{0,15})?$/;

            return (coord_regex.test(value) && !isNaN(value));
        }

        function process(response, reposition)
        {
            var last = 0;

            $.each(response, function(index, html) {
                if (index != 'latitude' && index != 'longitude' && index != 'location' && index != 'zoom') {
                    if (container.find(".index_"+index).length == 0) {
                        container.append('<div class="index_'+index+' mb-1"></div>');
                    }
                    container.find(".index_"+index).html(html);
                    last = index;
                }
            });

            container.find(".index_"+last).nextAll('div').remove();

            if (false !== reposition) {
                render(response.latitude, response.longitude, response.zoom, response.location);
            }
        }

        function request()
        {
            return $.ajax({
                'type': 'POST',
                'url': settings.url,
                'data': {id: instance.attr('id'), source: 'location', value: instance.val()},
                'cache': false,
                'dataType': 'json',
                'success': function(response) {
                    return response;
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 5000
            });
        }

        function render(latitude, longitude, zoom, message) {
            if (isCoordinate(latitude) && isCoordinate(longitude)) {
                myMap.setView([latitude, longitude], zoom);
                marker.setLatLng([latitude, longitude]);
                latitudeContainer.val(latitude);
                longitudeContainer.val(longitude);
                zoomContainer.val(zoom);
            }
        }

        function mapClick(event)
        {
            marker.setLatLng(event.latlng);
            myMap.flyTo(event.latlng);
            latitudeContainer.val(event.latlng.lat);
            longitudeContainer.val(event.latlng.lng);
        }
        
        function markerReposition()
        {
            var latlng = marker.getLatLng()
            myMap.setView(latlng);
            latitudeContainer.val(latlng.lat);
            longitudeContainer.val(latlng.lng);
        }
    }
}(jQuery));

(function($) {
    $.fn.searchResultsMap = function(options) {

        var instance = this;

        var settings = $.extend({
            'source': '{}',
            'provider': 'osm',
            'accessToken': ''
        }, options);

        if (Object.keys(settings.source.features).length == 0) {
            instance.remove();

            return false;
        }

        if (settings.provider == 'osm') {
            var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
                maxZoom: 19,
                noWrap: true
            });
        } else if (settings.provider == 'mapbox') {
            var tiles = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 25,
                id: 'mapbox/streets-v11',
                accessToken: settings.accessToken,
                tileSize: 512,
                zoomOffset: -1,
                noWrap: true
            });
        }

        var myMap = L.map(instance.attr('id'), { dragging: !L.Browser.mobile, tap: !L.Browser.mobile })
            .setView([0, 0], 0)
            .addLayer(tiles)
            .addControl(new L.Control.Fullscreen());

        myMap.scrollWheelZoom.disable();

        var zoomTimer;

        myMap.on('mouseover', function() {
            zoomTimer = setTimeout(function(){
                myMap.scrollWheelZoom.enable();
            }, 3000);
        });

        myMap.on('mouseout', function() {
            clearTimeout(zoomTimer);
            myMap.scrollWheelZoom.disable();
        });

        var markers = L.markerClusterGroup();

        var geoJSONLayer = L.geoJSON(settings.source, {
            pointToLayer: function (feature, latlng) {
                return L.marker(latlng, {
                    icon: L.icon.fontAwesome({
                        iconClasses: feature.properties.class,
                        markerColor: feature.properties.marker_color,
                        iconColor: feature.properties.icon_color,
                    })
                });
            },
            onEachFeature: function (feature, layer) {
                layer.bindPopup(feature.properties.popup);
            }
        });

        markers.addLayer(geoJSONLayer);

        myMap.addLayer(markers);

        myMap.fitBounds(markers.getBounds(), {
            padding: [50, 50],
        });

        $('.map').on('show', function () {
            myMap.invalidateSize();

            myMap.fitBounds(markers.getBounds(), {
                padding: [50, 50],
            });
        });
    }
}(jQuery));

(function($) {
    $.fn.widgetMap = function(options) {

        var instance = this;

        var settings = $.extend({
            'url': '',
            'latitude': '39.8283',
            'longitude': '-98.5795',
            'zoom': '4',
            'type_id': '',
            'provider': 'osm',
            'accessToken': ''
        }, options);

        if (settings.provider == 'osm') {
            var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
                maxZoom: 19,
                noWrap: true
            });
        } else if (settings.provider == 'mapbox') {
            var tiles = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 25,
                id: 'mapbox/streets-v11',
                accessToken: settings.accessToken,
                tileSize: 512,
                zoomOffset: -1,
                noWrap: true
            });
        }

        var myMap = L.map(instance.attr('id'), { dragging: !L.Browser.mobile, tap: !L.Browser.mobile });
        var markers = L.markerClusterGroup();

        myMap.on('load', function (event) { fetch(event); });

        myMap
            .setView([settings.latitude, settings.longitude], settings.zoom)
            .addLayer(tiles)
            .addControl(new L.Control.Fullscreen());

        myMap.addLayer(markers);

        myMap.scrollWheelZoom.disable();

        var zoomTimer;

        myMap.on('mouseover', function() {
            zoomTimer = setTimeout(function(){
                myMap.scrollWheelZoom.enable();
            }, 3000);
        });

        myMap.on('mouseout', function() {
            clearTimeout(zoomTimer);
            myMap.scrollWheelZoom.disable();
        });

        myMap.on('dragend', function (event) { fetch(event); });
        myMap.on('zoomend', function (event) { fetch(event); });

        function fetch(event)
        {
            $.ajax({
                'url': settings.url,
                'type': 'post',
                'data': {
                    north: event.target.getBounds().getNorth(),
                    west: event.target.getBounds().getWest(),
                    south: event.target.getBounds().getSouth(),
                    east: event.target.getBounds().getEast(),
                    type_id: settings.type_id,
                },
                'cache': false,
                'dataType': 'json',
                'success': function(response) {                   
                    markers.clearLayers();
                    
                    var geoJSONLayer = L.geoJSON(response, {
                        pointToLayer: function (feature, latlng) {
                            return L.marker(latlng, {
                                icon: L.icon.fontAwesome({
                                    iconClasses: feature.properties.class,
                                    markerColor: feature.properties.marker_color,
                                    iconColor: feature.properties.icon_color,
                                })
                            });
                        },
                        onEachFeature: function (feature, layer) {
                            layer.bindPopup(feature.properties.popup);
                        }
                    });
                    
                    markers.addLayer(geoJSONLayer);
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 10000
            });
        }
    
    }
}(jQuery));

(function($) {
    $.fn.listingMap = function(options) {

        var instance = this;

        var settings = $.extend({
            'latitude': '39.8283',
            'longitude': '-98.5795',
            'zoom': 17,
            'provider': 'osm',
            'accessToken': '',
            'icon_color': 'white',
            'marker_color': 'red',
            'class': '',
        }, options);

        if (isCoordinate(settings.latitude) && isCoordinate(settings.longitude)) {
            if (settings.provider == 'osm') {
                var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
                    maxZoom: 19,
                    noWrap: true
                });
            } else if (settings.provider == 'mapbox') {
                var tiles = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 25,
                    id: 'mapbox/streets-v11',
                    accessToken: settings.accessToken,
                    tileSize: 512,
                    zoomOffset: -1,
                    noWrap: true
                });
            }

            var myMap = L.map(instance.attr('id'), { dragging: !L.Browser.mobile, tap: !L.Browser.mobile })
                .setView([settings.latitude, settings.longitude], settings.zoom)
                .addLayer(tiles)
                .addControl(new L.Control.Fullscreen());

            myMap.scrollWheelZoom.disable();

            var zoomTimer;

            myMap.on('mouseover', function() {
                zoomTimer = setTimeout(function(){
                    myMap.scrollWheelZoom.enable();
                }, 3000);
            });

            myMap.on('mouseout', function() {
                clearTimeout(zoomTimer);
                myMap.scrollWheelZoom.disable();
            });

            var marker = L.marker([settings.latitude, settings.longitude], {
                icon: L.icon.fontAwesome({
                    iconClasses: settings.class,
                    iconColor: settings.icon_color,
                    markerColor: settings.marker_color,
                })
            }).addTo(myMap);
/*
            var myMapRouter = L.Routing.control({
                router: L.Routing.mapbox(settings.accessToken, {
                    profile: 'mapbox/driving',
                    language: 'en',
                    polylinePrecision: 5
                }),
                geocoder: L.Control.Geocoder.nominatim(),
                waypoints: [
                    L.latLng(settings.latitude, settings.longitude)
                ],
                routeWhileDragging: true,
                reverseWaypoints: true,
                showAlternatives: true,
                fitSelectedRoutes: true,
                altLineOptions: {
                    styles: [
                        {color: 'black', opacity: 0.15, weight: 9},
                        {color: 'white', opacity: 0.8, weight: 6},
                        {color: 'blue', opacity: 0.5, weight: 2}
                    ]
                },
            }).addTo(myMap);

            L.Routing.errorControl(myMapRouter).addTo(myMap);
*/
        }

        function isCoordinate(value)
        {
            var coord_regex = /^[-|+]?[0-9]{1,3}(.[0-9]{0,15})?$/;

            return (coord_regex.test(value) && !isNaN(value));
        }
    }
}(jQuery));

(function($) {
    $.fn.tree = function(options) {

        var instance = this;

        var selectButton = $('#'+this.attr('id')+'-select-all');
        var deselectButton = $('#'+this.attr('id')+'-deselect-all');

        var filterInput = $('#'+this.attr('id')+'-tree-search');
        var filterButton = $('#'+this.attr('id')+'-tree-search-clear');

        try {
            var value = JSON.parse(options.value);
        } catch (error) {
            var value = '[]';
        }

        var counter = $('#'+this.attr('id')+'-counter');
        var template = '{count} / {total}';        

        var settings = $.extend({
            'source': [],
            'limit': 1,
            'value': null,
            'leaves': true,
            'rtl': false
        }, options);

        settings.extensions = ['filter'];
        settings.filter = {
            autoExpand: true,
            mode: 'hide',
            nodata: false
        };

        settings.selectMode = 2;
        settings.checkbox = true;
        settings.activeVisible = true;
        settings.nodata = false;

        if (Number(settings.limit) > 1) {
            counter.html(template.replace('{count}', 0).replace('{total}', Number(settings.limit)));
        }

        if (Number(settings.limit) == 0) {
            settings.selectMode = 3;
        } else if (Number(settings.limit) == 1) {
            settings.selectMode = 1;
            settings.checkbox = 'radio';
        }

        settings.init = function(event, data) {
            data.tree.visit(function(node) {
                if (settings.leaves === true && node.hasChildren()) {
                    node.unselectable = true;
                    node.checkbox = false;
                    node.folder = true;
                }

                for (var i = 0; i < value.length; i++) {
                    if (value[i] == node.key) {
                        node.setSelected();

                        if (Number(settings.limit) == 1) {
                            node.setActive();
                        }
                    }
                }

                node.render(true);
            });
        };

        settings.beforeSelect = function(event, data) {
            if (Number(settings.limit) > 1) {
                if (data.node.isSelected() === true) {
                    counter.html(template.replace('{count}', data.tree.getSelectedNodes().length - 1).replace('{total}', Number(settings.limit)));
                } else {
                    if (data.tree.getSelectedNodes().length < Number(settings.limit)) {
                        counter.html(template.replace('{count}', data.tree.getSelectedNodes().length + 1).replace('{total}', Number(settings.limit)));
                    } else {
                        return false;
                    }
                }
            }
        };

        $('#'+instance.attr('id')).fancytree(settings);

        var tree = $.ui.fancytree.getTree('#'+instance.attr('id'));

        filterInput.on('keyup', function(e){
            var pattern = $(this).val();

            if (e && e.which === $.ui.keyCode.ESCAPE || $.trim(pattern) === "") {
                filterButton.trigger('click');

                return;
            }

            tree.filterNodes(pattern);
        });

        filterButton.click(function() {
            filterInput.val('');
            tree.clearFilter();

            return;
        });

        instance.closest('form').submit(function() {
            tree.generateFormElements(instance.attr('id') + '[]', false, {filter: function(node) {return node.isSelected() && node.unselectable !== true;}, stopOnParents: false});
        });

        if (Number(settings.limit) == 0) {
            selectButton.on('click', function() {
                tree.visit(function(node) {
                    node.setSelected(true);
                });
            });
            deselectButton.on('click', function() {
                tree.visit(function(node) {
                    node.setSelected(false);
                });
            });
        }
    }
}(jQuery));

if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;
}

(function($) {
    $.fn.dropzone = function(options) {

        var instance = this;

        var settings = $.extend({
            'url': '',
            'typeId': '1',
            'maxFiles': '1',
            'userCrop': false,
            'thumbnailWidth': 100,
            'thumbnailHeight': 100,
            'cropBoxWidth': 0,
            'cropBoxHeight': 0,
            'dictDefaultMessage': 'Drop files here to upload!',
            'dictButtonClose': 'Close',
            'dictButtonUpload': 'Upload',
            'dictButtonUpdate': 'Update',
            'dictButtonCrop': 'Crop',
            'dictButtonDescription': 'Description',
            'dictButtonRemove': 'Remove',
            'dictButtonZoomIn': 'Zoom In',
            'dictButtonZoomOut': 'Zoom Out',
            'dictButtonRotate': 'Rotate',
            'dictButtonReset': 'Reset',
            'dictErrorLimitReached': 'File Limit Per Field Reached'
        }, options);

        var cropperModalWindowTemplate = '' + 
            '<div class="cropper modal fade" tabindex="-1" role="dialog">' + 
                '<div class="modal-dialog" role="document">' + 
                    '<div class="modal-content">' + 
                        '<div class="modal-body">' + 
                            '<div class="image-container"></div>' + 
                        '</div>' +                      
                        '<div class="modal-footer">' + 
                            '<button type="button" class="btn btn-default crop-zoomin" title="' + settings.dictButtonZoomIn + '"><i class="fas fa-search-plus"></i></button>' + 
                            '<button type="button" class="btn btn-default crop-zoomout" title="' + settings.dictButtonZoomOut + '"><i class="fas fa-search-minus"></i></button>' + 
                            '<button type="button" class="btn btn-default crop-rotate" title="' + settings.dictButtonRotate + '"><i class="fas fa-redo"></i></button>' + 
                            '<button type="button" class="btn btn-default crop-reset" title="' + settings.dictButtonReset + '"><i class="fas fa-sync"></i></button>' + 
                            '<button type="button" class="btn btn-default" data-dismiss="modal">' + settings.dictButtonClose + '</button>' + 
                            '<button type="button" class="btn btn-primary crop-upload">' + settings.dictButtonUpload + '</button>' + 
                        '</div>' + 
                    '</div>' + 
                '</div>' + 
            '</div>' + 
        '';

        var descriptionModalWindowTemplate = '' + 
            '<div class="modal fade" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog" role="document">' +
                    '<div class="modal-content">' +
                        '<div class="modal-body">' +
                            '<div class="form-container" style="max-width: 100%;"></div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-default" data-dismiss="modal">' + settings.dictButtonClose + '</button>' +
                            '<button type="button" class="btn btn-primary update">' + settings.dictButtonUpdate + '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '';

        var dropzonePreviewTemplate = '' +
            '<div class="dz-preview dz-file-preview">' +
                '<div class="dz-image" style="width: ' + Number(settings.thumbnailWidth) + 'px; height: ' + Number(settings.thumbnailHeight) + 'px;">' +
                    '<img data-dz-thumbnail />' +
                '</div>' +
                '<div class="dz-details">' +
                    '<div class="btn btn-light dz-tools-cropper" title="' + settings.dictButtonCrop + '"><i class="fas fa-crop-alt"></i></div>' +
                    '<div class="btn btn-light dz-tools-description" title="' + settings.dictButtonDescription + '"><i class="fas fa-edit"></i></div>' +
                    '<div class="btn btn-danger dz-remove" title="' + settings.dictButtonRemove + '" data-dz-remove><i class="fas fa-trash-alt"></i></div>' +
                    '<div class="dz-filename"><span data-dz-name></span></div>' +
                '</div>' +
                '<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>' +
                '<div class="dz-success-mark"></div>' +
                '<div class="dz-error-mark"></div>' +
                '<div class="dz-error-message"><span data-dz-errormessage></span></div>' +
            '</div>' +
        '';

        var myDropzone = new Dropzone($('#'+this.attr('id')+'_container')[0], {
            url: settings.url + '/put',
            method: 'post',
            uploadMultiple: false,
            maxFiles: Number(settings.maxFiles),
            maxFilesize: Number(settings.maxSize),
            createThumbnail:true,
            thumbnailWidth: Number(settings.thumbnailWidth),
            thumbnailHeight: Number(settings.thumbnailHeight),
            previewTemplate: dropzonePreviewTemplate,
            addRemoveLinks: false,
            dictDefaultMessage: '<i class="fas fa-cloud-upload-alt"></i><br>' + settings.dictDefaultMessage,
            init: function() {
                $.ajax({
                    'url': settings.url + '/get',
                    'type': 'post',
                    'data': {type_id: settings.typeId, document_id: instance.val()},
                    'cache': false,
                    'dataType': 'json',
                    'success': function(response) {
                        $.each(response, function(index, obj) {
                            var file = {name: obj.name, size: obj.size, status: Dropzone.ADDED, accepted: true};
                            myDropzone.files.push(file);
                            myDropzone.emit("addedfile", file);
                            if (obj.hasOwnProperty('thumbnail') && obj.thumbnail != '') {
                                myDropzone.emit("thumbnail", file, obj.thumbnail);
                            }
                            myDropzone.emit("complete", file);
                            addTools(file, obj);
                        });
                    },
                    'error': function(jqXHR, error, error_text) {},
                    'timeout': 5000
                });
            }
        });

        myDropzone.on('sending', function(file, xhr, formdata) {
            formdata.append('document_id', instance.val());
            formdata.append('type_id', settings.typeId);
        });    

        myDropzone.on('success', function(file, response) {
            if (response.error) {
                alert(response.error);
                myDropzone.removeFile(file);
            }
            else {
                addTools(file, response);
            }
        });

        myDropzone.on('maxfilesexceeded', function(file) {
            alert(settings.dictErrorLimitReached);
            myDropzone.removeFile(file);
        });

        myDropzone.on('removedfile', function(file) {
            if (file.id) {
                $.ajax({
                    'type': 'POST',
                    'url': settings.url + '/remove',
                    'data': {id: file.id, type_id: settings.typeId, document_id: instance.val()},
                    'cache': false,
                    'dataType': 'json',
                    'success': function(response) {
                        return true;
                    },
                    'error': function(jqXHR, error, error_text) {},
                    'timeout': 5000
                });
            }
        });            

        function addTools(file, obj)
        {
            file.id = obj.id;

            if (obj.hasOwnProperty('thumbnail') && obj.thumbnail != '') {
                file.previewElement.querySelector('img').src = obj.thumbnail;
            }

            if (settings.userCrop === true && isImageByMime(obj.mime)) {
                file.previewElement.querySelector('.dz-tools-cropper').style.display = 'inline';
                addCropperListener(file.previewElement.querySelector('.dz-tools-cropper'), file.previewElement.querySelector('img'), obj.id, obj.url, obj.crop_data);
            }

            addDescriptionListener(file.previewElement.querySelector('.dz-tools-description'), obj.id);
        }

        function isImageByMime(mime)
        {
            return (['image/jpeg', 'image/png', 'image/gif', 'image/webp'].indexOf(mime) != -1);
        }

        function addCropperListener(element, thumbnailElement, id, src, crop_data)
        {
            element.addEventListener('click', function() {
                var img = $('<img />');
                var cropperModalWindow = $(cropperModalWindowTemplate);
                cropperModalWindow.find('.image-container').html(img);
                cropperModalWindow.modal('show');

                img.attr('src', src + '&' + (new Date()).getTime());
                img.cropper({
                    viewMode: 1,
                    aspectRatio: Number(settings.cropBoxWidth)/Number(settings.cropBoxHeight),
                    minContainerWidth: Number(settings.cropBoxWidth),
                    minContainerHeight: Number(settings.cropBoxHeight),
                    guides: false,
                    dragCrop: false,
                    cropBoxMovable: false,
                    dragMode: 'move',
                    toggleDragModeOnDblclick: false,
                    cropBoxResizable: false,
                    ready: function () {
                        var container = img.cropper('getContainerData');
                        var cropBoxWidth = Number(settings.cropBoxWidth);
                        var cropBoxHeight = Number(settings.cropBoxHeight);
                       
                        img.cropper('setCropBoxData', {
                            width: cropBoxWidth,
                            height: cropBoxHeight,
                            left: (container.width - cropBoxWidth) / 2,
                            top: (container.height - cropBoxHeight) / 2
                        });

                        if (crop_data) {
                            img.cropper('setData', JSON.parse(crop_data));
                        }
                    }
                });

                cropperModalWindow.find('.crop-upload').on('click', function() {
                    $.ajax({
                        'type': 'POST',
                        'url': settings.url + '/crop',
                        'data': {id: id, type_id: settings.typeId, document_id: instance.val(), data: JSON.stringify(img.cropper('getData', true))},
                        'cache': false,
                        'dataType': 'json',
                        'success': function(response) {
                            thumbnailElement.src = response.thumbnail;
                        },
                        'error': function(jqXHR, error, error_text) {},
                        'timeout': 5000
                    });           
                    cropperModalWindow.modal('hide');
                });

                cropperModalWindow.find('.crop-zoomin').on('click', function() {
                    img.data('cropper').zoom(0.1);
                });

                cropperModalWindow.find('.crop-zoomout').on('click', function() {
                    img.data('cropper').zoom(-0.1);
                });

                cropperModalWindow.find('.crop-reset').on('click', function() {
                    img.data('cropper').reset();
                });

                cropperModalWindow.find('.crop-rotate').on('click', function() {
                    img.data('cropper').rotate(90);
                });
            });
        }

        function addDescriptionListener(element, id)
        {
            element.addEventListener('click', function() {
                var descriptionModalWindow = $(descriptionModalWindowTemplate);
                $.ajax({
                    'type': 'POST',
                    'url': settings.url + '/info',
                    'data': {id: id, type_id: settings.typeId, document_id: instance.val()},
                    'cache': false,
                    'dataType': 'json',
                    'success': function(response) {
                        descriptionModalWindow.find('.form-container').html(response.html);
                    },
                    'error': function(jqXHR, error, error_text) {},
                    'timeout': 5000
                });

                descriptionModalWindow.modal('show');

                descriptionModalWindow.find('.update').on('click', function() {
                    $.ajax({
                        'type': 'POST',
                        'url': settings.url + '/update',
                        'data': {id: id, type_id: settings.typeId, document_id: instance.val(), title: descriptionModalWindow.find('input[name="title"]').val(), description: descriptionModalWindow.find('textarea[name="description"]').val()},
                        'cache': false,
                        'dataType': 'json',
                        'success': function(response) {
                            descriptionModalWindow.modal('hide');
                        },
                        'error': function(jqXHR, error, error_text) {},
                        'timeout': 5000
                    });
                });            
            });
        }
    };
}(jQuery));

(function($) {
    $.fn.range = function(options) {

        var instance = this;

        var settings = $.extend({
            'min': 0,
            'max': 10,
            'step': 1,
            'direction': 'ltr',
            'template': '{min} - {max}'
        }, options);

        var container = document.getElementById(this.attr('id')+'-container');
        var details = $('#'+this.attr('id')+'-counter');

        noUiSlider.create(container, {
            range: {'min': [Number(settings.min)], 'max': [Number(settings.max)]},
            start: [0, 0],
            step: Number(settings.step),
            connect: true,
            direction: settings.direction
        });

        value = [Number(settings.min), Number(settings.max)];
            
        if(instance.val().match(/;/)) {
            temp = instance.val().split(';');
            if (is_number(temp[0]) && is_number(temp[1]))
                value = [temp[0], temp[1]];
        }

        container.noUiSlider.set(value);
        details.html(settings.template.replace('{min}', value[0]).replace('{max}', value[1]));
        instance.val(value[0] + ';' + value[1]);

        container.noUiSlider.on('update', function() {
            value = container.noUiSlider.get();
            details.html(settings.template.replace('{min}', value[0]).replace('{max}', value[1]));
            $('#'+instance.attr('id')).val(value[0]+';'+value[1]);
        });

        function is_number(value)
        {
            regex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;

            if(regex.test(value))
                return true;

            return false;
        }
    }
}(jQuery));

(function($) {
    $.fn.sortable = function(options) {

        var instance = this;

        var settings = $.extend({
            'url': '',
            'source': '',
            'data': '',
            'speed': 160
        }, options);

        var currentEvent;

        var sortable = new Sortable($('#'+this.attr('id'))[0], {
            handle: '.draggable',
            animation: Number(settings.speed),
            onMove: function (event) {
                currentEvent = event;
            },
            onEnd: function (event) {
                return $.ajax({
                    'type': 'POST',
                    'url': settings.url,
                    'data': {
                        source: settings.source,
                        data: settings.data,
                        dragged: currentEvent.dragged.cells[0].dataset.id,
                        related: currentEvent.related.cells[0].dataset.id,
                        after: currentEvent.willInsertAfter
                    },
                    'cache': false,
                    'dataType': 'text',
                    'success': function(response) {
                    },
                    'error': function(jqXHR, error, error_text) {},
                    'timeout': 5000
                });
            }
        });
    }
}(jQuery));

(function($) {
    $.fn.hours = function(options) {

        var instance = this;

        var settings = $.extend({
            'url': '',
        }, options);

        var container = $('#'+(this.attr('id'))+'-container');
        var dow = $('#'+(this.attr('id'))+'-dow');
        var start = $('#'+(this.attr('id'))+'-start input');
        var end = $('#'+(this.attr('id'))+'-end input');
        var save = $('#'+(this.attr('id'))+'-save');

        $.ajax({
            'type': 'POST',
            'url': settings.url,
            'data': {action: 'get', hash: instance.val()},
            'cache': false,
            'dataType': 'text',
            'success': function(response) {
                container.html(response);
            },
            'error': function(jqXHR, error, error_text) {},
            'timeout': 5000
        });

        save.on('click', function (e) {
            $.ajax({
                'type': 'POST',
                'url': settings.url,
                'data': {action: 'post', hash: instance.val(), dow: dow.val(), start: start.val(), end: end.val()},
                'cache': false,
                'dataType': 'text',
                'success': function(response) {
                    container.html(response);
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 5000
            });
        });

        container.on('click', 'button', function (e) {
            $.ajax({
                'type': 'POST',
                'url': settings.url,
                'data': {action: 'delete', hash: instance.val(), id: $(this).data('id')},
                'cache': false,
                'dataType': 'text',
                'success': function(response) {
                    container.html(response);
                },
                'error': function(jqXHR, error, error_text) {},
                'timeout': 5000
            });
        });
    }
}(jQuery));
