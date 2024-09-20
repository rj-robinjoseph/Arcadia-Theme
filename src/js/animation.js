import { gsap, Power4 } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
// import { DrawSVGPlugin } from 'gsap/DrawSVGPlugin';
import { CSSPlugin } from 'gsap/CSSPlugin';

gsap.registerPlugin(ScrollTrigger, CSSPlugin);

gsap.config({
    nullTargetWarn: false,
});

document.addEventListener('DOMContentLoaded', () => {
    const slideInElements = document.querySelectorAll('.animate-site .main-content > div:not(.combo), .animate-site .main-content > div.combo > div');

    const slideUpTargets = [
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'p',
        'li',
        'blockquote',

        // Components
        '.btn',

        // Blocks
        '.content-grid .item',

        // Formidable
        '.frm_submit',
        '.frm_forms .form-field',
    ].join(',');

    const slideFromLeft = [
        '.accent-image.align-left .accent',
    ].join(',');

    const slideFromRight = [
        '.accent-image.align-right .accent',
    ].join(',');

    const slideFromLeftLong = [
        '.blank',
    ].join(',');

    const slideFromRightLong = [
        '.blank',
    ].join(',');

    setTimeout(() => {
        slideInElements.forEach((slideInElement) => {
            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: slideInElement,
                    start: 'top 75%',
                    // markers: true,
                },
            });

            const slideUpElements = slideInElement.querySelectorAll(slideUpTargets);
            const fromLeft = slideInElement.querySelectorAll(slideFromLeft);
            const fromRight = slideInElement.querySelectorAll(slideFromRight);
            const fromLeftLong = slideInElement.querySelectorAll(slideFromLeftLong);
            const fromRightLong = slideInElement.querySelectorAll(slideFromRightLong);

            [...slideUpElements, ...fromLeft, ...fromRight, ...fromLeftLong, ...fromRightLong].forEach((item) => {
                item.style.visibility = 'visible';

                gsap.set((item), {
                    transformOrigin: 'center center',
                });
            });

            tl.addLabel('start');
            tl.from(slideUpElements, { y: 25, opacity: 0, duration: .5, stagger: 0.125, ease: Power4.easeOut }, 'start');
            tl.from(fromLeft, { x: -25, opacity: 0, duration: .5, stagger: 0.125, ease: Power4.easeOut }, 'start');
            tl.from(fromRight, { x: 25, opacity: 0, duration: .5, stagger: 0.125, ease: Power4.easeOut }, 'start');
            tl.from(fromLeftLong, { x: -2000, opacity: 0, duration: .5, stagger: 0.125, ease: Power4.easeOut }, 'start');
            tl.from(fromRightLong, { x: 2000, opacity: 0, duration: .5, stagger: 0.125, ease: Power4.easeOut }, 'start');
        });
    }, 400);
});
