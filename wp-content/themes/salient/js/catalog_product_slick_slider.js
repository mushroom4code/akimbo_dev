jQuery(document).ready(function ($) {
    $('.slick-slider-product').slick({
        arrows: true,
        prevArrow:
            '<span class="product-item-detail-slider-left carousel_elem_custom" ' +
            'data-entity="slider-control-left" style="">' +
            '<i class="fa fa-angle-left" aria-hidden="true"></i></span>',
        nextArrow: '<span class="product-item-detail-slider-right carousel_elem_custom" ' +
            'data-entity="slider-control-right" style=""' +
            '><i class="fa fa-angle-right" aria-hidden="true"></i></span>',
        slidesToShow: 1,
        slidesToScroll: 1,
    });
});