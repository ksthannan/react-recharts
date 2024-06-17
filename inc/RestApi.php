<?php 
namespace Recharts\Graph;

// Rest API 
class RestApi{

    public function __construct(){
        // Hook the custom routes into REST API init
        add_action('rest_api_init', array($this, 'recharts_register_routes'));
    }

    // Register custom REST API routes
    function recharts_register_routes() {
        register_rest_route('recharts/v1', '/data', array(
            'methods' => 'GET',
            'callback' => array($this, 'recharts_get_entries'),
        ));
    }

    // Callback function to get all entries
    function recharts_get_entries() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'recharts_data';
        // query 
        $query = $wpdb->prepare("SELECT * FROM $table_name");
        $results = $wpdb->get_results($query, ARRAY_A);
        return new \WP_REST_Response($results, 200);
    }

}
