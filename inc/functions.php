<?php 
// Create database table when activating plugin 
function recharts_plugin_activate() {

    global $wpdb;

    // Set the table name
    $table_name =  $wpdb->prefix . 'recharts_data';

    // Charset and collation
    $charset_collate = $wpdb->get_charset_collate();

    // SQL statement to create the table
    $rechart_sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
        uv mediumint(20) NOT NULL,
        pv mediumint(20) NOT NULL,
        amt mediumint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Include the WordPress file for dbDelta function
     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Execute the query
    dbDelta( $rechart_sql );

}

