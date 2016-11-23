jQuery(function() {
    jQuery(document).foundation();

    jQuery('.fancybox').fancybox({
        maxWidth: 600,
        scrolling: 'no',
        helpers: {
            overlay: {
                locked: false
            },
            title: false
        }
    });

    jQuery('.slick').slick();

    if(jQuery('.logged-in')) {
        jQuery('.add-wishlist').on("click", function () {
            //return false;
        });
    }

    jQuery('.scroll-top').on("click", function(e){
        jQuery("html, body").animate({ scrollTop: 0 }, "slow");
        e.preventDefault();
        return false;
    });

    jQuery('.comments .primary.button').on('click', function(){
        jQuery('.comments .review-form').stop(true, true).slideToggle();
    });

    //stLight.options({publisher: "31abfba6-0978-4139-8479-d6e96f61d25f-10exp-N", doNotHash: true, doNotCopy: true, hashAddressBar: true});

    jQuery.ajax({
        url : cart.ajax_url,
        type : 'post',
        data : {
            action : 'count_cart',
        },
        success : function( response ) {
            if(response == 0) {
                jQuery('li.cart a span').remove();
                return;
            } else {
                jQuery('li.cart a span').remove();
                jQuery('li.cart a').append('<span class="badge">' + response + '</span>');
            }
        }
    });
});

var _mfq = _mfq || [];
(function() {
    var mf = document.createElement("script");
    mf.type = "text/javascript"; mf.async = true;
    mf.src = "//cdn.mouseflow.com/projects/12c3a3ac-cf66-422e-854a-5833e958148d.js";
    document.getElementsByTagName("head")[0].appendChild(mf);
})();
