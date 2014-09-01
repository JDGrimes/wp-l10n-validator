<?php

/**
 * Textdomain test case.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

/**
 * Check that invalid textdomains are picked up by the parser.
 *
 * @since 0.1.0
 */
class WP_L10n_Validator_Textdomain_Test extends WP_L10n_Validator_UnitTestCase {

	/**
	 * The file to parse for the test.
	 *
	 * @since 0.1.0
	 *
	 * @type string $file
	 */
	protected static $file = '/bad-textdomains.php';

	/**
	 * Test that the parser found the invalid textdomains.
	 *
	 * @since 0.1.0
	 */
	public function test_invalid_textdomains_found() {

		$this->assertFoundUnexpectedTextdomain( 'wp-l10n-validator-test' );
		$this->assertFoundUnexpectedTextdomain( '' );
	}
}
