jQuery(document).ready(function(){	
	//Back to top slider
    jQuery('a[href=#totop]').click(function(){
        jQuery('html, body').animate({scrollTop:0}, 600);
        return false;
    });
	// Product Slider
    jQuery('#featured-products').jcarousel();
	jQuery('#featured-products2').jcarousel();

	// FancyBox jQuery
	jQuery("a.group").fancybox({ 'zoomSpeedIn': 300, 'zoomSpeedOut': 300, 'overlayShow': true }); 

	// Slider Homepage
	jQuery("#slider")
	    .cycle({
          fx: 'fadeout',
          speed: 3000,
          slideExpr: 'ul li',
          timeout: 6000,
          pager: '#controls'
     }); 
});
