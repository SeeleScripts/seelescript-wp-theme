/**
 * SeeleScript Admin — Theme Settings JS
 *
 * Handles:
 *  1. WordPress media uploader for image-upload fields.
 *  2. Social icons repeater (add / remove rows + sync to hidden JSON input).
 *
 * Dependencies: jQuery, wp.media (loaded via wp_enqueue_media on our pages).
 */
( function ( $ ) {
	'use strict';

	// =========================================================================
	// 1. Media Uploader
	// =========================================================================
	// Works for every .seelescript-image-upload wrapper on the page.

	$( document ).on( 'click', '.seelescript-upload-btn', function ( e ) {
		e.preventDefault();

		var $btn     = $( this );
		var $wrap    = $btn.closest( '.seelescript-image-upload' );
		var inputId  = $wrap.data( 'input-id' );
		var $input   = $( '#' + inputId );
		var $preview = $wrap.find( '.seelescript-image-preview' );

		var frame = wp.media( {
			title:    $btn.data( 'title' )      || 'Select Image',
			button:   { text: $btn.data( 'button-text' ) || 'Use this image' },
			multiple: false,
		} );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			var url        = attachment.url;

			$input.val( url );

			$preview.html(
				$( '<img>' ).attr( {
					src:   url,
					alt:   'Preview',
					style: 'max-width:300px;max-height:150px;display:block;',
				} )
			);

			// Show remove button if not already present.
			if ( ! $wrap.find( '.seelescript-remove-btn' ).length ) {
				var $remove = $( '<button>' )
					.attr( { type: 'button' } )
					.addClass( 'button seelescript-remove-btn' )
					.css( 'margin-left', '5px' )
					.text( 'Remove' );
				$btn.after( $remove );
			}
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.seelescript-remove-btn', function () {
		var $wrap   = $( this ).closest( '.seelescript-image-upload' );
		var inputId = $wrap.data( 'input-id' );

		$( '#' + inputId ).val( '' );
		$wrap.find( '.seelescript-image-preview' ).empty();
		$( this ).remove();
	} );

	// =========================================================================
	// 2. Social Icons Repeater
	// =========================================================================

	var $list     = $( '#social-icons-list' );
	var $jsonInput = $( '#seelescript-social-data' );

	if ( ! $list.length ) {
		return; // Not on the footer settings page.
	}

	/**
	 * Read all rows and write the JSON representation to the hidden input.
	 */
	function syncSocialData() {
		var data = [];

		$list.find( '.social-icon-row' ).each( function () {
			var icon = $( this ).find( '.social-icon-class' ).val().trim();
			var url  = $( this ).find( '.social-icon-url' ).val().trim();

			if ( icon || url ) {
				data.push( { icon: icon, url: url } );
			}
		} );

		$jsonInput.val( JSON.stringify( data ) );
	}

	// Sync on any input change inside the list.
	$( document ).on( 'input change', '#social-icons-list input', syncSocialData );

	// Add a new row.
	$( '#add-social-row' ).on( 'click', function () {
		var template = document.getElementById( 'social-icon-row-template' );

		if ( ! template ) {
			return;
		}

		var $newRow = $( template.content.cloneNode( true ) );
		$list.append( $newRow );
	} );

	// Remove a row.
	$( document ).on( 'click', '.remove-social-row', function () {
		$( this ).closest( '.social-icon-row' ).remove();
		syncSocialData();
	} );

	// Initial sync on page load (in case of pre-populated rows).
	syncSocialData();

} )( jQuery );
