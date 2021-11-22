<?php
/**
 * Initialize TGM plugins
 *
 * @author     FunnyWP
 * @package    WP Alpha Framework
 * @subpackage theme
 * @since      1.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

class Alpha_TGM_Plugins {

	/**
	 * Array of plugins. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 *
	 * @var   array
	 * @since 1.0
	 */
	protected $plugins;

	/**
	 * Constructor
	 *
	 * Init plugins and register actions and filters.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->plugins = apply_filters(
			'alpha_plugin',
			array(
				array(
					'name'      => ALPHA_CORE_NAME,
					'slug'      => ALPHA_CORE_SLUG,
					'required'  => true,
					'url'       => ALPHA_CORE_PLUGIN_URI,
					'image_url' => ALPHA_ASSETS . '/images/admin/plugins/' . ALPHA_CORE_SLUG . '.svg',
					'usein'     => 'setup',
				),
				array(
					'name'      => 'Kirki',
					'slug'      => 'kirki',
					'required'  => true,
					'url'       => 'kirki/kirki.php',
					'image_url' => ALPHA_ASSETS . '/images/admin/plugins/kirki.png',
					'usein'     => 'setup',
				),
				array(
					'name'      => 'Woocommerce',
					'slug'      => 'woocommerce',
					'required'  => true,
					'url'       => 'woocommerce/woocommerce.php',
					'image_url' => ALPHA_ASSETS . '/images/admin/plugins/woocommerce.png',
					'usein'     => 'setup',
				),
				array(
					'name'      => 'Meta-Box',
					'slug'      => 'meta-box',
					'required'  => true,
					'url'       => 'meta-box/meta-box.php',
					'image_url' => ALPHA_ASSETS . '/images/admin/plugins/meta_box.png',
					'usein'     => 'setup',
				),
				array(
					'name'      => 'Elementor',
					'slug'      => 'elementor',
					'required'  => false,
					'url'       => 'elementor/elementor.php',
					'image_url' => ALPHA_ASSETS . '/images/admin/plugins/elementor.png',
					'usein'     => 'setup',
				),
			)
		);
		/*************************/
		/* TGM Plugin Activation */
		/*************************/
		$plugin = alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/plugins/tgm-plugin-activation/class-tgm-plugin-activation.php' );
		if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
			require_once $plugin;
		}

		add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );

		add_filter( 'tgmpa_notice_action_links', array( $this, 'update_action_links' ), 10, 1 );

		$this->plugins = $this->get_plugins_list();
	}

	/**
	 * Get plugins list including transient.
	 *
	 * @return array All plugins list.
	 * @since 1.0
	 */
	public function get_plugins_list() {
		// get transient
		$plugins = get_site_transient( 'alpha_plugins' );
		if ( false === $plugins && Alpha_Admin::get_instance()->is_registered() ) {
			$plugins = $this->update_plugins_list();
		}
		if ( ! $plugins ) {
			return $this->plugins;
		}
		return array_merge( $plugins, $this->plugins );
	}

	/**
	 * Set transient to plugins list.
	 *
	 * @return array Plugins list.
	 * @since 1.0
	 */
	private function update_plugins_list() {

		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php' );
		$importer_api = new Alpha_Importer_API();
		$args = $importer_api->generate_args( false );
		$url      = $importer_api->get_url( 'plugins_version' );

		if ( isset( $args['code'] ) ) {
			$url = add_query_arg( 'code', $args['code'], $url );
		}
		$plugins = $importer_api->get_response( $url );
		if ( ! $plugins || is_wp_error( $plugins ) ) {
			if ( is_wp_error( $plugins ) ) {
				set_transient( 'alpha_purchase_code_error_msg', $plugins->get_error_message(), HOUR_IN_SECONDS * 24 * 7 );
			}
			set_site_transient( 'alpha_plugins', array(), HOUR_IN_SECONDS * 24 * 7 );
			return false;
		}
		delete_transient( 'alpha_purchase_code_error_msg' );
		setcookie( 'alpha_dismiss_code_error_msg', '', time() - 3600 );
		
		foreach ( $plugins as $key => $plugin ) {
			$args['plugin']               = $plugin['slug'];
			$plugins[ $key ]['source']    = add_query_arg( $args, $importer_api->get_url( 'plugins' ) );
			$plugins[ $key ]['image_url'] = alpha_framework_uri( '/admin/plugins/images/' . $args['plugin'] . '.png' );
			if ( ! isset( $plugins[ $key ]['usein'] ) ) {
				$plugins[ $key ]['usein'] = 'setup';
			}
		}
		// set transient
		set_site_transient( 'alpha_plugins', $plugins, 7 * 24 * HOUR_IN_SECONDS );

		return $plugins;
	}

	/**
	 * Register required plugins.
	 *
	 * @since 1.0
	 */
	public function register_required_plugins() {
		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 *
		 * @var   array
		 * @since 1.0
		 */
		$config = array(
			'domain'       => 'alpha',                    // Text domain - likely want to be the same as your theme.
			'default_path' => '',                          // Default absolute path to pre-packaged plugins
			'menu'         => 'install-required-plugins',  // Menu slug
			'has_notices'  => true,                        // Show admin notices or not
			'is_automatic' => true,                        // Automatically activate plugins after installation or not
			'message'      => '',                          // Message to output right before the plugins table
		);

		tgmpa( $this->plugins, $config );
	}

	/**
	 * Update plugins action url in setup wizard.
	 *
	 * @param  array $action_links Plugins action link.
	 * @return array               Return updated action link.
	 * @since 1.0
	 */
	public function update_action_links( $action_links ) {
		$url = add_query_arg(
			array(
				'page' => 'alpha-setup-wizard',
				'step' => 'default_plugins',
			),
			self_admin_url( 'admin.php' )
		);
		foreach ( $action_links as $key => $link ) {
			if ( $link ) {
				$link                 = preg_replace( '/<a([^>]*)href="([^"]*)"/i', '<a$1href="' . esc_url( $url ) . '"', $link );
				$action_links[ $key ] = $link;
			}
		}
		return $action_links;
	}
}

new Alpha_TGM_Plugins;
