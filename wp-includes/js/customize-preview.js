<<<<<<< HEAD
(function(b,d){var c=wp.customize,a;a=function(g,e,f){var h;return function(){var i=arguments;f=f||this;clearTimeout(h);h=setTimeout(function(){h=null;g.apply(f,i)},e)}};c.Preview=c.Messenger.extend({initialize:function(g,f){var e=this;c.Messenger.prototype.initialize.call(this,g,f);this.body=d(document.body);this.body.on("click.preview","a",function(h){h.preventDefault();e.send("scroll",0);e.send("url",d(this).prop("href"))});this.body.on("submit.preview","form",function(h){h.preventDefault()});this.window=d(window);this.window.on("scroll.preview",a(function(){e.send("scroll",e.window.scrollTop())},200));this.bind("scroll",function(h){e.window.scrollTop(h)})}});d(function(){c.settings=window._wpCustomizeSettings;if(!c.settings){return}var f,e;f=new c.Preview({url:window.location.href,channel:c.settings.channel});f.bind("settings",function(g){d.each(g,function(i,h){if(c.has(i)){c(i).set(h)}else{c.create(i,h)}})});f.trigger("settings",c.settings.values);f.bind("setting",function(g){var h;g=g.slice();if(h=c(g.shift())){h.set.apply(h,g)}});f.bind("sync",function(g){d.each(g,function(i,h){f.trigger(i,h)});f.send("synced")});f.bind("active",function(){if(c.settings.nonce){f.send("nonce",c.settings.nonce)}});f.send("ready");e=d.map(["color","image","position_x","repeat","attachment"],function(g){return"background_"+g});c.when.apply(c,e).done(function(j,i,m,h,l){var n=d(document.body),o=d("head"),g=d("#custom-background-css"),k;if(n.hasClass("custom-background")&&!g.length){return}k=function(){var p="";n.toggleClass("custom-background",!!(j()||i()));if(j()){p+="background-color: "+j()+";"}if(i()){p+='background-image: url("'+i()+'");';p+="background-position: top "+m()+";";p+="background-repeat: "+h()+";";p+="background-position: top "+l()+";"}g.remove();g=d('<style type="text/css" id="custom-background-css">body.custom-background { '+p+" }</style>").appendTo(o)};d.each(arguments,function(){this.bind(k)})})})})(wp,jQuery);
=======
(function( exports, $ ){
	var api = wp.customize,
		debounce;

	debounce = function( fn, delay, context ) {
		var timeout;
		return function() {
			var args = arguments;

			context = context || this;

			clearTimeout( timeout );
			timeout = setTimeout( function() {
				timeout = null;
				fn.apply( context, args );
			}, delay );
		};
	};

	api.Preview = api.Messenger.extend({
		/**
		 * Requires params:
		 *  - url    - the URL of preview frame
		 */
		initialize: function( params, options ) {
			var self = this;

			api.Messenger.prototype.initialize.call( this, params, options );

			this.body = $( document.body );
			this.body.on( 'click.preview', 'a', function( event ) {
				event.preventDefault();
				self.send( 'scroll', 0 );
				self.send( 'url', $(this).prop('href') );
			});

			// You cannot submit forms.
			// @todo: Allow form submissions by mixing $_POST data with the customize setting $_POST data.
			this.body.on( 'submit.preview', 'form', function( event ) {
				event.preventDefault();
			});

			this.window = $( window );
			this.window.on( 'scroll.preview', debounce( function() {
				self.send( 'scroll', self.window.scrollTop() );
			}, 200 ));

			this.bind( 'scroll', function( distance ) {
				self.window.scrollTop( distance );
			});
		}
	});

	$( function() {
		api.settings = window._wpCustomizeSettings;
		if ( ! api.settings )
			return;

		var preview, bg;

		preview = new api.Preview({
			url: window.location.href,
			channel: api.settings.channel
		});

		preview.bind( 'settings', function( values ) {
			$.each( values, function( id, value ) {
				if ( api.has( id ) )
					api( id ).set( value );
				else
					api.create( id, value );
			});
		});

		preview.trigger( 'settings', api.settings.values );

		preview.bind( 'setting', function( args ) {
			var value;

			args = args.slice();

			if ( value = api( args.shift() ) )
				value.set.apply( value, args );
		});

		preview.bind( 'sync', function( events ) {
			$.each( events, function( event, args ) {
				preview.trigger( event, args );
			});
			preview.send( 'synced' );
		});

	 	preview.bind( 'active', function() {
	 		if ( api.settings.nonce )
	 			preview.send( 'nonce', api.settings.nonce );
	 	});

		preview.send( 'ready' );

		/* Custom Backgrounds */
		bg = $.map(['color', 'image', 'position_x', 'repeat', 'attachment'], function( prop ) {
			return 'background_' + prop;
		});

		api.when.apply( api, bg ).done( function( color, image, position_x, repeat, attachment ) {
			var body = $(document.body),
				head = $('head'),
				style = $('#custom-background-css'),
				update;

			// If custom backgrounds are active and we can't find the
			// default output, bail.
			if ( body.hasClass('custom-background') && ! style.length )
				return;

			update = function() {
				var css = '';

				// The body will support custom backgrounds if either
				// the color or image are set.
				//
				// See get_body_class() in /wp-includes/post-template.php
				body.toggleClass( 'custom-background', !! ( color() || image() ) );

				if ( color() )
					css += 'background-color: ' + color() + ';';

				if ( image() ) {
					css += 'background-image: url("' + image() + '");';
					css += 'background-position: top ' + position_x() + ';';
					css += 'background-repeat: ' + repeat() + ';';
					css += 'background-attachment: ' + attachment() + ';';
				}

				// Refresh the stylesheet by removing and recreating it.
				style.remove();
				style = $('<style type="text/css" id="custom-background-css">body.custom-background { ' + css + ' }</style>').appendTo( head );
			};

			$.each( arguments, function() {
				this.bind( update );
			});
		});
	});

})( wp, jQuery );
>>>>>>> 3.5.1
