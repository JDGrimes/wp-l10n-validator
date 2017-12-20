<?php

/**
 * Plugin Name: No config
 */

function do_some_things( $var ) {

	$var = apply_filters( 'some filter' );

	return $var;
}

function do_some_other_stuff() {

	echo 'Hello world';
}

function display_message() {

	_e( 'Message', 'textdomain' );
}
