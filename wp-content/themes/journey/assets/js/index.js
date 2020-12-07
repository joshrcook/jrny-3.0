jQuery(document).ready(function($) {
    $('.jrny-sermon-group').each(function() {
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