<?php

/**
 * Bootstrap file for the WP L10n Validator unit tests.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

/**
 * The path to the base directory of the validator.
 *
 * @since 0.1.0
 */
define( 'WP_L10N_VALIDATOR_DIR', dirname( dirname( __DIR__ ) ) );

/**
 * The WordPress L10n validator.
 *
 * @since 0.1.0
 */
require_once WP_L10N_VALIDATOR_DIR . '/wp-l10n-validator.php';

/**
 * A special extension of WP_L10n_Validator for use with the unit tests.
 *
 * @since 0.1.0
 */
require_once __DIR__ . '/class-test-l10n-validator.php';

/**
 * The test case parent for the validator tests.
 *
 * @since 0.1.0
 */
require_once __DIR__ . '/class-wp-l10n-validator-unittestcase.php';

// Let the tests begin!