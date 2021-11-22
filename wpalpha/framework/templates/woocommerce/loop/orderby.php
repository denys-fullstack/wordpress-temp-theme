<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     3.6.0
 */

defined( 'ABSPATH' ) || die;

global $alpha_layout;

if ( 'archive_product' != alpha_get_page_layout() && ! apply_filters( 'alpha_is_vendor_store', false ) ) {
	return;
}

$ts = ! empty( $alpha_layout['top_sidebar'] ) && 'hide' != $alpha_layout['top_sidebar'] && is_active_sidebar( $alpha_layout['top_sidebar'] );
$ls = ! empty( $alpha_layout['left_sidebar'] ) && 'hide' != $alpha_layout['left_sidebar'] && is_active_sidebar( $alpha_layout['left_sidebar'] );
$rs = ! empty( $alpha_layout['right_sidebar'] ) && 'hide' != $alpha_layout['right_sidebar'] && is_active_sidebar( $alpha_layout['right_sidebar'] );

if ( $ts ) {
	echo '<div class="toolbox-horizontal">';
	alpha_get_template_part( 'sidebar', null, array( 'position' => 'top' ) );
}
$link = get_permalink( wc_get_page_id( 'shop' ) );
if ( is_product_category() ) {
	global $wp_query;
	$link = get_term_link( $wp_query->get_queried_object() );
}
?>
<div class="sticky-toolbox sticky-content fix-top toolbox toolbox-top">
	<div class="toolbox-left">
		<?php
		$toggle_class = $ts ? 'top' : ( $ls ? ( is_rtl() ? 'right' : 'left' ) : '' );
		if ( $toggle_class ) :
			$toggle_class .= '-sidebar-toggle';
			if ( $ts || $ls && ( empty( $alpha_layout['left_sidebar_type'] ) || 'offcanvas' != $alpha_layout['left_sidebar_type'] ) ) {
				$toggle_class .= ' d-lg-none';
			}
			?>
			<a href="#" class="toolbox-item toolbox-toggle <?php echo esc_attr( $toggle_class ); ?> btn btn-sm btn-outline btn-primary btn-icon-left"><i class="<?php echo ALPHA_ICON_PREFIX; ?>-icon-category"></i><span class="d-none d-sm-block"><?php esc_html_e( 'Filters', 'alpha' ); ?></span></a>
		<?php endif; ?>

		<form class="woocommerce-ordering toolbox-item toolbox-sort select-box" method="get">
			<?php if ( ! $ts ) : ?>
			<label><?php esc_html_e( 'Sort By :', 'alpha' ); ?></label>
			<?php endif; ?>
			<select name="orderby" class="orderby form-control" aria-label="<?php esc_attr_e( 'Shop order', 'woocommerce' ); ?>">
				<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="hidden" name="paged" value="1" />
			<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
		</form>
	</div>

	<div class="toolbox-right">
		<?php
		alpha_wc_count_per_page();
		alpha_wc_shop_show_type();
		?>
	</div>
</div>
<?php
if ( $ts ) {
	echo '</div>';
}
?>
<?php if ( $ts ) : ?>
	<div class="select-items">
		<a href="<?php echo esc_url( $link ); ?>" class="filter-clean text-primary"><?php esc_html_e( 'Clean All', 'alpha' ); ?></a>
	</div>
	<?php
endif;

// If shop page's loadmore type is button, do not show pagination.
if ( ! empty( $alpha_layout['loadmore_type'] ) && 'page' != $alpha_layout['loadmore_type'] ) {
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );
}
