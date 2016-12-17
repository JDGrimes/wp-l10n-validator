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
 *
 * @group cli
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
		$this->assertEquals( 1, $this->exit_code );

		$output = $this->run_command( 'wp-l10n-validator textdomain', '/no-config' );
		$this->assertEquals( "/no-config.php#16: Non gettexted string 'Hello world'", $output );
		$this->assertEquals( 1, $this->exit_code );

		$output = $this->run_command( 'wp-l10n-validator textdomain default', '/no-config' );
		$this->assertEquals(
			"/no-config.php#9 apply_filters( 1 ): Non gettexted string 'some-filter'"
			. "\n/no-config.php#16: Non gettexted string 'Hello world'"
			. "\n/no-config.php#21 _e( 1 ): Non gettexted string 'Message'"
			. "\n/no-config.php#21 _e( 2 ): Non gettexted string 'textdomain'"
			, $output
		);
		$this->assertEquals( 1, $this->exit_code );
	}

	/**
	 * Test usage with JSON config.
	 *
	 * @since 0.1.1
	 */
	public function test_with_json_config() {

		$output = $this->run_command( 'wp-l10n-validator', '/with-config' );
		$this->assertEquals( "/with-config.php#16: Non gettexted string 'Hello world'", $output );
		$this->assertEquals( 1, $this->exit_code );
	}

	/**
	 * Text ignores cache generation.
	 *
	 * @since 0.1.1
	 */
	public function test_ignores_cache_generation() {

		$output = $this->run_command( 'wp-l10n-validator -c', '/with-config' );
		$this->assertEmpty( $output );
		$this->assertEquals( 0, $this->exit_code );

		$ignores_cache = dirname( __DIR__ ) . '/data/with-config/wp-l10n-validator-ignores.cache';

		if ( ! ($content = @file_get_contents( $ignores_cache )) )
			$this->fail( 'The ignores cache file was not generated, or could not be read.' );

		unlink( $ignores_cache );

		$this->assertEquals( array( '/with-config.php' => array( 'Hello world' => array( 16 => false ) ) ), json_decode( $content, true ) );
		$this->assertEquals( 0, $this->exit_code );
	}

	/**
	 * Test passing a list of files to check via the command line.
	 *
	 * @since 0.4.0
	 */
	public function test_files_passed() {

		$output = $this->run_command( 'wp-l10n-validator -- other.php', '/no-config' );
		$this->assertEquals( 0, strpos( $output, 'Usage:' ) );
		$this->assertEquals( 1, $this->exit_code );

		$output = $this->run_command( 'wp-l10n-validator textdomain -- other.php', '/no-config' );
		$this->assertEquals( '', $output );
		$this->assertEquals( 0, $this->exit_code );

		// With a dot before the file name.
		$output = $this->run_command( 'wp-l10n-validator textdomain -- ./no-config.php', '/no-config' );
		$this->assertEquals( "/no-config.php#16: Non gettexted string 'Hello world'", $output );
		$this->assertEquals( 1, $this->exit_code );

		// Multiple files.
		$output = $this->run_command( 'wp-l10n-validator textdomain -- other.php no-config.php', '/no-config' );
		$this->assertEquals( "/no-config.php#16: Non gettexted string 'Hello world'", $output );
		$this->assertEquals( 1, $this->exit_code );

		// Passing an ignored file.
		$output = $this->run_command( 'wp-l10n-validator textdomain -- ignored.php with-config.php', '/with-config' );
		$this->assertEquals( "/with-config.php#16: Non gettexted string 'Hello world'", $output );
		$this->assertEquals( 1, $this->exit_code );
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

		$spec = array( 2 => array( 'pipe', 'w' ), 3 => array( 'pipe', 'w' ) );
		$process = proc_open( $command, $spec, $pipes, $working_dir );

		if ( is_resource( $process ) ) {

			while ( ($proc_status = proc_get_status( $process )) && $proc_status['running'] ) {

				usleep( 10000 );
			}

			$this->exit_code = $proc_status['exitcode'];

			$output = stream_get_contents( $pipes[2] );

			fclose( $pipes[2] );
			fclose( $pipes[3] );
			proc_close( $process );

			@unlink( $working_dir . '/wp-l10n-validator.cache' );

			return trim( $output );
		}

		$this->markTestSkipped( 'Unable to open the process with proc_open()' );
	}
}
