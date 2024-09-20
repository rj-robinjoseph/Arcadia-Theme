/* global google, MarkerClusterer */
export default class GoogleMap {
    constructor(el, config = {}) {
        this.args = Object.assign({
            zoom: 16,
            center: new google.maps.LatLng(0, 0),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false,
            gestureHandling: 'cooperative',
        }, config);

        this.map = new google.maps.Map(el, this.args);
        this.markers = [];
        this.cluster = null;

        this.setIcon();

        return this;
    }
    setIcon(icon = null) {
        this.icon = icon;
    }
    enableClustering(options = {}) {
        this.cluster = new MarkerClusterer(this.map, this.markers, options);
    }
    addMarker(options = {}) {
        const position = new google.maps.LatLng(options.lat, options.lng);

        const marker = new google.maps.Marker({
            position,
            map: this.map,
            icon: options.icon ?? this.icon,
        });

        if (this.cluster !== null) {
            this.cluster.addMarker(marker);
        }

        if (options.info) {
            const infowindow = new google.maps.InfoWindow({
                content: options.info,
            });

            marker.infoWindow = infowindow;

            google.maps.event.addListener(marker, 'click', () => {
                this.closeOpenWindows();
                infowindow.open(this.map, marker);
                this.map.panTo(position);
            });
        }

        this.markers.push(marker);
        
        return marker;
    }
    centerMap() {
        const bounds = new google.maps.LatLngBounds();

        this.markers.forEach((marker) => {
            const latlng = new google.maps.LatLng(marker.position.lat(), marker.position.lng());
            bounds.extend(latlng);
        });

        if (this.markers.length === 1) {
            this.map.setCenter(bounds.getCenter());
            this.map.setZoom(this.args.zoom);
        } else {
            this.map.fitBounds(bounds);
        }
    }
    closeOpenWindows() {
        this.markers.forEach((marker) => {
            if (marker.infoWindow) {
                marker.infoWindow.close();
            }
        });
    }
}
