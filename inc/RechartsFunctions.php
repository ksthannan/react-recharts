<?php
namespace Recharts\Graph;

/**
 * Class RechartsFunctions.
 */
class RechartsFunctions {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Do something after plugin activation.
		add_action( 'recharts_plugin_activated', array( $this, 'recharts_insert_data_table' ) );
		add_action( 'recharts_plugin_activated', array( $this, 'recharts_demo_data_insert' ) );

		// Hook into the 'wp_dashboard_setup' action to register our custom widget.
		add_action( 'wp_dashboard_setup', array( $this, 'recharts_dashboard_widgets' ) );
	}

	/**
	 * Create database table when activating plugin.
	 */
	public function recharts_insert_data_table() {

		global $wpdb;

		// Set the table name.
		$table_name = $wpdb->prefix . 'recharts_data';

		// Charset and collation.
		$charset_collate = $wpdb->get_charset_collate();

		// SQL statement to create the table.
		$rechart_sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(50) NOT NULL,
			uv mediumint(20) NOT NULL,
			pv mediumint(20) NOT NULL,
			amt mediumint(20) NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";

		// Include the WordPress file for dbDelta function.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Execute the query.
		dbDelta( $rechart_sql );
	}

	/**
	 * Insert demo data automatically.
	 */
	public function recharts_demo_data_insert() {
		$data = array(
			array(
				'name'       => 'Page A',
				'uv'         => '6000',
				'pv'         => '4400',
				'amt'        => '2400',
				'created_at' => '2024-06-26 14:59:03',
			),
			array(
				'name'       => 'Page B',
				'uv'         => '3000',
				'pv'         => '1398',
				'amt'        => '2210',
				'created_at' => '2024-06-05 14:59:22',
			),
			array(
				'name'       => 'Page C',
				'uv'         => '4000',
				'pv'         => '9800',
				'amt'        => '2290',
				'created_at' => '2024-06-23 14:59:42',
			),
			array(
				'name'       => 'Page D',
				'uv'         => '2780',
				'pv'         => '3908',
				'amt'        => '2000',
				'created_at' => '2024-06-18 14:59:59',
			),
			array(
				'name'       => 'Page E',
				'uv'         => '1890',
				'pv'         => '4800',
				'amt'        => '2181',
				'created_at' => '2024-06-15 15:00:15',
			),
			array(
				'name'       => 'Page F',
				'uv'         => '2390',
				'pv'         => '3800',
				'amt'        => '2500',
				'created_at' => '2024-06-10 15:00:30',
			),
			array(
				'name'       => 'Page G',
				'uv'         => '3490',
				'pv'         => '4300',
				'amt'        => '2100',
				'created_at' => '2024-06-05 15:00:46',
			),
		);

		global $wpdb;

		// Set the table name.
		$table_name = $wpdb->prefix . 'recharts_data';

		// Read query.
		$data_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

		if ( ! $data_count > 0 ) {
			$this->recharts_insert_multiple_rows( $table_name, $data );
		}
	}

	/**
	 * Insert multiple rows at once.
	 *
	 * @param string $table The table name.
	 * @param array  $request The data to insert.
	 * @return bool|int False on failure, the number of rows inserted on success.
	 */
	public function recharts_insert_multiple_rows( $table, $request ) {
		global $wpdb;
		$column_keys   = '';
		$column_values = '';
		$sql           = '';
		$last_key      = array_key_last( $request );
		$first_key     = array_key_first( $request );
		foreach ( $request as $k => $value ) {
			$keys = array_keys( $value );

			// Prepare column keys & values.
			foreach ( $keys as $v ) {
				$column_keys   .= sanitize_key( $v ) . ',';
				$sanitize_value = sanitize_text_field( $value[ $v ] );
				$column_values .= is_numeric( $sanitize_value ) ? $sanitize_value . ',' : "'$sanitize_value'" . ',';
			}
			// Trim trailing comma.
			$column_keys   = rtrim( $column_keys, ',' );
			$column_values = rtrim( $column_values, ',' );
			if ( $first_key === $k ) {
				$sql .= "INSERT INTO {$table} ($column_keys) VALUES ($column_values),";
			} elseif ( $last_key == $k ) {
				$sql .= "($column_values)";
			} else {
				$sql .= "($column_values),";
			}

			// Reset keys & values to avoid duplication.
			$column_keys   = '';
			$column_values = '';
		}
		return $wpdb->query( $sql );
	}

	/**
	 * Function to register the custom dashboard widget.
	 */
	public function recharts_dashboard_widgets() {
		wp_add_dashboard_widget(
			'recharts_widget',
			__( 'Recharts', 'recharts' ),
			array( $this, 'recharts_widget_content' )
		);
	}

	/**
	 * Function to display the content of the custom dashboard widget.
	 */
	public function recharts_widget_content() {
		esc_html_e( '<div id="rechart_root">' . __( 'Loading...', 'recharts' ) . '</div>' );
	}
}
