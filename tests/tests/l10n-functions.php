<?php

/**
 * Recognizes l10n functions test case.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

/**
 * Check that the validator recognizes all of the l10n functions.
 *
 * @since 0.1.0
 */
class WP_L10n_Validator_L10n_Functions_Recognized_Test extends WP_L10n_Validator_UnitTestCase {

	/**
	 * The file to parse for the test.
	 *
	 * @since 0.1.0
	 *
	 * @type string $file
	 */
	protected static $file = 'l10n-functions.php';

	/**
	 * Test that the parser found the invalid textdomains.
	 *
	 * @since 0.1.0
	 */
	public function test_deprecated_functions_found() {

		$this->assertFoundDeprecatedFunction( '_c' );
		$this->assertFoundDeprecatedFunction( '_nc' );
		$this->assertFoundDeprecatedFunction( '__ngettext' );
		$this->assertFoundDeprecatedFunction( '__ngettext_noop' );
	}
}
