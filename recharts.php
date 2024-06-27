<?php
/**
 * Plugin Name:       Recharts
 * Description:       WordPress dashboard widget recharts
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Abdul Hannan
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       recharts
 *
 * @package Recharts_Graph
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'RECHARTS_PLUGIN_FILE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Autoload require.
 */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Class Recharts_Graph
 */
class Recharts_Graph {
	/**
	 * Instance.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Register the activation hook.
		register_activation_hook( __FILE__, array( $this, 'recharts_plugin_activate' ) );

		// Admin enqueue.
		add_action( 'admin_enqueue_scripts', array( $this, 'recharts_admin_enqueue_assets' ) );

		// Load features.
		add_action( 'init', array( $this, 'recharts_initialize_features' ) );

		// Plugin Functions.
		new Recharts\Graph\RechartsFunctions();

		// Rest API.
		new Recharts\Graph\RestApi();
	}

	/**
	 * Plugin activate hook callback.
	 */
	public function recharts_plugin_activate() {
		do_action( 'recharts_plugin_activated' );
	}

	/**
	 * Instance.
	 *
	 * @return Recharts_Graph|null
	 */
	public static function recharts_get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize features.
	 */
	public function recharts_initialize_features() {
		// Load text domain.
		load_plugin_textdomain( 'recharts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Enqueue the admin scripts.
	 *
	 * @param string $hook The current admin page.
	 */
	public function recharts_admin_enqueue_assets( $hook ) {
		if ( 'index.php' === $hook ) {
			// Enqueue styles.
			wp_enqueue_style( 'recharts-style', plugin_dir_url( __FILE__ ) . 'build/style-index.css', array(), '1.0.0', 'all' );

			// Enqueue scripts.
			wp_enqueue_script( 'recharts-script', plugin_dir_url( __FILE__ ) . 'build/index.js', array( 'wp-element' ), '1.0.0', true );

			// Localize script.
			wp_localize_script(
				'recharts-script',
				'rechartsObj',
				array(
					'site_url' => site_url( '/' ),
					'root'     => esc_url_raw( rest_url() ),
					'nonce'    => wp_create_nonce( 'wp_rest' ),
				)
			);
		}
	}
}

/**
 * Instantiate the Recharts_Graph class.
 */
Recharts_Graph::recharts_get_instance();
