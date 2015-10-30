<?php

/**
 * Plugin Name: With config
 */

function do_some_things() {

	global $wpdb;

	return $wpdb->get_results( 'SELECT * FROM ' . $wpdb->posts );
}

function do_some_other_stuff() {

	echo 'Hello world';
	echo 'Dlrow olleh';
}

function display_message() {

	_e( 'Message', 'textdomain' );
	func_to_ignore( 'yes' );
}

class AClass {
	protected $ignored = 'parent';
}
