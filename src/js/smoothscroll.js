import polyfill from 'smoothscroll-polyfill';

export default class SmoothScroll {
    constructor(options = {}) {
        this.options = Object.assign({
            behavior: 'smooth',
        }, options);

        polyfill.polyfill();
    }
    to(hash) {
        const targetElement = document.querySelector(hash);
        const target = !targetElement ? document.querySelector(`[name=${hash.slice(1)}]`) : targetElement;

        if (target) {
            target.scrollIntoView({
                behavior: this.options.behavior,
            });
        }
    }
}
