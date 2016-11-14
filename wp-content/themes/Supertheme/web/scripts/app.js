jQuery(function() {
    jQuery(document).foundation();
    jQuery('.fancybox').fancybox({
        maxWidth: 600,
        helpers: {
            overlay: {
                locked: false
            }
        }
    });
    jQuery('.slick').slick();
});

jQuery('.scroll-top').on("click", function(e){
    jQuery("html, body").animate({ scrollTop: 0 }, "slow");
    e.preventDefault();
    return false;
});

jQuery('.comments .primary.button').on('click', function(){
   jQuery('.comments .review-form').stop(true, true).slideToggle();
});


//stLight.options({publisher: "31abfba6-0978-4139-8479-d6e96f61d25f-10exp-N", doNotHash: true, doNotCopy: true, hashAddressBar: false});
