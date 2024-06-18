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
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define('RECHARTS_PLUGIN_FILE_PATH', plugin_dir_path( __FILE__ ));

/**
 * Autoload require
 */
require_once __DIR__ . "/vendor/autoload.php";

class Recharts_Graph{
    // Properties 
    private static $instance = null;

    public function __construct(){

        // Register the activation hook
        register_activation_hook( __FILE__, array($this, 'recharts_plugin_activate') );

        // admin enqueue 
        add_action('admin_enqueue_scripts', array($this, 'recharts_admin_enqueue_assets'));

        // load features 
        add_action('init', array($this, 'recharts_initialize_features'));

        // Plugin Functions 
        new Recharts\Graph\RechartsFunctions();

        // Rest API 
        new Recharts\Graph\RestApi();

    }

    // Plugin activate hook callback 
    public function recharts_plugin_activate(){
        do_action('recharts_plugin_activated');
    }

    /**
     * Instance
     */
    public static function recharts_get_instance() {
        if ( self::$instance == null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function recharts_initialize_features(){
        // Load text domain 
        load_plugin_textdomain( 'recharts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    }

    // Enqueue the admin scripts
    function recharts_admin_enqueue_assets($hook) {

        // Enqueue styles
        wp_enqueue_style('recharts-style', plugin_dir_url(__FILE__) . 'build/style-index.css', array(), '1.0.0', 'all');
        
        // Enqueue scripts 
        wp_enqueue_script('recharts-script', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-element'), '1.0.0', true);

        // Localize script
        wp_localize_script('recharts-script', 'rechartsObj', array(
            'site_url' => site_url( '/' ),
        ));
    }

}


/**
 * Instantiate
 */
Recharts_Graph::recharts_get_instance();

