<<<<<<< HEAD
if(typeof wp==="undefined"){var wp={}}(function(a,b){var c;if(typeof _wpPluploadSettings==="undefined"){return}c=function(e){var d=this,g={container:"container",browser:"browse_button",dropzone:"drop_element"},f;this.supports={upload:c.browser.supported};this.supported=this.supports.upload;if(!this.supported){return}this.plupload=b.extend(true,{multipart_params:{}},c.defaults);this.container=document.body;b.extend(true,this,e);for(f in this){if(b.isFunction(this[f])){this[f]=b.proxy(this[f],this)}}for(f in g){if(!this[f]){continue}this[f]=b(this[f]).first();if(!this[f].length){delete this[f];continue}if(!this[f].prop("id")){this[f].prop("id","__wp-uploader-id-"+c.uuid++)}this.plupload[g[f]]=this[f].prop("id")}if(!(this.browser&&this.browser.length)&&!(this.dropzone&&this.dropzone.length)){return}this.uploader=new plupload.Uploader(this.plupload);delete this.plupload;this.param(this.params||{});delete this.params;this.uploader.init();this.supports.dragdrop=this.uploader.features.dragdrop&&!c.browser.mobile;(function(j,h){var i=50,k;if(!j){return}j.toggleClass("supports-drag-drop",!!h);if(!h){return j.unbind(".wp-uploader")}j.bind("dragover.wp-uploader",function(){if(k){return}j.addClass("drag-over");k=true});j.bind("dragleave.wp-uploader, drop.wp-uploader",function(){k=false;j.removeClass("drag-over")})}(this.dropzone,this.supports.dragdrop));if(this.browser){this.browser.on("mouseenter",this.refresh)}else{this.uploader.disableBrowse(true);b("#"+this.uploader.id+"_html5_container").hide()}this.uploader.bind("UploadProgress",this.progress);this.uploader.bind("FileUploaded",function(h,j,i){try{i=JSON.parse(i.response)}catch(k){return d.error(pluploadL10n.default_error,k)}if(!i||!i.type||!i.data){return d.error(pluploadL10n.default_error)}if("error"===i.type){return d.error(i.data.message,i.data)}if("success"===i.type){return d.success(i.data)}});this.uploader.bind("Error",function(h,i){var k=pluploadL10n.default_error,j;for(j in c.errorMap){if(i.code===plupload[j]){k=c.errorMap[j];break}}d.error(k,i);h.refresh()});this.uploader.bind("FilesAdded",function(h,i){b.each(i,function(){d.added(this)});h.refresh();h.start()});this.init()};b.extend(c,_wpPluploadSettings);c.uuid=0;c.errorMap={FAILED:pluploadL10n.upload_failed,FILE_EXTENSION_ERROR:pluploadL10n.invalid_filetype,IMAGE_FORMAT_ERROR:pluploadL10n.not_an_image,IMAGE_MEMORY_ERROR:pluploadL10n.image_memory_exceeded,IMAGE_DIMENSIONS_ERROR:pluploadL10n.image_dimensions_exceeded,GENERIC_ERROR:pluploadL10n.upload_failed,IO_ERROR:pluploadL10n.io_error,HTTP_ERROR:pluploadL10n.http_error,SECURITY_ERROR:pluploadL10n.security_error};b.extend(c.prototype,{param:function(d,e){if(arguments.length===1&&typeof d==="string"){return this.uploader.settings.multipart_params[d]}if(arguments.length>1){this.uploader.settings.multipart_params[d]=e}else{b.extend(this.uploader.settings.multipart_params,d)}},init:function(){},error:function(){},success:function(){},added:function(){},progress:function(){},complete:function(){},refresh:function(){this.uploader.refresh()}});a.Uploader=c})(wp,jQuery);
=======
window.wp = window.wp || {};

(function( exports, $ ) {
	var Uploader;

	if ( typeof _wpPluploadSettings === 'undefined' )
		return;

	/*
	 * An object that helps create a WordPress uploader using plupload.
	 *
	 * @param options - object - The options passed to the new plupload instance.
	 *    Accepts the following parameters:
	 *    - container - The id of uploader container.
	 *    - browser   - The id of button to trigger the file select.
	 *    - dropzone  - The id of file drop target.
	 *    - plupload  - An object of parameters to pass to the plupload instance.
	 *    - params    - An object of parameters to pass to $_POST when uploading the file.
	 *                  Extends this.plupload.multipart_params under the hood.
	 *
	 * @param attributes - object - Attributes and methods for this specific instance.
	 */
	Uploader = function( options ) {
		var self = this,
			elements = {
				container: 'container',
				browser:   'browse_button',
				dropzone:  'drop_element'
			},
			key, error;

		this.supports = {
			upload: Uploader.browser.supported
		};

		this.supported = this.supports.upload;

		if ( ! this.supported )
			return;

		// Use deep extend to ensure that multipart_params and other objects are cloned.
		this.plupload = $.extend( true, { multipart_params: {} }, Uploader.defaults );
		this.container = document.body; // Set default container.

		// Extend the instance with options
		//
		// Use deep extend to allow options.plupload to override individual
		// default plupload keys.
		$.extend( true, this, options );

		// Proxy all methods so this always refers to the current instance.
		for ( key in this ) {
			if ( $.isFunction( this[ key ] ) )
				this[ key ] = $.proxy( this[ key ], this );
		}

		// Ensure all elements are jQuery elements and have id attributes
		// Then set the proper plupload arguments to the ids.
		for ( key in elements ) {
			if ( ! this[ key ] )
				continue;

			this[ key ] = $( this[ key ] ).first();

			if ( ! this[ key ].length ) {
				delete this[ key ];
				continue;
			}

			if ( ! this[ key ].prop('id') )
				this[ key ].prop( 'id', '__wp-uploader-id-' + Uploader.uuid++ );
			this.plupload[ elements[ key ] ] = this[ key ].prop('id');
		}

		// If the uploader has neither a browse button nor a dropzone, bail.
		if ( ! ( this.browser && this.browser.length ) && ! ( this.dropzone && this.dropzone.length ) )
			return;

		this.uploader = new plupload.Uploader( this.plupload );
		delete this.plupload;

		// Set default params and remove this.params alias.
		this.param( this.params || {} );
		delete this.params;

		error = function( message, data, file ) {
			if ( file.attachment )
				file.attachment.destroy();

			Uploader.errors.unshift({
				message: message || pluploadL10n.default_error,
				data:    data,
				file:    file
			});

			self.error( message, data, file );
		};

		this.uploader.init();

		this.supports.dragdrop = this.uploader.features.dragdrop && ! Uploader.browser.mobile;

		// Generate drag/drop helper classes.
		(function( dropzone, supported ) {
			var timer, active;

			if ( ! dropzone )
				return;

			dropzone.toggleClass( 'supports-drag-drop', !! supported );

			if ( ! supported )
				return dropzone.unbind('.wp-uploader');

			// 'dragenter' doesn't fire correctly,
			// simulate it with a limited 'dragover'
			dropzone.bind( 'dragover.wp-uploader', function(){
				if ( timer )
					clearTimeout( timer );

				if ( active )
					return;

				dropzone.trigger('dropzone:enter').addClass('drag-over');
				active = true;
			});

			dropzone.bind('dragleave.wp-uploader, drop.wp-uploader', function(){
				// Using an instant timer prevents the drag-over class from
				// being quickly removed and re-added when elements inside the
				// dropzone are repositioned.
				//
				// See http://core.trac.wordpress.org/ticket/21705
				timer = setTimeout( function() {
					active = false;
					dropzone.trigger('dropzone:leave').removeClass('drag-over');
				}, 0 );
			});
		}( this.dropzone, this.supports.dragdrop ));

		if ( this.browser ) {
			this.browser.on( 'mouseenter', this.refresh );
		} else {
			this.uploader.disableBrowse( true );
			// If HTML5 mode, hide the auto-created file container.
			$('#' + this.uploader.id + '_html5_container').hide();
		}

		this.uploader.bind( 'FilesAdded', function( up, files ) {
			_.each( files, function( file ) {
				var attributes, image;

				// Ignore failed uploads.
				if ( plupload.FAILED === file.status )
					return;

				// Generate attributes for a new `Attachment` model.
				attributes = _.extend({
					file:      file,
					uploading: true,
					date:      new Date(),
					filename:  file.name,
					menuOrder: 0,
					uploadedTo: wp.media.model.settings.post.id
				}, _.pick( file, 'loaded', 'size', 'percent' ) );

				// Handle early mime type scanning for images.
				image = /(?:jpe?g|png|gif)$/i.exec( file.name );

				// Did we find an image?
				if ( image ) {
					attributes.type = 'image';

					// `jpeg`, `png` and `gif` are valid subtypes.
					// `jpg` is not, so map it to `jpeg`.
					attributes.subtype = ( 'jpg' === image[0] ) ? 'jpeg' : image[0];
				}

				// Create the `Attachment`.
				file.attachment = wp.media.model.Attachment.create( attributes );

				Uploader.queue.add( file.attachment );

				self.added( file.attachment );
			});

			up.refresh();
			up.start();
		});

		this.uploader.bind( 'UploadProgress', function( up, file ) {
			file.attachment.set( _.pick( file, 'loaded', 'percent' ) );
			self.progress( file.attachment );
		});

		this.uploader.bind( 'FileUploaded', function( up, file, response ) {
			var complete;

			try {
				response = JSON.parse( response.response );
			} catch ( e ) {
				return error( pluploadL10n.default_error, e, file );
			}

			if ( ! _.isObject( response ) || _.isUndefined( response.success ) )
				return error( pluploadL10n.default_error, null, file );
			else if ( ! response.success )
				return error( response.data && response.data.message, response.data, file );

			_.each(['file','loaded','size','percent'], function( key ) {
				file.attachment.unset( key );
			});

			file.attachment.set( _.extend( response.data, { uploading: false }) );
			wp.media.model.Attachment.get( response.data.id, file.attachment );

			complete = Uploader.queue.all( function( attachment ) {
				return ! attachment.get('uploading');
			});

			if ( complete )
				Uploader.queue.reset();

			self.success( file.attachment );
		});

		this.uploader.bind( 'Error', function( up, pluploadError ) {
			var message = pluploadL10n.default_error,
				key;

			// Check for plupload errors.
			for ( key in Uploader.errorMap ) {
				if ( pluploadError.code === plupload[ key ] ) {
					message = Uploader.errorMap[ key ];
					if ( _.isFunction( message ) )
						message = message( pluploadError.file, pluploadError );
					break;
				}
			}

			error( message, pluploadError, pluploadError.file );
			up.refresh();
		});

		this.init();
	};

	// Adds the 'defaults' and 'browser' properties.
	$.extend( Uploader, _wpPluploadSettings );

	Uploader.uuid = 0;

	Uploader.errorMap = {
		'FAILED':                 pluploadL10n.upload_failed,
		'FILE_EXTENSION_ERROR':   pluploadL10n.invalid_filetype,
		'IMAGE_FORMAT_ERROR':     pluploadL10n.not_an_image,
		'IMAGE_MEMORY_ERROR':     pluploadL10n.image_memory_exceeded,
		'IMAGE_DIMENSIONS_ERROR': pluploadL10n.image_dimensions_exceeded,
		'GENERIC_ERROR':          pluploadL10n.upload_failed,
		'IO_ERROR':               pluploadL10n.io_error,
		'HTTP_ERROR':             pluploadL10n.http_error,
		'SECURITY_ERROR':         pluploadL10n.security_error,

		'FILE_SIZE_ERROR': function( file ) {
			return pluploadL10n.file_exceeds_size_limit.replace('%s', file.name);
		}
	};

	$.extend( Uploader.prototype, {
		/**
		 * Acts as a shortcut to extending the uploader's multipart_params object.
		 *
		 * param( key )
		 *    Returns the value of the key.
		 *
		 * param( key, value )
		 *    Sets the value of a key.
		 *
		 * param( map )
		 *    Sets values for a map of data.
		 */
		param: function( key, value ) {
			if ( arguments.length === 1 && typeof key === 'string' )
				return this.uploader.settings.multipart_params[ key ];

			if ( arguments.length > 1 ) {
				this.uploader.settings.multipart_params[ key ] = value;
			} else {
				$.extend( this.uploader.settings.multipart_params, key );
			}
		},

		init:     function() {},
		error:    function() {},
		success:  function() {},
		added:    function() {},
		progress: function() {},
		complete: function() {},
		refresh:  function() {
			var node, attached, container, id;

			if ( this.browser ) {
				node = this.browser[0];

				// Check if the browser node is in the DOM.
				while ( node ) {
					if ( node === document.body ) {
						attached = true;
						break;
					}
					node = node.parentNode;
				}

				// If the browser node is not attached to the DOM, use a
				// temporary container to house it, as the browser button
				// shims require the button to exist in the DOM at all times.
				if ( ! attached ) {
					id = 'wp-uploader-browser-' + this.uploader.id;

					container = $( '#' + id );
					if ( ! container.length ) {
						container = $('<div class="wp-uploader-browser" />').css({
							position: 'fixed',
							top: '-1000px',
							left: '-1000px',
							height: 0,
							width: 0
						}).attr( 'id', 'wp-uploader-browser-' + this.uploader.id ).appendTo('body');
					}

					container.append( this.browser );
				}
			}

			this.uploader.refresh();
		}
	});

	Uploader.queue = new wp.media.model.Attachments( [], { query: false });
	Uploader.errors = new Backbone.Collection();

	exports.Uploader = Uploader;
})( wp, jQuery );
>>>>>>> 3.5.1
