<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $css
 * @var $el_id
 * @var $equal_height
 * @var $content_placement
 * @var $content - shortcode content
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Row_Inner $this
 */
$el_class        = $equal_height = $content_placement = $css = $el_id = '';
$disable_element = '';
$output          = $after_output = '';
$atts            = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$el_class    = $this->getExtraClass( $el_class );
$css_classes = array(
	'vc_row',
	'wpb_row',
	'vc-custom-row',
	// deprecated
	'vc_inner',
	'vc_row-fluid',
	$el_class,
	vc_shortcode_custom_css_class( $css ),
);
if ( 'yes' === $disable_element ) {
	if ( vc_is_page_editable() ) {
		$css_classes[] = 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
	} else {
		return '';
	}
}

if ( vc_shortcode_custom_css_has_property(
	$css,
	array(
		'border',
		'background',
	)
) ) {
	$css_classes[] = 'vc_row-has-fill';
}

if ( ! empty( $atts['gap'] ) ) {
	$css_classes[] = 'vc_column-gap-' . $atts['gap'];
}

if ( ! empty( $equal_height ) ) {
	$flex_row      = true;
	$css_classes[] = 'vc_row-o-equal-height';
}

if ( ! empty( $atts['rtl_reverse'] ) ) {
	$css_classes[] = 'vc_rtl-columns-reverse';
}

if ( ! empty( $content_placement ) ) {
	$flex_row      = true;
	$css_classes[] = 'vc_row-o-content-' . $content_placement;
}

if ( ! empty( $flex_row ) ) {
	$css_classes[] = 'vc_row-flex';
}

$wrapper_attributes = array();
// build attributes for wrapper
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}

// Animation
if ( ! empty( $atts['animation_type'] ) && 'none' != $atts['animation_type'] && 'default' != $atts['animation_type'] ) {
	if ( ! vc_is_inline() ) {
		$css_classes[] = 'appear-animate';
	}

	$animation_settings   = array(
		'_animation'          => $atts['animation_type'],
		'_animation_delay'    => ! empty( $atts['animation_delay'] ) ? $atts['animation_delay'] : '0',
		'_animation_duration' => ! empty( $atts['animation_duration'] ) ? $atts['animation_duration'] : '1000',
	);
	$wrapper_attributes[] = 'data-settings="' . esc_attr( json_encode( $animation_settings ) ) . '"';
}

// lazy load background image
if ( function_exists( 'alpha_get_option' ) && alpha_get_option( 'lazyload' ) && ! vc_is_inline() ) {
	preg_match( '/\.vc_custom_[^}]*(background-image:[^(]*([^)]*)|background:\s#[A-Fa-f0-9]{3,6}\s*url\(([^)]*))/', $css, $matches );
	if ( ! empty( $matches[2] ) || ! empty( $matches[3] ) ) {
		$image_url            = ! empty( $matches[2] ) ? $matches[2] : $matches[3];
		$wrapper_attributes[] = 'data-lazy="' . esc_url( trim( str_replace( array( '(', ')' ), '', $image_url ) ) ) . '"';
	}
}

$css_class            = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( array_unique( $css_classes ) ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

$output .= '<div ' . implode( ' ', $wrapper_attributes ) . '>';
$output .= wpb_js_remove_wpautop( $content );
$output .= '</div>';
$output .= $after_output;

return $output;
