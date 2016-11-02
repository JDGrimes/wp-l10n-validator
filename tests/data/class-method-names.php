<?php

/**
 * Test file for checking class method name handling.
 *
 * @package WP_L10n_Validator
 * @since 0.1.0
 */

function some_func() {
	// See #5
}

class IMHO {
	protected $ignored = 'imho' _debug_ ; _debug_
}

class FWIW extends IMHO {

	var $var _debug_;
	public $public _debug_;
	protected $protected _debug_;
	private $private _debug_;
	public $ignored = 'fwiw' _debug_;

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

$func = function() {};

get_some_class()->method( _debug_ );

wordpoints_component( $var )
	->get_sub_app( $var )
	->get_sub_app( _debug_ );

$var
	->wrap( _debug_ );

interface ParentI {
	public function parent_method( $var _debug_ );
}

interface ChildI extends ParentI {
	public function ignored( $var = 'something' _debug_ );
}

interface AnotherI {}

class Implementor implements ChildI, AnotherI {

	public function parent_method( $var _debug_ ) {
		$this->ignored( 'ignore me' _debug_ );
	}

	public function ignored( $var = 'something' _debug_ ) {}
}
