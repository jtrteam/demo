var $jQ = jQuery.noConflict();

$jQ(document).ready(function(){	

	// FancyBox jQuery
	jQuery("a.group").fancybox({ 'zoomSpeedIn': 300, 'zoomSpeedOut': 300, 'overlayShow': true }); 
	
	$jQ('#featured').orbit({
		 animation: 'fade',                  // fade, horizontal-slide, vertical-slide, horizontal-push
		 animationSpeed: 800,                // how fast animtions are
		 timer: true, 			 // true or false to have the timer
		 resetTimerOnClick: false,           // true resets the timer instead of pausing slideshow progress
		 advanceSpeed: 4000, 		 // if timer is enabled, time between transitions 
		 pauseOnHover: false, 		 // if you hover pauses the slider
		 startClockOnMouseOut: false, 	 // if clock should start on MouseOut
		 startClockOnMouseOutAfter: 1000, 	 // how long after MouseOut should the timer start again
		 directionalNav: false, 		 // manual advancing directional navs
		 captions: false, 			 // do you want captions?
		 captionAnimation: 'fade', 		 // fade, slideOpen, none
		 captionAnimationSpeed: 800, 	 // if so how quickly should they animate in
		 bullets: false,			 // true or false to activate the bullet navigation
		 bulletThumbs: false,		 // thumbnails for the bullets
		 bulletThumbLocation: '',		 // location from this file where thumbs will be
		 afterSlideChange: function(){}, 	 // empty function 
		 fluid: true                         // or set a aspect ratio for content slides (ex: '4x3') 
	});	
	
	$jQ('#brands').elastislide({
		imageW  : 230
	});
	
	$jQ('#brands li a').click(function(){
		var url = $jQ(this).attr('href');
		window.location = url;
	});
	
	//Top Cart
	$jQ('.topCart .ahref').click(function(){
		$jQ('.mini-cart').slideToggle('medium');
	});
	
	//Mini Login
	$jQ('#linkLogin').click(function(){
		$jQ('.top-login .mini-login').slideToggle('medium');
	});
	
	//My Account
	$jQ('.account-create .fieldset:last').css('margin-right','0');
	
	//Vertical Navigation
	$jQ(".category span").click(function() {
		var open = this.getAttributeNode('lang').value;
		$jQ(".subcategory_" + open).slideToggle('medium');
		$jQ(".subcategory_" + open).parent().prev().toggleClass('openn');
	}); 
	
	$jQ("#left-nav li.category, #left-nav li.cate").mouseenter(function() {
		$jQ(this).addClass('over');
	}).mouseleave(function() {
		$jQ(this).removeClass('over');
	}); 
	
	
	$jQ('#left-nav .cate.active').parents('#left-nav').find('.category.active a').css({'background': 'none repeat scroll 0 -2px transparent', 'color': '#999999'});
	$jQ('#left-nav .cat.active').parents('#left-nav').find('.category.active a').css({'background': 'none repeat scroll 0 -2px transparent', 'color': '#999999'})
	$jQ('#left-nav .cat.active').parents('#left-nav').find('.cate.active a').css({'background': 'none repeat scroll 0 -2px transparent', 'color': '#666666'})
	
	
	var select = $jQ('.store-switch li.selected').html();
	$jQ('.selectSwitch').append(select);
	$jQ(".selectSwitch").click(function() {
		$jQ('.selectSwitch').html('');
		var select = $jQ('.store-switch li.selected').html();
		$jQ('.selectSwitch').append(select);
		$jQ('.ulSelect').slideToggle('medium');
	});  


	var wt = $jQ(window).width();
	if(wt < 480) {
		$jQ('.cms-home .block.block-leftnav').insertAfter('.header.container');
		$jQ('.site-links').append('<div class="topbarMenu"></div>');
		$jQ('.site-links ul.links').css('display','none');
		$jQ('.header .site-links li a[title="My Wishlist"], .header .site-links li a[title="Log In"], .header .site-links li a[title="Log Out"]').parent('li').css('display','none');
		$jQ('.topbarMenu').click(function(){
			$jQ('.site-links ul.links').slideToggle('medium');
		});
	} return false;

	$jQ("ul#nav li").find('ul').append('<span class="arrow"></span>');

	function switcher () {	
		$jQ('.switcher a.switch').click(function(){
			$jQ(this).toggleClass("hover");
			$jQ(this).next('.options').slideToggle(100);
		});
	}

	function layered_nav () {
		$jQ('.block-layered-nav dt').each(function(){
			$jQ(this).addClass('hover');
			$jQ(this).toggle(function(){
				$jQ(this).removeClass('hover').next().slideUp(200);
			},function(){
				$jQ(this).addClass('hover').next().slideDown(200);
			})
		});                        		
		$jQ('.block-layered-nav dt').append('<span class="toggle"></span>');
	}
		
	layered_nav ();			
	switcher();
		
});