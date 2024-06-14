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

// Register the activation hook
register_activation_hook( __FILE__, 'recharts_plugin_activate' );

add_action('init', 'recharts_init');
function recharts_init(){
    // Load text domain 
    load_plugin_textdomain( 'recharts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// Enqueue the React scripts
function recharts_enqueue_scripts($hook) {

    wp_enqueue_style('recharts-style', plugin_dir_url(__FILE__) . 'build/style-index.css', array(), '1.0.0', 'all');

    wp_enqueue_script('recharts-script', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-element'), '1.0.0', true);

    // Localize script to pass data
    wp_localize_script('recharts-script', 'rechartsObj', array(
        'site_url' => site_url( '/' ),
    ));

}
add_action('admin_enqueue_scripts', 'recharts_enqueue_scripts');


// Hook into the 'wp_dashboard_setup' action to register our custom widget
add_action('wp_dashboard_setup', 'recharts_dashboard_widgets');

// Function to register the custom dashboard widget
function recharts_dashboard_widgets() {
    wp_add_dashboard_widget(
        'recharts_widget', 
        'Recharts',
        'recharts_widget_content' 
    );
}

// Function to display the content of the custom dashboard widget
function recharts_widget_content() {
    echo '<div id="rechart_root">'.__('Loading...', 'recharts').'</div>';
}

// Require files 
require_once RECHARTS_PLUGIN_FILE_PATH . "inc/functions.php";
require_once RECHARTS_PLUGIN_FILE_PATH . "inc/rest_api.php";






