jQuery(function ($) {
    $("#slider-block").slick({
        speed: 300,
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: false,
        fade: true,
        adaptiveHeight: true,
        appendDots: $(".controls .dots"),
    });
});
