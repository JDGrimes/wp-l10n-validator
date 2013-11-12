<?php

/**
 * Test case for bash commands.
 *
 * @package WP_L10n_Validator
 * @since 0.1.1
 */

/**
 * Test that the bash commands function properly.
 *
 * @since 0.1.1
 * @group dev
 */
class WP_L10n_Validator_CLI_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Test default CLI usage.
	 *
	 * @since 0.1.1
	 */
	public function test_default_usage() {

		$output = $this->run_command( 'wp-l10n-validator', '/no-config' );
		$this->assertEquals( 0, strpos( $output, 'Usage:' ) );

		$output = $this->run_command( 'wp-l10n-validator textdomain', '/no-config' );
		$this->assertEquals( "/no-config.php#16: Non gettexted string 'Hello world'", $output );

		$output = $this->run_command( 'wp-l10n-validator textdomain default', '/no-config' );
		$this->assertEquals(
			"/no-config.php#11 \$wpdb->get_results( 1 ): Non gettexted string 'SELECT * FROM'"
			. "\n/no-config.php#16: Non gettexted string 'Hello world'"
			. "\n/no-config.php#21 _e( 1 ): Non gettexted string 'Message'"
			. "\n/no-config.php#21 _e( 2 ): Non gettexted string 'textdomain'"
			, $output
		);
	}

	/**
	 * Test usage with JSON config.
	 *
	 * @since 0.1.1
	 */
	public function test_with_json_config() {

		$output = $this->run_command( 'wp-l10n-validator', '/with-config' );
		$this->assertEquals( "/with-config.php#16: Non gettexted string 'Hello world'", $output );
	}

	/**
	 * Text ignores cache generation.
	 *
	 * @since 0.1.1
	 */
	public function test_ignores_cache_generation() {

		$output = $this->run_command( 'wp-l10n-validator -c', '/with-config' );
		$this->assertEmpty( $output );

		$ignores_cache = dirname( __DIR__ ) . '/data/with-config/wp-l10n-validator-ignores.cache';

		if ( ! ($content = @file_get_contents( $ignores_cache )) )
			$this->fail( 'The ignores cache file was not generated, or could not be read.' );

		unlink( $ignores_cache );

		$this->assertEquals( array( '/with-config.php' => array( 'Hello world' => array( 16 => false ) ) ), json_decode( $content, true ) );
	}

	/**
	 * Run a command and return the output.
	 *
	 * @since 0.1.1
	 *
	 * @param string $command The command to run.
	 * @param string $working_dir The current working directory.
	 *
	 * @return string The result of the command.
	 */
	protected function run_command( $command, $working_dir ) {

		$command = dirname( dirname( __DIR__ ) ) . '/bin/' . $command;
		$working_dir = dirname( __DIR__ ) . '/data' . $working_dir;

		$process = proc_open( $command, array( 2 => array( 'pipe', 'w' ) ), $pipes, $working_dir );

		if ( is_resource( $process ) ) {

			while ( ($proc_status = proc_get_status( $process )) && $proc_status['running'] ) {

				usleep( 10000 );
			}

			$output = stream_get_contents( $pipes[2] );

			fclose( $pipes[2] );
			proc_close( $process );

			@unlink( $working_dir . '/wp-l10n-validator.cache' );

			return trim( $output );
		}

		$this->markTestSkipped( 'Unable to open the process with proc_open()' );
	}
}
