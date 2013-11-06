<?php

/**
 * Catches bad l10n function arguments test case.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

/**
 * Make sure the validator catches non-string arguments passed to l10n functions.
 *
 * @since 0.1.0
 */
class WP_L10n_Validator_L10n_Bad_L10n_Args_Test extends WP_L10n_Validator_UnitTestCase {

	/**
	 * The file to parse for the test.
	 *
	 * @since 0.1.0
	 *
	 * @type string $file
	 */
	protected static $file = 'bad-l10n-func-args.php';

	/**
	 * Test that the parser found the invalid l10n function arguments.
	 *
	 * @since 0.1.0
	 */
	public function test_invalid_l10n_args_found() {

		$this->assertFoundNonGettextedString( 'test' );
		$this->assertFoundNonGettextedString( 'textdomain' );

		$this->assertFoundInvalidL10nArg( 1, '_e', 13 );
		$this->assertFoundInvalidL10nArg( 1, '_e', 14 );
		$this->assertFoundInvalidL10nArg( 2, '__', 19 );
		$this->assertFoundInvalidL10nArg( 2, '__', 20 );
	}
}
