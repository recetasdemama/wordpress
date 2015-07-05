jQuery( document ).ready( function ( $ ) {
	
	watermarkFileUpload = {
		frame: function () {
			if ( this._frameWatermark )
				return this._frameWatermark;

			this._frameWatermark = wp.media( {
				title: upload_manager_args.title,
				frame: upload_manager_args.frame,
				button: upload_manager_args.button,
				multiple: upload_manager_args.multiple,
				library: {
					type: 'image'
				}
			} );

			this._frameWatermark.on( 'open', this.updateFrame ).state( 'library' ).on( 'select', this.select );
			return this._frameWatermark;
		},
		select: function () {
			var attachment = this.frame.state().get( 'selection' ).first();

			if ( jQuery.inArray( attachment.attributes.mime, ['image/gif', 'image/jpg', 'image/jpeg', 'image/png'] ) !== -1 ) {
				
				$( '#upload_image' ).val( attachment.attributes.id );

				if ( $( 'div#previewImg_imageDiv img#previewImg_image' ).attr( 'src' ) !== '' )	{
					$( 'div#previewImg_imageDiv img#previewImg_image' ).replaceWith( '<img id="previewImg_image" src="' + attachment.attributes.url + '" alt="" width="300" />' );
				} else {
					$( 'div#previewImg_imageDiv img#previewImg_image' ).attr( 'src', attachment.attributes.url );
				}

				$( '#turn_off_image_button' ).removeAttr( 'disabled' );
				$( 'div#previewImg_imageDiv img#previewImg_image' ).show();

				var img = new Image();
				img.src = attachment.attributes.url;

				img.onload = function () {
					$( 'p#previewImageInfo' ).html( upload_manager_args.originalSize + ': ' + this.width + ' px / ' + this.height + ' px' );
				}
				
			} else {
				
				$( '#turn_off_image_button' ).attr( 'disabled', 'true' );
				$( '#upload_image' ).val( 0 );
				$( 'div#previewImg_imageDiv img#previewImg_image' ).attr( 'src', '' ).hide();
				$( 'p#previewImageInfo' ).html( '<strong>' + upload_manager_args.notAllowedImg + '</strong>' );
				
			}
		},
		init: function () {
			$( '#wpbody' ).on( 'click', 'input#upload_image_button', function ( e ) {
				e.preventDefault();
				watermarkFileUpload.frame().open();
			} );
		}
	};

	watermarkFileUpload.init();

	$( document ).on( 'click', '#turn_off_image_button', function ( event ) {
		$( this ).attr( 'disabled', 'true' );
		$( '#upload_image' ).val( 0 );
		$( 'div#previewImg_imageDiv img#previewImg_image' ).attr( 'src', '' ).hide();
		$( 'p#previewImageInfo' ).html( upload_manager_args.noSelectedImg );
	} );

} );