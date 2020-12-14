jQuery(document).ready(function($) {
    var $sermonGroup = $('.jrny-sermon-group');
    $sermonGroup.each(function() {
        $(this).removeClass('is-loading');
        const slickEl = $(this).find('.jrny-sermon-group__sermons');
        const nextButton = $(this).find('.jrny-sermon-group__btn--next');
        const prevButton = $(this).find('.jrny-sermon-group__btn--prev');
        slickEl.slick({
            slidesToShow: 3,
            infinite: false,
            prevArrow: prevButton,
            nextArrow: nextButton,
        });
    });
});