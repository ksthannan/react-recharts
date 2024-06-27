<?php
namespace Recharts\Graph;

/**
 * Class RestApi.
 */
class RestApi {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook the custom routes into REST API init.
		add_action( 'rest_api_init', array( $this, 'recharts_register_routes' ) );
	}

	/**
	 * Register custom REST API routes.
	 */
	public function recharts_register_routes() {
		register_rest_route(
			'recharts/v1',
			'/data',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'recharts_get_entries' ),
				'permission_callback' => array( $this, 'recharts_permission_check' ),
			)
		);
	}

	/**
	 * Callback function to get all entries.
	 *
	 * @return \WP_REST_Response The response object.
	 */
	public function recharts_get_entries() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'recharts_data';

		// Query.
		$query   = $wpdb->prepare( "SELECT * FROM $table_name" );
		$results = $wpdb->get_results( $query, ARRAY_A );

		return new \WP_REST_Response( $results, 200 );
	}

	/**
	 * Permission callback function.
	 *
	 * @return bool True if the user has permission, false otherwise.
	 */
	public function recharts_permission_check() {
		return current_user_can( 'manage_options' );
	}
}
