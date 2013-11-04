jQuery(document).ready(function(){	

	// Slider Homepage
	jQuery('#slide .placeholder').cycle({
		fx:'scrollHorz',
		timeout:50,
		speed:2500,
		slideExpr:'.panel',
		pager:'#pager',
			pagerAnchorBuilder:function(idx, slide){
			return '<a href="#">&nbsp;</a>';
		}
	});
	
	// Product Slider
    jQuery('#featured-products').jcarousel();
	jQuery('a.img-display').mouseenter(function(){
		jQuery(this).find('.category-description-home').fadeIn(250).css("display", "block");
		}).mouseleave(function(){
		jQuery(this).find('.category-description-home').fadeOut(250).css("display", "none");
	});

	//Back to top slider
    jQuery('a[href=#totop]').click(function(){
        jQuery('html, body').animate({scrollTop:0}, 600);
        return false;
    });
	
	// Custom Menu
	jQuery(".category").click(function() {
		var open = this.getAttributeNode('lang').value;
		jQuery(".subcategory_" + open).slideToggle('medium');
	});	

	// FancyBox jQuery
	jQuery("a.group").fancybox({ 'zoomSpeedIn': 300, 'zoomSpeedOut': 300, 'overlayShow': true }); 

	// Cart Popup
	jQuery('.mycart').hover(function(){
        jQuery(this).toggleClass('up');
    });
	
	jQuery('.mycart').click(function() {
		jQuery('#mycart-block').slideToggle('slow');
	});

	jQuery('#mycart-block').stickyfloat({ 
		duration: 600,
		offsetY: 185,
	});
	
	//Border Style
	jQuery('ul#left-nav li.category:last').css("border-bottom", "1px solid #BAB0A8");
	
	//Corner Style
	jQuery('#navigation, .mycart').corner('4px top');	
	jQuery('.block-content ol li.item, .pages li ').corner('4px');	
	jQuery('.my-wishlist button.btn-cart, a.add-to-cart ').corner('6px');	
	jQuery('.cart-count .subtotal').corner('4px left');
	
	// Sidebar Height IE FIX
	if ( jQuery.browser.msie ) {
		jQuery('#col-right').height(jQuery('#col-right').parents('.middle').outerHeight());
	}	
});