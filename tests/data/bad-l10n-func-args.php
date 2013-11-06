<?php

/**
 * Test that all l10n functions are properly recognized.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

// Bad translation strings.
$test = 'test';

_e( $test, 'wp-l10n-validator-tests' );
_e( rand(), 'wp-l10n-validator-tests' );

// Bad textdomain
$textdomain = 'textdomain';

$bad = __( 'test', $textdomain );
$bad = __( 'test', MY_TEXTDOMAIN );

// Allowed var.
$two = 2;

$plural = _n( 'single', 'plural', $two, 'wp-l10n-validator-tests' );
