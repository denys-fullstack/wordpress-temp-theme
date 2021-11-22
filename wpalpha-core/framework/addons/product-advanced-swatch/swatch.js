/**
 * Alpha Plugin - Product Swatch
 * 
 * @instance single
 * @author     FunnyWP
 * @package    WP Alpha FrameWork
 * @subpackage Theme
 * @since      1.0
 */
'use strict';
window.theme || ( window.theme = {} );

( function ( $ ) {

	var Swatch = {
		/**
		 * Register events for swatch
		 * 
		 * @since 1.0
		 */
		init: function () {
			this.skipReset = false;

			theme.$body
				// Archive product
				.on( 'click', 'li>.product .product-variations>button', function ( e ) {
					var $btn = $( e.currentTarget );
					if ( !$btn.closest( '.product' ).hasClass( 'product-single' ) ) {
						Swatch.previewArchive( $btn );
					}
				} )

				// Single product
				.on( 'click', '.product-single .product-variations > button', function ( e ) {
					var $btn = $( e.currentTarget );
					Swatch.skipReset = true;
					theme.requestTimeout( function () {
						Swatch.skipReset = false;
						Swatch.previewSingle( $btn );
					}, 50 );
				} );

			var wc_reset_variation_attr = $.fn.wc_reset_variation_attr;
			$.fn.wc_reset_variation_attr = function ( attr ) {
				Swatch.skipReset || wc_reset_variation_attr.call( this, attr );
			};
			this.initVariableProduct();
		},

		/**
		 * Preview swatch image for archive products
		 * 
		 * @since 1.0
		 */
		previewArchive: function ( $btn ) {
			if ( $btn.hasClass( 'disabled' ) ) {
				return;
			}
			var isActive = $btn.hasClass( 'active' );

			$btn.closest( '.product-variations' ).children( 'button' ).removeClass( 'active' );

			isActive || $btn.addClass( 'active' );

			if ( $btn.data( 'image' ) ) {
				var $img = $btn.closest( '.product' ).find( '.product-media img:first-child' );
				if ( isActive ) {
					$img.attr( 'src', $img.data( 'origin-src' ) )
						.attr( 'srcset', $img.data( 'origin-srcset' ) );
				} else {
					var match = $btn.data( 'image' ).match( /src="([^"]*)"/ );
					if ( match && match.length == 2 ) {
						$img.data( 'origin-src' ) || $img.data( 'origin-src', $img.attr( 'src' ) );
						$img.attr( 'src', match[ 1 ] );
					}
					match = $btn.data( 'image' ).match( /srcset="([^"]*)"/ );
					if ( match && match.length == 2 ) {
						$img.data( 'origin-srcset' ) || $img.data( 'origin-srcset', $img.attr( 'srcset' ) );
						$img.attr( 'srcset', match[ 1 ] );
					}
				}
			}
		},

		/**
		 * Preview swatch image for single product
		 * 
		 * @since 1.0
		 */
		previewSingle: function ( $btn ) {
			var $form = $btn.closest( '.variations_form' ),
				variationImage = $form.attr( 'current-image' );

			// If no variation is matched
			if ( !variationImage ) {
				var $product = $btn.closest( '.product' );

				// if deactive image, find active image button
				if ( !$btn.hasClass( 'active' ) ) {
					$btn = $form.find( '.image.active' ).not( $btn ).first();
				}

				if ( $btn.length ) {
					// activate swatch image
					var swatchImageHtml = $btn.attr( 'data-image' );
					if ( swatchImageHtml ) {
						var $product_img = $product.find( '.wp-post-image' ),
							$swatchImage = $( swatchImageHtml );

						$product_img.wc_set_variation_attr( 'src', $swatchImage.attr( 'src' ) );
						$product_img.wc_set_variation_attr( 'height', $swatchImage.attr( 'height' ) );
						$product_img.wc_set_variation_attr( 'width', $swatchImage.attr( 'width' ) );
						$product_img.wc_set_variation_attr( 'srcset', $swatchImage.attr( 'srcset' ) );
						$product_img.wc_set_variation_attr( 'sizes', $swatchImage.attr( 'sizes' ) );
						$product_img.wc_set_variation_attr( 'title', $swatchImage.attr( 'title' ) );
						$product_img.wc_set_variation_attr( 'data-caption', $swatchImage.attr( 'data-caption' ) );
						$product_img.wc_set_variation_attr( 'alt', $swatchImage.attr( 'alt' ) );
						$product_img.wc_set_variation_attr( 'data-src', $swatchImage.attr( 'data-src' ) );
						$product_img.wc_set_variation_attr( 'data-large_image', $swatchImage.attr( 'data-large_image' ) );
						$product_img.wc_set_variation_attr( 'data-large_image_width', $swatchImage.attr( 'data-large_image_width' ) );
						$product_img.wc_set_variation_attr( 'data-large_image_height', $swatchImage.attr( 'data-large_image_height' ) );
					}
				} else {
					// reset
					$form.wc_variations_image_reset();
				}

				// refresh gallery
				var gallery = $product.find( '.woocommerce-product-gallery' ).data( 'alpha_product_gallery' );
				gallery & gallery.changePostImage();
			}
		},

		/**
		 * Init variable product.
		 * 
		 * @since 1.0
		 */
		initVariableProduct: function () {
			function onClickListVariation( e ) {
				var $btn = $( e.currentTarget );
				if ( $btn.hasClass( 'disabled' ) ) {
					return;
				}
				if ( $btn.hasClass( 'active' ) ) {
					$btn.removeClass( 'active' )
						.parent().next().val( '' ).change();
				} else {
					$btn.addClass( 'active' ).siblings().removeClass( 'active' );
					$btn.parent().next().val( $btn.attr( 'name' ) ).change();
				}
			}

			function onClickResetVariation( e ) {
				$( e.currentTarget ).closest( '.variations_form' ).find( '.active' ).removeClass( 'active' );
			}

			function onToggleResetVariation() {
				var $reset = $( theme.byClass( 'reset_variations', this ) );
				$reset.css( 'visibility' ) == 'hidden' ? $reset.hide() : $reset.show();
			}

			function onFoundVariation( e, variation ) {

				var $product = $( e.currentTarget ).closest( '.product' );
				// Display product of matched variation.
				var gallery = $product.find( '.woocommerce-product-gallery' ).data( 'alpha_product_gallery' );
				if ( gallery ) {
					gallery.changePostImage( variation );
				}

				// Display sale countdown of matched variation.
				var $counter = $product.find( '.countdown-variations' );
				if ( $counter.length ) {
					if ( variation && variation.is_purchasable && variation.alpha_date_on_sale_to ) {
						var $countdown = $counter.find( '.countdown' );
						if ( $countdown.data( 'until' ) != variation.alpha_date_on_sale_to ) {
							theme.countdown( $countdown, { until: new Date( variation.alpha_date_on_sale_to ) } );
							$countdown.data( 'until', variation.alpha_date_on_sale_to );
						}
						$counter.slideDown();
					} else {
						$counter.slideUp();
					}
				}
			}

			function onResetVariation( e ) {
				var $product = $( e.currentTarget ).closest( '.product' );
				var $gallery = $product.find( '.woocommerce-product-gallery' );

				if ( $gallery.length ) {
					var gallery = $gallery.data( 'alpha_product_gallery' );
					if ( gallery ) {
						gallery.changePostImage( 'reset' );
					}
				}

				$product.find( '.countdown-variations' ).slideUp();
			}

			function onUpdateVariation() {
				var $form = $( this );
				$form.find( '.product-variations>button' ).addClass( 'disabled' );

				// Loop through selects and disable/enable options based on selections.
				$form.find( 'select' ).each( function () {
					var $this = $( this );
					var $buttons = $this.closest( '.variations > *' ).find( '.product-variations' );
					$this.children( '.enabled' ).each( function () {
						$buttons.children( '[name="' + this.getAttribute( 'value' ) + '"]' ).removeClass( 'disabled' );
					} );
					$this.children( ':selected' ).each( function () {
						$buttons.children( '[name="' + this.getAttribute( 'value' ) + '"]' ).addClass( 'active' );
					} );
				} );
			}

			// Variation
			theme.$body.on( 'click', '.variations .product-variations button', onClickListVariation )
				.on( 'click', '.reset_variations', onClickResetVariation )
				.on( 'check_variations', '.variations_form', onToggleResetVariation )
				.on( 'found_variation', '.variations_form', onFoundVariation )
				.on( 'reset_image', '.variations_form', onResetVariation )
				.on( 'update_variation_values', '.variations_form', onUpdateVariation )
		}
	};

	theme.Swatch = Swatch;

	theme.$window.on( 'alpha_complete', function () {
		theme.Swatch.init();
	} )
} )( jQuery );