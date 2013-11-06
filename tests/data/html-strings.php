<?php

/**
 * Test that all untranslated HTML strings are caught.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

?>
<style type="text/css">
p {
	color: #000;
}
</style>
<h1>Catch me!</h2>
<p><?php echo 'bob'; ?></p>
<p class="ignoreme"></p>
<?php do_stuff(); ?>
<script>
$( 'a' ).addClass( 'link' );
</script>
<a href="http://example.com/" title="catch me"><?php _e( 'link', 'wp-l10n-validator-tests' ); ?></a>
<a href="<?php echo $url; ?>" id="<?php echo $id; ?>"></a>