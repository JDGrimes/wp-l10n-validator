<?php

/**
 * Test that all l10n functions are properly recognized.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

__( 'test', 'wp-l10n-validator-tests' );
_e( 'test', 'wp-l10n-validator-tests' );
_c( 'test|context', 'wp-l10n-validator-tests' );
_nc( 'test|context', 'single', 'plural', 'wp-l10n-validator-tests' );
__ngettext( 'single', 'plural', 1, 'wp-l10n-validator-tests' );
_n( 'single', 'plural', 1, 'wp-l10n-validator-tests' );
__ngettext_noop( 'single', 'plural', 'wp-l10n-validator-tests' );
_n_noop( 'single', 'plural', 'wp-l10n-validator-tests' );
_x( 'test', 'context', 'wp-l10n-validator-tests' );
_ex( 'test', 'context', 'wp-l10n-validator-tests' );
_nx( 'singular', 'plural', 1, 'context', 'wp-l10n-validator-tests' );
_nx_noop( 'single', 'plural', 'context', 'wp-l10n-validator-tests' );
esc_attr__( 'test', 'wp-l10n-validator-tests' );
esc_html__( 'test', 'wp-l10n-validator-tests' );
esc_html_e( 'test', 'wp-l10n-validator-tests' );
esc_attr_e( 'test', 'wp-l10n-validator-tests' );
esc_attr_x( 'test', 'context', 'wp-l10n-validator-tests' );
esc_html_x( 'test', 'context', 'wp-l10n-validator-tests' );
