<?php
defined( 'ABSPATH' ) || die;

/**
 * Alpha Heading Countdown Render
 *
 * @author     FunnyWP
 * @package    WP Alpha Core FrameWork
 * @subpackage Core
 * @since      1.0
 */

extract( // @codingStandardsIgnoreLine
	shortcode_atts(
		array(
			'date'        => '',
			'type'        => 'block',
			'label'       => 'Offer Ends In:',
			'label_type'  => '',
			'label_pos'   => '',
			'date_format' => array( 'D', 'H', 'M', 'S' ),
			'timezone'    => '',
			'hide_split'  => false,
		),
		$atts
	)
);

$html = '';

if ( $date ) {
	$until = strtotime( $date );
	$now   = strtotime( 'now' );
	$until = $until - $now;

	$class = 'countdown';
	if ( $label_pos ) {
		$class .= ' outer-period';
	}
	if ( $timezone ) {
		$class .= ' user-tz';
	}
	if ( true == $hide_split ) {
		$class .= ' no-split';
	}

	$format = '';
	if ( is_array( $date_format ) ) {
		foreach ( $date_format as $f ) {
			$format .= $f;
		}
	} else {
		$format = str_replace( ',', '', $date_format );
	}

	$html .= '<div class="countdown-container ' . ( $type ) . '-type">';

	if ( 'inline' == $type ) {
		$html .= '<label class="countdown-label">' . sanitize_text_field( $label ) . '</label>';
	}

	$html .= '<div class="' . esc_attr( $class ) . '" data-until="' . esc_attr( $until ) . '" data-relative="true" ' . ( 'inline' == $type ? 'data-compact="true" ' : ' ' ) . ( 'short' == $label_type ? ' data-labels-short="true"' : '' ) . ' data-format="' . esc_attr( $format ) . '" data-time-now="' . esc_attr( str_replace( '-', '/', current_time( 'mysql' ) ) ) . '" >';

	if ( 'block' == $type ) {
		$html .= '<span class="countdown-row countdown-show' . ( is_array( $date_format ) ? count( $date_format ) : 0 ) . '">';

		$formats = 'short' == $label_type ? array(
			'Y' => esc_html__( 'Years', 'alpha-core' ),
			'O' => esc_html__( 'Months', 'alpha-core' ),
			'W' => esc_html__( 'Weeks', 'alpha-core' ),
			'D' => esc_html__( 'Days', 'alpha-core' ),
			'H' => esc_html__( 'Hrs', 'alpha-core' ),
			'M' => esc_html__( 'Mins', 'alpha-core' ),
			'S' => esc_html__( 'Secs', 'alpha-core' ),
		) : array(
			'Y' => esc_html__( 'Years', 'alpha-core' ),
			'O' => esc_html__( 'Months', 'alpha-core' ),
			'W' => esc_html__( 'Weeks', 'alpha-core' ),
			'D' => esc_html__( 'Days', 'alpha-core' ),
			'H' => esc_html__( 'Hours', 'alpha-core' ),
			'M' => esc_html__( 'Minutes', 'alpha-core' ),
			'S' => esc_html__( 'Seconds', 'alpha-core' ),
		);

		if ( is_array( $date_format ) ) {
			foreach ( $date_format as $item ) {
				$html .= '<span class="countdown-section"><span class="countdown-amount">00</span><span class="countdown-period">' . $formats[ $item ] . '</span></span>';
			}
		}

		$html .= '</span>';

	} else {
		$html .= '00 : 00 : 00';
	}

	$html .= '</div>';

	$html .= '</div>';
}

echo alpha_escaped( $html );
