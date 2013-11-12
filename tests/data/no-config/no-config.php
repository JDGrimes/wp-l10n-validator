<?php

/**
 * Plugin Name: No config
 */

function do_some_things() {

	global $wpdb;

	return $wpdb->get_results( 'SELECT * FROM ' . $wpdb->posts );
}

function do_some_other_stuff() {

	echo 'Hello world';
}

function display_message() {

	_e( 'Message', 'textdomain' );
}
