function hefct() {
	var scrollPosition = jQuery(window).scrollTop();
	jQuery('#header-image').css('top', (0 - (scrollPosition * 0.2)) + 'px');
}
jQuery(document).ready(function() {
	
	jQuery("time.entry-date").timeago();
	
	jQuery(document).ready(function() {
		jQuery('.main-navigation .menu ul').superfish({
			delay:       700,                            // 1 second avoids dropdown from suddenly disappearing
			animation:   {opacity:'show',height:'hide'},  // fade-in and slide-down animation
			speed:       'fast',                          // faster animation speed
			autoArrows:  false                           // disable generation of arrow mark-up
		});
	});
		
	jQuery(window).bind('scroll', function() {
		hefct();
	});		

});	
jQuery(window).load(function(){
	jQuery('#slider').nivoSlider({
		effect:'boxRainGrowReverse,boxRain,boxRainGrow',
		pauseTime: 6000,
		animSpeed: 500,
		boxCols: 10,
		boxRows: 6,
		slices: 20,
		beforeChange: function() {
			jQuery('#slider .nivo-caption').animate({
												opacity: 0
												},300,'linear');
			jQuery('#slider .nivo-caption div.slide-title').animate({
												marginLeft:'-100px',
												opacity: 0
												},300,'easeOutQuint');
			jQuery('#slider .nivo-caption div.slide-description').animate({
												marginLeft:'-50px',
												opacity: 0
												},400);
												
		},
		afterChange: function() {
			//jQuery('#slider img.nivo-main-image').transition({'transform': 'scale(2) !important',duration:3000});
			jQuery('#slider .nivo-caption').animate({
												opacity: 1
												},800,'linear');
			jQuery('#slider .nivo-caption div.slide-title').css({ marginLeft:'-100px',opacity:0});
			jQuery('#slider .nivo-caption div.slide-title').animate({
												marginLeft: 0,
												opacity: 1
												},1200,'easeOutQuint');
												
			jQuery('#slider .nivo-caption div.slide-description').css({marginLeft:'-200px',opacity:0});		
			jQuery('#slider .nivo-caption div.slide-description').animate({
												marginLeft:'20px',
												opacity: 1
												},1600,'easeInOutBack');	
			jQuery('#slider img.nivo-main-image').width('110%');
			jQuery('#slider .slide img').animate({marginLeft:'100px'},3000)
																													
		}

		
	});
});