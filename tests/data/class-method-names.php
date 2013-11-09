<?php

/**
 * Test file for checking class method name handling.
 *
 * @package WP_L10n_Validator
 * @since 0.1.0
 */

class FWIW extends IMHO {

	public function what_i_think( $topic ) {

		$this->topic = $topic;

		parent::init_opinion( _debug_ );
		$this->reason( _debug_ );
		self::add_bias( _debug_ );

		return $this->thoughts;
	}
}

$wpdb->query( _debug_ );

$query = new WP_Query( _debug_ );