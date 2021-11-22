<?php
/**
 * Alpha Layout Builder
 *
 * @author     FunnyWP
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Alpha_Layout_Builder' ) ) {
	class Alpha_Layout_Builder extends Alpha_Base {

		/**
		 * Constructor
		 *
		 * @since 1.0
		 * @access public
		 */
		public function __construct() {

			// Setup layout
			add_action( 'wp', array( $this, 'setup_layout' ), 5 );

			// Get layout options from theme option.
			add_filter( 'alpha_layout_theme_options_map', array( $this, 'get_theme_options_map' ), 10, 2 );

			// Print layout css vars
			add_action( 'wp_enqueue_scripts', array( $this, 'print_css_vars' ), 99 );

			// Print partial blocks
			add_action( 'alpha_before_content', array( $this, 'print_part_block' ) );
			add_action( 'alpha_before_inner_content', array( $this, 'print_part_block' ) );
			add_action( 'alpha_after_inner_content', array( $this, 'print_part_block' ) );
			add_action( 'alpha_after_content', array( $this, 'print_part_block' ) );

			// Add Layout Toolbar Menu
			if ( current_user_can( 'manage_options' ) ) {
				add_action( 'alpha_add_wp_toolbar_menu', array( $this, 'add_layout_toolbar_menu' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			}
		}

		/**
		 * Get Layout Parts for Layout Builder Menu
		 *
		 * @since 1.0
		 */
		public function get_layout_parts() {
			global $alpha_layout;

			$panel_map = array();
			if ( ! empty( $alpha_layout['alpha_panel_map'] ) ) {
				$panel_map = $alpha_layout['alpha_panel_map'];
			}

			$layout_parts = array();

			// Layout Parts
			$slugs = apply_filters(
				'alpha_layout_builder_display_parts',
				array(
					'wrap'                => array(
						'name'        => esc_html__( 'Wrap', 'alpha' ),
						'parent'      => 'general',
						'layout_part' => esc_html__( 'General', 'alpha' ),
					),
					'popup'               => array(
						'name'        => esc_html__( 'Popup', 'alpha' ),
						'parent'      => 'general',
						'layout_part' => esc_html__( 'General', 'alpha' ),
					),
					'top_bar'             => array(
						'name'        => esc_html__( 'Top Notice Block', 'alpha' ),
						'parent'      => 'general',
						'layout_part' => esc_html__( 'General', 'alpha' ),
					),
					'header'              => array(
						'name'   => esc_html__( 'Header', 'alpha' ),
						'parent' => 'header',
					),
					'ptb'                 => array(
						'name'        => esc_html__( 'Page Title Bar', 'alpha' ),
						'parent'      => 'ptb',
						'layout_part' => esc_html__( 'Page Header', 'alpha' ),
					),
					'show_breadcrumb'     => array(
						'name'        => esc_html__( 'Breadcrumb', 'alpha' ),
						'parent'      => 'ptb',
						'layout_part' => esc_html__( 'Page Header', 'alpha' ),
					),
					'top_block'           => array(
						'name'   => esc_html__( 'Top Block', 'alpha' ),
						'parent' => 'top_block',
					),
					'top_sidebar'         => array(
						'name'   => esc_html__( 'Top Filter Sidebar', 'alpha' ),
						'parent' => 'top_sidebar',
					),
					'left_sidebar'        => array(
						'name'   => esc_html__( 'Left Sidebar', 'alpha' ),
						'parent' => 'left_sidebar',
					),
					'right_sidebar'       => array(
						'name'   => esc_html__( 'Right Sidebar', 'alpha' ),
						'parent' => 'right_sidebar',
					),
					'inner_top_block'     => array(
						'name'   => esc_html__( 'Inner Top Block', 'alpha' ),
						'parent' => 'inner_top_block',
					),
					'inner_bottom_block'  => array(
						'name'   => esc_html__( 'Inner Bottom Block', 'alpha' ),
						'parent' => 'inner_bottom_block',
					),
					'bottom_block'        => array(
						'name'   => esc_html__( 'Bottom Block', 'alpha' ),
						'parent' => 'bottom_block',
					),
					'footer'              => array(
						'name'   => esc_html__( 'Footer', 'alpha' ),
						'parent' => 'footer',
					),
					'products_column'     => array(
						'name'        => esc_html__( 'Product Columns', 'alpha' ),
						'parent'      => 'content_archive_product',
						'layout_part' => esc_html__( 'Shop', 'alpha' ),
					),
					'loadmore_type'       => array(
						'name'        => esc_html__( 'Load More', 'alpha' ),
						'parent'      => 'content_archive_product',
						'layout_part' => esc_html__( 'Shop', 'alpha' ),
					),
					'count_select'        => array(
						'name'        => esc_html__( 'Products Count Select', 'alpha' ),
						'parent'      => 'content_archive_product',
						'layout_part' => esc_html__( 'Shop', 'alpha' ),
					),
					'single_product_type' => array(
						'name'        => esc_html__( 'Single Product Type', 'alpha' ),
						'parent'      => 'content_single_product',
						'layout_part' => esc_html__( 'Single Product', 'alpha' ),
					),
					'product_data_type'   => array(
						'name'        => esc_html__( 'Product Data Type', 'alpha' ),
						'parent'      => 'content_single_product',
						'layout_part' => esc_html__( 'Single Product', 'alpha' ),
					),
					'error_block'         => array(
						'name'   => esc_html__( '404 Block', 'alpha' ),
						'parent' => 'content_error',
					),
				)
			);

			// Block
			$blocks = apply_filters(
				'alpha_layout_builder_block_parts',
				array(
					'popup',
					'top_bar',
					'header',
					'ptb',
					'top_block',
					'inner_top_block',
					'inner_bottom_block',
					'bottom_block',
					'footer',
					'error_block',
				)
			);

			// Widget Area
			$widgets = apply_filters(
				'alpha_layout_builder_widget_area_parts',
				array(
					'top_sidebar',
					'left_sidebar',
					'right_sidebar',
				)
			);

			foreach ( $slugs as $key => $data ) {
				if ( ! empty( $panel_map[ $key ] ) ) {
					$item = array(
						'name'        => $data['name'],
						'layout'      => $panel_map[ $key ]['title'],
						'layout_url'  => esc_url( admin_url( 'admin.php?page=alpha-layout-builder&category=' . $panel_map[ $key ]['category'] . '&index=' . $panel_map[ $key ]['index'] . '&slug=' . $data['parent'] ) ),
						'layout_part' => isset( $data['layout_part'] ) ? $data['layout_part'] : $data['name'],
					);

					if ( ! empty( $alpha_layout[ $key ] ) ) {
						if ( in_array( $key, $blocks ) ) {
							$post_id = $alpha_layout[ $key ];
							if ( ! empty( get_post( $post_id ) ) ) {
								$item['block'] = get_post( $post_id )->post_name;

								if ( alpha_get_feature( 'fs_pb_elementor' ) && defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
									$edit_link = admin_url( 'post.php?post=' . $post_id . '&action=elementor' );
								} else {
									$edit_link = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
								}

								$item['block_url'] = esc_url( $edit_link );
							} elseif ( 'hide' == $alpha_layout[ $key ] ) {
								$item['block']     = esc_html__( 'Hide', 'alpha' );
								$item['block_url'] = $item['layout_url'];
							}
						}
						if ( in_array( $key, $widgets ) ) {
							$item['block']     = $alpha_layout[ $key ];
							$item['block_url'] = esc_url( admin_url( 'widgets.php' ) );
							if ( 'hide' == $item['block'] ) {
								$item['block']     = esc_html__( 'Hide', 'alpha' );
								$item['block_url'] = $item['layout_url'];
							}
						}
					}

					$layout_parts[ $data['parent'] ][] = $item;
				}
			}

			return array_values( apply_filters( 'alpha_layout_builder_parts', $layout_parts ) );
		}

		/**
		 * Enqueue Scripts for Layout Builder
		 *
		 * @since 1.0
		 */
		public function enqueue_scripts() {
			$layout_parts = $this->get_layout_parts();

			wp_enqueue_script( 'alpha-layout-builder', alpha_framework_uri( '/admin/layout-builder/layout-builder' . ALPHA_JS_SUFFIX ), array( 'jquery-core' ), ALPHA_VERSION, true );

			// Localize vars.
			wp_localize_script(
				'alpha-layout-builder',
				'alpha_layout_vars',
				apply_filters(
					'alpha_layout_vars',
					array(
						'layout_parts' => json_encode( $layout_parts ),
					)
				)
			);
		}
		/**
		 * Add Layout Toolbar Menu
		 *
		 * @since 1.0
		 */
		public function add_layout_toolbar_menu( $self ) {
			if ( ! is_admin() ) {
				$self->add_wp_toolbar_menu_item(
					'<span class="ab-icon dashicons dashicons-alpha-layout"></span><span class="ab-label">' . esc_html__( 'Layout Builder', 'alpha' ) . '</span>',
					false,
					esc_url( admin_url( 'admin.php?page=alpha-layout-builder' ) ),
					array(
						'class'  => 'alpha-layout-menu',
						'target' => '_blank',
					),
					'alpha-layout'
				);
			}
		}

		/**
		 * Setup layout
		 *
		 * @since 1.0
		 */
		public function setup_layout() {
			global $alpha_layout;
			$alpha_layout = $this->get_layout();
		}

		/**
		 * Get controls
		 *
		 * @since 1.0
		 */
		public function get_controls() {

			return apply_filters(
				'alpha_layout_get_controls',
				array(
					'general'                 => array(
						'wrap'        => array(
							'type'    => 'image',
							'label'   => esc_html__( 'Wrap except header and footer', 'alpha' ),
							'options' => array(
								'container'       => array(
									'image' => 'site-boxed.svg',
									'title' => esc_html__( 'Container', 'alpha' ),
								),
								'container-fluid' => array(
									'image' => 'site-fluid.svg',
									'title' => esc_html__( 'Container Fluid', 'alpha' ),
								),
								'full'            => array(
									'image' => 'site-full.svg',
									'title' => esc_html__( 'Full', 'alpha' ),
								),
							),
						),
						'popup'       => array(
							'type'  => 'block_popup',
							'label' => esc_html__( 'Popup', 'alpha' ),
						),
						'popup_delay' => array(
							'type'  => 'number',
							'label' => esc_html__( 'Popup Delay (s)', 'alpha' ),
							'unit'  => esc_html( 'seconds', 'alpha' ),
						),
						'top_bar'     => array(
							'type'  => 'block',
							'label' => esc_html__( 'Top Notice Block', 'alpha' ),
						),
					),
					'header'                  => array(
						'header' => array(
							'type'  => 'block_header',
							'label' => esc_html__( 'Header', 'alpha' ),
						),
					),
					'footer'                  => array(
						'footer' => array(
							'type'  => 'block_footer',
							'label' => esc_html__( 'Footer', 'alpha' ),
						),
					),
					'ptb'                     => array(
						'ptb'             => array(
							'type'  => 'block',
							'label' => esc_html__( 'Page Title Bar', 'alpha' ),
						),
						'show_breadcrumb' => array(
							'type'  => 'toggle',
							'label' => esc_html__( 'Show Breadcrumb', 'alpha' ),
						),
						'breadcrumb_wrap' => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Breadcrumb Wrap', 'alpha' ),
							'options' => array(
								''                => esc_html__( 'Default', 'alpha' ),
								'container'       => esc_html__( 'Container', 'alpha' ),
								'container-fluid' => esc_html__( 'Container Fluid', 'alpha' ),
								'full'            => esc_html__( 'Full', 'alpha' ),
							),
						),
						'title'           => array(
							'type'        => 'text',
							'label'       => esc_html__( 'Page Title', 'alpha' ),
							'description' => esc_html__( 'If you give title in page metabox, its priority is higher than layout builder.', 'alpha' ),
						),
						'subtitle'        => array(
							'type'        => 'text',
							'label'       => esc_html__( 'Page Subtitle', 'alpha' ),
							'description' => esc_html__( 'If you give subtitle in page metabox, its priority is higher than layout builder.', 'alpha' ),
						),
					),
					'top_block'               => array(
						'top_block' => array(
							'type'  => 'block',
							'label' => esc_html__( 'Top Block', 'alpha' ),
						),
					),
					'bottom_block'            => array(
						'bottom_block' => array(
							'type'  => 'block',
							'label' => esc_html__( 'Bottom Block', 'alpha' ),
						),
					),
					'inner_top_block'         => array(
						'inner_top_block' => array(
							'type'  => 'block',
							'label' => esc_html__( 'Inner Top Block', 'alpha' ),
						),
					),
					'inner_bottom_block'      => array(
						'inner_bottom_block' => array(
							'type'  => 'block',
							'label' => esc_html__( 'Inner Bottom Block', 'alpha' ),
						),
					),
					'top_sidebar'             => array(
						'top_sidebar' => array(
							'type'  => 'block_sidebar',
							'label' => esc_html__( 'Horizontal Filter Widgets', 'alpha' ),
						),
					),
					'left_sidebar'            => array(
						'left_sidebar'       => array(
							'type'  => 'block_sidebar',
							'label' => esc_html__( 'Left Sidebar', 'alpha' ),
						),
						'left_sidebar_type'  => array(
							'type'    => 'image',
							'label'   => esc_html__( 'Sidebar Type', 'alpha' ),
							'options' => array(
								'classic'   => array(
									'image' => 'ls-classic.svg',
									'title' => esc_html__( 'Classic', 'alpha' ),
								),
								'offcanvas' => array(
									'image' => 'ls-offcanvas.svg',
									'title' => esc_html__( 'Off Canvas', 'alpha' ),
								),
							),
						),
						'left_sidebar_width' => array(
							'type'  => 'text',
							'label' => esc_html__( 'Sidebar Width e.g: 250px', 'alpha' ),
						),
					),
					'right_sidebar'           => array(
						'right_sidebar'       => array(
							'type'  => 'block_sidebar',
							'label' => esc_html__( 'Right Sidebar', 'alpha' ),
						),
						'right_sidebar_type'  => array(
							'type'    => 'image',
							'label'   => esc_html__( 'Right Sidebar Type', 'alpha' ),
							'options' => array(
								'classic'   => array(
									'image' => 'rs-classic.svg',
									'title' => esc_html__( 'Classic', 'alpha' ),
								),
								'offcanvas' => array(
									'image' => 'rs-offcanvas.svg',
									'title' => esc_html__( 'Off Canvas', 'alpha' ),
								),
							),
						),
						'right_sidebar_width' => array(
							'type'  => 'text',
							'label' => esc_html__( 'Sidebar Width e.g: 250px', 'alpha' ),
						),
					),
					'content_error'           => array(
						'error_block' => array(
							'type'  => 'block',
							'label' => esc_html__( 'Error Block', 'alpha' ),
						),
					),
					'content_single_product'  => array(
						'single_product_type'     => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Single Product Type', 'alpha' ),
							'options' => apply_filters(
								'alpha_sp_types',
								array(
									''              => esc_html__( 'Default', 'alpha' ),
									'vertical'      => esc_html__( 'Vertical Thumbs', 'alpha' ),         // @feature: fs_spt_vertical
									'horizontal'    => esc_html__( 'Horizontal Thumbs', 'alpha' ),       // @feature: fs_spt_horizontal
									'grid'          => esc_html__( 'Grid Images', 'alpha' ),             // @feature: fs_spt_grid
									'masonry'       => esc_html__( 'Masonry', 'alpha' ),                 // @feature: fs_spt_masonry
									'gallery'       => esc_html__( 'Gallery', 'alpha' ),                 // @feature: fs_spt_gallery
									'sticky-info'   => esc_html__( 'Sticky Information', 'alpha' ),      // @feature: fs_spt_sticky-info
									'sticky-thumbs' => esc_html__( 'Sticky Thumbs', 'alpha' ),           // @feature: fs_spt_sticky-thumbs
									'sticky-both'   => esc_html__( 'Left &amp; Right Sticky', 'alpha' ), // @feature: fs_spt_sticky-both
									'builder'       => esc_html__( 'Use Builder', 'alpha' ),             // @feature: fs_builder_singleproduct
								),
								'layout'
							),
						),
						'single_product_template' => array(
							'type'  => 'block_product_layout',
							'label' => esc_html__( 'Single Product Layout', 'alpha' ),
						),
						'product_data_type'       => array(
							'type'    => 'buttonset',
							'label'   => esc_html__( 'Product Data Type', 'alpha' ),
							'options' => array(
								'tab'       => esc_html__( 'Tab', 'alpha' ),
								'accordion' => esc_html__( 'Accordion', 'alpha' ),
								'section'   => esc_html__( 'Section', 'alpha' ),
							),
						),
					),
					'content_archive_product' => array(
						// 'cs_products_grid' => array(
						// 	'type'  => 'title',
						// 	'label' => esc_html__( 'Products Grid', 'alpha' ),
						// ),
						'products_column' => array(
							'type'  => 'number',
							'label' => esc_html__( 'Products Column', 'alpha' ),
							'min'   => 1,
							'max'   => 8,
						),
						'loadmore_type'   => array(
							'type'    => 'image',
							'label'   => esc_html__( 'Load More', 'alpha' ),
							'options' => array(
								'page'   => array(
									'image' => 'loadmore-page.png',
									'title' => esc_html( 'Pagination', 'alpha' ),
								),
								'button' => array(
									'image' => 'loadmore-btn.png',
									'title' => esc_html( 'Button', 'alpha' ),
								),
								'scroll' => array(
									'image' => 'loadmore-scroll.png',
									'title' => esc_html( 'Infinite Scroll', 'alpha' ),
								),
							),
						),
						'count_select'    => array(
							'type'    => 'text',
							'label'   => esc_html__( 'Products Count Select', 'alpha' ),
							'tooltip' => esc_html__( 'Input numbers of count select box(9, _12, 24, 36).', 'alpha' ),
						),
					),
				)
			);
		}

		/**
		 * Get layout theme options map
		 *
		 * @since 1.0
		 */
		function get_theme_options_map( $options_map, $layout_name ) {

			$res = array();
			if ( 'archive_product' == $layout_name ) {
				$res = array(
					'products_column' => 'products_column',
					'products_gap'    => 'products_gap',
					'count_select'    => 'products_count_select',
					'loadmore_type'   => 'products_load',
				);
			} elseif ( 'single_product' == $layout_name ) {
				$res = array(
					'single_product_type'          => 'single_product_type',
					'single_product_sticky'        => 'single_product_sticky',
					'single_product_sticky_mobile' => 'single_product_sticky_mobile',
					'products_count_select'        => 'products_count_select',
					'products_load'                => 'products_load',
					'product_data_type'            => 'product_data_type',
				);
			} elseif ( 'archive_' == substr( $layout_name, 0, 8 ) ) {
				$post_type = substr( $layout_name, 8 );
				if ( ALPHA_NAME == substr( $post_type, 0, strlen( ALPHA_NAME ) ) ) {
					$post_type = substr( $post_type, strlen( ALPHA_NAME ) + 1 );
				}
				$res = array(
					'post_type'      => $post_type . '_type',
					'post_overlay'   => $post_type . '_overlay',
					'posts_column'   => $post_type . 's_column',
					'posts_layout'   => $post_type . 's_layout',
					'posts_filter'   => $post_type . 's_filter',
					'excerpt_type'   => 'post' == $post_type ? 'excerpt_type' : $post_type . '_excerpt_type',
					'excerpt_length' => 'post' == $post_type ? 'excerpt_length' : $post_type . '_excerpt_length',
					'loadmore_type'  => $post_type . 's_load',
				);
			} elseif ( 'single_' == substr( $layout_name, 0, 7 ) ) {
				$post_type = substr( $layout_name, 7 );
				if ( ALPHA_NAME == substr( $post_type, 0, strlen( ALPHA_NAME ) ) ) {
					$post_type = substr( $post_type, strlen( ALPHA_NAME ) + 1 );
				}
				$res = array(
					'posts_layout'     => $post_type . 's_layout',
					'related_count'    => $post_type . '_related_count',
					'related_column'   => $post_type . '_related_column',
					'related_order'    => $post_type . '_related_order',
					'related_orderway' => $post_type . 's_related_orderway',
				);
			}
			// } elseif ( 'archive_post' == $layout_name ) {
			// 	$res = array(
			// 		'post_type'      => 'post_type',
			// 		'post_overlay'   => 'post_overlay',
			// 		'posts_column'   => 'posts_column',
			// 		'posts_layout'   => 'posts_layout',
			// 		'posts_filter'   => 'posts_filter',
			// 		'excerpt_type'   => 'excerpt_type',
			// 		'excerpt_length' => 'excerpt_length',
			// 		'loadmore_type'  => 'posts_load',
			// 	);
			// } elseif ( 'single_post' == $layout_name ) {
			// 	$res = array(
			// 		'posts_layout'     => 'posts_layout',
			// 		'related_count'    => 'post_related_count',
			// 		'related_column'   => 'post_related_column',
			// 		'related_order'    => 'post_related_order',
			// 		'related_orderway' => 'posts_related_orderway',
			// 	);
			// }
			return apply_filters( 'alpha_get_options_map', $res, $options_map, $layout_name );
		}

		/**
		 * Get layout
		 *
		 * @since 1.0
		 */
		public function get_layout( $layout_name = '' ) {

			global $wp_query;
			$layout         = array(
				'alpha_panel_map' => array(),
			);
			$all_conditions = alpha_get_option( 'conditions' );
			$all_controls   = $this->get_controls();

			if ( ! $layout_name ) {
				$layout_name = alpha_get_page_layout();
			}

			// create layout value
			foreach ( $all_controls as $part => $controls ) {
				if ( 'content_' == substr( $part, 0, 8 ) ) {
					if ( 'content_' . $layout_name == $part ) {
						// create empty layout content value
						foreach ( $controls as $name => $control ) {
							$layout[ $name ]           = '';
							$layout['alpha_panel_map'] = array(
								$name => '',
							);
						}
					}
					continue;
				}
				foreach ( $controls as $name => $control ) {
					$layout[ $name ]           = '';
					$layout['alpha_panel_map'] = array(
						$name => '',
					);
				}
			}

			/**
			 * Filters the retrieving layout value from theme options.
			 *
			 * @since 1.0
			 */
			$options_map = apply_filters( 'alpha_layout_theme_options_map', array(), $layout_name );
			foreach ( $options_map as $option => $name ) {
				$layout[ $option ] = alpha_get_option( $name );
			}

			// Retrieve current term information in single or archive pages.
			$current_term_id    = false;
			$current_taxonomy   = false;
			$current_term       = false;
			$current_post_id    = (string) get_the_ID();
			$current_post_terms = null;
			if ( $wp_query->is_tax || $wp_query->is_category || $wp_query->is_tag ) {
				$current_term     = $wp_query->get_queried_object();
				$current_term_id  = $current_term->term_id;
				$current_taxonomy = $current_term->taxonomy;
			}

			// Apply only site layout.
			$apply_only_site_layout = apply_filters( 'alpha_apply_only_site_layout', apply_filters( 'alpha_is_vendor_store', false ) );

			// retrieve layout value from layout builder.

			if ( $all_conditions && is_array( $all_conditions ) ) {
				foreach ( $all_conditions as $category => $conditions ) {

					if ( 'site' != $category && $apply_only_site_layout ) {
						continue;
					}

					if ( is_front_page() && 'single_front' == $category  // if home layout
						|| 'site' == $category                          // if global layout
						|| $layout_name == $category                    // if current post type's single or archive layout
						|| is_search() && 'search' == $category         // if search layout
						) {

						$index = 0;
						foreach ( $conditions as $condition ) {
							$pass = false;

							if ( 'site' == $category || 'error' == $category || 'single_front' == $category ) {

								// if no condition scheme exists
								$pass = true;

							} elseif ( ! empty( $condition['scheme'] ) ) {

								// check scheme

								$scheme = $condition['scheme'];

								if ( ! empty( $scheme['all'] ) && $scheme['all'] ) {

									// apply for all cases.
									$pass = true;

								} elseif ( is_search() && 'search' == $category ) {

									$type = get_query_var( 'post_type' );
									if ( 'any' == $type ) {
										$type = 'post';
									}

									if ( ! is_array( $scheme ) || ! count( $scheme ) || isset( $scheme[ $type ] ) && $scheme[ $type ] ) {
										$pass = true;
									}
								} elseif ( $current_term || function_exists( 'is_shop' ) && is_shop() || is_home() && 'archive_post' == $category ) { // Archive pages

									foreach ( $scheme as $scheme_key => $scheme_data ) {

										if (
										'category' == $scheme_key && $wp_query->is_category ||
										'post_tag' == $scheme_key && $wp_query->is_tag ||
										taxonomy_exists( $scheme_key ) && $wp_query->is_tax && $current_term->taxonomy == $scheme_key
										) {
											if ( is_array( $scheme_data ) && count( $scheme_data ) ) {
												if ( in_array( (string) $current_term->term_id, $scheme_data ) ) {
													$pass = true;
												}
											} elseif ( $scheme_data ) {
												$pass = true;
											}
										}
									}
								} else { // Single Pages

									foreach ( $scheme as $scheme_key => $scheme_data ) {

										if ( 'child' == $scheme_key ) {
											if ( is_array( $scheme_data ) && in_array( wp_get_post_parent_id( 0 ), $scheme_data ) ) {
												$pass = true;
											}
										} elseif ( taxonomy_exists( $scheme_key ) ) {

											// Has matched term of listed taxonomy

											$found_term = false;
											if ( ! $current_post_terms ) {
												$current_post_terms = get_terms();
											}

											foreach ( $current_post_terms as $term ) {
												if ( $term->taxonomy == $scheme_key ) {
													$found_term = true;
												}
											}

											if ( is_array( $scheme_data ) && count( $scheme_data ) ) {
												foreach ( $current_post_terms as $term ) {
													if ( in_array( (string) $term->term_id, $scheme_data ) ) {
														$pass = true;
													}
												}
											} elseif ( $scheme_data && $found_term ) {
												$pass = true;
											}
										} elseif ( post_type_exists( $scheme_key ) && is_singular( $scheme_key ) &&
										is_array( $scheme_data ) && count( $scheme_data ) &&
										in_array( $current_post_id, $scheme_data ) ) {

											// Pass only post's id exists

											$pass = true;
										}
									}
								}
							}

							// if pass
							if ( $pass && isset( $condition['options'] ) && is_array( $condition['options'] ) ) {
								foreach ( $condition['options'] as $name => $value ) {
									if ( $value ) {
										$layout[ $name ]                    = $value;
										$layout['alpha_panel_map'][ $name ] = array(
											'title'    => $condition['title'],
											'category' => $category,
											'index'    => $index,
										);
									}
								}
							}

							$index = $index + 1;
						}
					}
				}
			}

			/**
			 * Filters the layout.
			 *
			 * @param array  $layout      The layouts
			 * @param string $layout_name The layout name
			 * @since 1.0
			 */
			return apply_filters( 'alpha_get_layout', $layout, $layout_name );
		}

		/**
		 * Setup title and subtitle
		 *
		 * @since 1.0
		 */
		public function setup_titles() {
			// If title or subtitle is already set, return
			global $alpha_layout;
			if ( ! ( empty( $alpha_layout['title'] ) || empty( $alpha_layout['subtitle'] ) ) ) {
				return;
			}

			// Get page title and subtitle for titlebar.
			global $wp_query;
			$title    = '';
			$subtitle = '';

			if ( ! $title ) {
				if ( function_exists( 'is_product_category' ) && is_product_category() ) {
					$cats     = explode( '/', $wp_query->query['product_cat'] );
					$term     = get_term_by( 'slug', array_pop( $cats ), 'product_cat' );
					$title    = $term->name;
					$subtitle = sanitize_text_field( get_the_title( wc_get_page_id( 'shop' ) ) );
				} elseif ( function_exists( 'is_product_tag' ) && is_product_tag() ) {
					$term  = get_term_by( 'slug', $wp_query->query['product_tag'], 'product_tag' );
					$title = $term->name;
					/* translators: %s: product tag */
					$subtitle = sprintf( __( 'Products tagged &ldquo;%s&rdquo;', 'woocommerce' ), $term->name );
				} elseif ( alpha_is_shop() ) {
					$title    = sanitize_text_field( get_the_title( wc_get_page_id( 'shop' ) ) );
					$subtitle = '';

					// Custom Taxonomy Archive
					$term = get_queried_object();
					if ( $term && ! empty( $term->name ) && ! empty( $term->taxonomy ) ) {
						$title    = $term->name;
						$subtitle = sanitize_text_field( get_the_title( wc_get_page_id( 'shop' ) ) );
					}
				} elseif ( is_home() || is_post_type_archive( 'post' ) ) {
					$title    = apply_filters( 'alpha_blog_ptb_title', get_the_title( get_option( 'page_for_posts' ) ) );
					$subtitle = '';
				} elseif ( is_search() ) {
					$title    = '<span id="search-results-count">' . $wp_query->found_posts . '</span> ' . esc_html__( 'Search Results Found', 'alpha' );
					$subtitle = esc_html__( 'You searched for:', 'alpha' ) . ' &quot;' . esc_html( get_search_query( false ) ) . '&quot;';
				} elseif ( is_archive() ) {

					if ( is_author() ) { // Author

						$title    = get_the_archive_title();
						$subtitle = esc_html__( 'This author has written', 'alpha' ) . ' ' . get_the_author_posts() . ' ' . esc_html__( 'articles', 'alpha' );
					} elseif ( is_post_type_archive() ) { // Post Type archive title

						$title = post_type_archive_title( '', false );
					} elseif ( is_day() ) { // Daily archive title
						// translators: %s represents date
						$title = sprintf( esc_html__( 'Daily Archives: %s', 'alpha' ), get_the_date() );
					} elseif ( is_month() ) { // Monthly archive title
						// translators: %s represents date
						$title = sprintf( esc_html__( 'Monthly Archives: %s', 'alpha' ), get_the_date( esc_html_x( 'F Y', 'Page title monthly archives date format', 'alpha' ) ) );
					} elseif ( is_year() ) { // Yearly archive title

						// translators: %s represents date
						$title = sprintf( esc_html__( 'Yearly Archives: %s', 'alpha' ), get_the_date( esc_html_x( 'Y', 'Page title yearly archives date format', 'alpha' ) ) );
					} else { // Categories/Tags/Other

						// Get term title
						$title = single_term_title( '', false );

						// Fix for plugins that are archives but use pages
						if ( ! $title ) {
							$title = get_the_title( get_the_ID() );
						}
					}
				} elseif ( is_404() ) {
					$title    = apply_filters( 'alpha_404_ptb_title', esc_html__( 'Error 404', 'alpha' ) );
					$subtitle = '';
				} else {
					$title     = sanitize_text_field( get_the_title() );
					$parent_id = wp_get_post_parent_id( get_the_ID() );
					if ( $parent_id ) {
						$subtitle = get_the_title( $parent_id );
					}
				}
			}

			if ( empty( $alpha_layout['title'] ) ) {
				$alpha_layout['title'] = $title;
			}

			if ( empty( $alpha_layout['subtitle'] ) ) {
				$alpha_layout['subtitle'] = $subtitle;
			}
		}

		/**
		 * Print partial block content
		 *
		 * @since 1.0
		 */
		public function print_part_block( $arg = ALPHA_BEFORE_CONTENT ) {
			$block_name = '';

			global $alpha_layout;

			if ( doing_action( 'alpha_before_content' ) && ! empty( $alpha_layout['top_block'] ) && 'hide' != $alpha_layout['top_block'] ) {
				$block_name = sanitize_text_field( $alpha_layout['top_block'] );
				echo '<div class="top-block">';
			} elseif ( doing_action( 'alpha_before_inner_content' ) && ! empty( $alpha_layout['inner_top_block'] ) && 'hide' != $alpha_layout['inner_top_block'] ) {
				$block_name = sanitize_text_field( $alpha_layout['inner_top_block'] );
				echo '<div class="inner-top-block">';
			} elseif ( doing_action( 'alpha_after_inner_content' ) && ! empty( $alpha_layout['inner_bottom_block'] ) && 'hide' != $alpha_layout['inner_bottom_block'] ) {
				$block_name = sanitize_text_field( $alpha_layout['inner_bottom_block'] );
				echo '<div class="inner-bottom-block">';
			} elseif ( doing_action( 'alpha_after_content' ) && ! empty( $alpha_layout['bottom_block'] ) && 'hide' != $alpha_layout['bottom_block'] ) {
				$block_name = sanitize_text_field( $alpha_layout['bottom_block'] );
				echo '<div class="bottom-block">';
			}

			alpha_print_template( $block_name );

			if ( $block_name ) {
				echo '</div>';
			}
		}

		/**
		 * Print css vars of layout builder.
		 *
		 * @since 1.0
		 */
		public function print_css_vars() {
			$style = '';
			global $alpha_layout;

			if ( ! empty( $alpha_layout['left_sidebar_width'] ) && ! empty( $alpha_layout['left_sidebar'] ) && 'hide' != $alpha_layout['left_sidebar'] ) {
				$v = $this->format_distance( $alpha_layout['left_sidebar_width'] );
				if ( $v ) {
					$style .= '--alpha-left-sidebar-width:' . $v . ';';
				}
			}
			if ( ! empty( $alpha_layout['right_sidebar_width'] ) && ! empty( $alpha_layout['right_sidebar'] ) && 'hide' != $alpha_layout['right_sidebar'] ) {
				$v = $this->format_distance( $alpha_layout['right_sidebar_width'] );
				if ( $v ) {
					$style .= '--alpha-right-sidebar-width:' . $v . ';';
				}
			}

			if ( $style ) {
				$style = 'html {' . $style . '}';
				wp_add_inline_style( 'alpha-theme', $style );
			}
		}

		/**
		 * Get format of distance unit.
		 *
		 * @since 1.0
		 * @param string $distance Distance string to format.
		 * @return string Formated distance
		 */
		public function format_distance( $distance ) {
			if ( (string) (float) $distance == $distance ) {
				return $distance . 'px';
			}
			$matches = array();
			preg_match( '/[\d|\.]+[px|rem|%]+/i', $distance, $matches );
			return count( $matches ) && $matches[0] ? $matches[0] : '';
		}
	}
}

Alpha_Layout_Builder::get_instance();
