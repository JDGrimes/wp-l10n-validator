<?php

/**
 * Testcase for checking class method name handling.
 *
 * @package WP_L10n_Validator
 * @since 0.1.0
 */

/**
 * Test that class method names are properly adjusted inside a class.
 *
 * @since 0.1.0
 */
class Class_Method_Name_Test extends WP_L10n_Validator_UnitTestCase {

	/**
	 * The file to parse for the test.
	 *
	 * @since 0.1.0
	 *
	 * @type string $file
	 */
	protected static $file = '/class-method-names.php';

	/**
	 * Set up ignores.
	 *
	 * Part of the test is checking that a method is ignored in a child class if it
	 * is ignored in the parent class.
	 *
	 * @since 0.1.2
	 */
	public static function configure_parser( $parser ) {

		$parser->add_ignored_functions( array( 'IMHO::ignored' => true ) );
	}

	/**
	 * Test that the class method names are correct.
	 *
	 * @since 0.1.0
	 */
	public function test_class_method_names() {

		$expected = array(
			'FWIW::private_thoughts',
			'FWIW::what_i_think',
			'inner_func',
			'FWIW::property->func',
			'IMHO::init_opinion',
			'FWIW::reason',
			'FWIW::add_bias',
			'$wpdb->query',
			'WP_Query::__construct',
		);

		foreach ( $expected as $key => $function_name ) {

			if ( isset( parent::$debugs[ $key ]['cur_func']['name'] ) )
				$this->assertEquals( $function_name, parent::$debugs[ $key ]['cur_func']['name'] );
			else
				$this->fail( 'Not enough debug results were found.' );

			unset( parent::$debugs[ $key ] );
		}
	}
}
