require([
        'jquery',
        'testimonialSlider',        'testimonialmodal'
    ], function($){
         jQuery(".testimonialslider .slides").owlCarousel({
        autoPlay: 5000,
        items : 3,
        itemsDesktop : [1199,3],
        itemsTablet: [768,2],
        itemsDesktopSmall : [979,2],
        itemsMobile : [750,1],
        pagination:true,
        navigation:false
      });
    });
