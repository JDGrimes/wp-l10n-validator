<?php

/**
 * Test file for checking class method name handling.
 *
 * @package WP_L10n_Validator
 * @since 0.1.0
 */

class FWIW extends IMHO {

	private function private_thoughts( _debug_ );

	public function what_i_think( $topic _debug_ ) {

		function inner_func( _debug_ ) {}

		$func = function() {};

		$this->topic = $topic;

		$this->ignored( 'ignore me' );

		$test = "{$topic}";
		$test = "${test}";

		$this->property->func( _debug_ );

		parent::init_opinion( _debug_ );
		$this->reason( _debug_ );
		self::add_bias( _debug_ );

		return $this->thoughts;
	}
}

$wpdb->query( _debug_ );

$query = new WP_Query( _debug_ );

$func = function() {}

get_some_class()->method( _debug_ );