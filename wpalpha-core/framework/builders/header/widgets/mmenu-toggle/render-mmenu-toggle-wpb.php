<?php
/**
 * Header Mobile Menu Toggle Shortcode Render
 *
 * @author     FunnyWP
 * @package    Alpha Core Framework
 * @subpackage Core
 * @since      1.0
 */

// Preprocess
$wrapper_attrs = array(
	'class' => 'alpha-hb-v-divider-container ' . $atts['shortcode_class'] . $atts['style_class'],
);

$wrapper_attrs = apply_filters( 'alpha_wpb_element_wrapper_atts', $wrapper_attrs, $atts );

$wrapper_attr_html = '';
foreach ( $wrapper_attrs as $key => $value ) {
	$wrapper_attr_html .= $key . '="' . esc_attr( $value ) . '" ';
}

$atts = array(
	'icon_class' => isset( $atts['icon'] ) && $atts['icon'] ? $atts['icon'] : ALPHA_ICON_PREFIX . '-icon-hamburger',
);

?>
<div <?php echo alpha_escaped( $wrapper_attr_html ); ?>>
	<?php require alpha_core_framework_path( ALPHA_BUILDERS . '/header/widgets/mmenu-toggle/render-mmenu-toggle-elementor.php' ); ?>
</div>
<?php
