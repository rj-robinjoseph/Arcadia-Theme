import GoogleMap from './gmaps';
import SmoothScroll from './smoothscroll';
import './animation';

/**
 * Activate smooth scroll
 */
const scroll = new SmoothScroll();

/**
 * Google maps
 */
const maps = document.querySelectorAll('.acf-map');

Array.from(maps).forEach((map) => {
    const markers = map.querySelectorAll('.marker');

    if (markers.length === 0) {
        return;
    }

    const gmap = new GoogleMap(map);

    if (map.hasAttribute('data-icon')) {
        gmap.setIcon(map.getAttribute('data-icon'));
    }

    if (map.hasAttribute('data-cluster-path')) {
        gmap.enableClustering({
            imagePath: map.getAttribute('data-cluster-path'),
        });
    }

    Array.from(markers).forEach((marker) => {
        gmap.addMarker({
            lat: marker.getAttribute('data-lat'),
            lng: marker.getAttribute('data-lng'),
            info: marker.innerHTML,
        });
    });

    gmap.centerMap();
});

/**
 * Add anchor link tracking
 */
const links = document.querySelectorAll('a[href*="#"]');

Array.from(links).forEach((element) => {
    let href = element.getAttribute('href');

    // Clean up URL
    if (href.indexOf('#') !== false) {
        href = href.substr(href.indexOf('#'));
    }

    if (href.length > 2) {
        element.addEventListener('click', (e) => {
            // Make sure the anchor is on the current page
            if (location.pathname.replace(/^\//, '') === element.pathname.replace(/^\//, '')
                && location.hostname === element.hostname) {
                e.preventDefault();
                scroll.to(href);
            }
        });
    }
});

/**
 * Check URL for hash
 */
window.addEventListener('load', () => {
    if (window.location.hash) {
        scroll.to(window.location.hash);
    }
});

/**
 * Mobile Menu
 */
const mobileMenu = document.querySelector('.mobile-menu');

if (mobileMenu) {
    mobileMenu.addEventListener('click', () => {
        const nav = document.querySelector('.mobile-navigation');

        mobileMenu.classList.toggle('is-active');
        nav.classList.toggle('is-open');
    });
}
