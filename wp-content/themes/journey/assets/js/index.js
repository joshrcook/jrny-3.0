// Initialize sermons
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

// Init the live banners
jQuery(document).ready(function($) {
    $('[banner="live"]').slideDown();
});

// Save the personalization response to a cookie
jQuery(document).ready(function($) {
    $('[personalize]').click(function() {
        var personalizeValue = $(this).attr('personalize');
        Cookies.set('personalize', $(this).attr('personalize'), { expires: 365 });
        $.post(ajaxUrl, { action: 'save_personalize_value', personalize: personalizeValue }, function(data) {
            console.log('personalize response', data);
        });
        $('body').addClass(`personalize-${personalizeValue}`)
    });
});