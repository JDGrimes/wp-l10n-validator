<?php

/**
 * Used for testing for invalid text domains.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

$a = __( 'test', 'wp-l10n-validator-tests' ); // Valid.
$b = __( 'test', 'wp-l10n-validator-test' );  // Oops.
