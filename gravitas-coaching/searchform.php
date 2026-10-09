<?php
/**
 * Search form.
 *
 * @package Elevora
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label><span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'gravitas-coaching' ); ?></span><input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'gravitas-coaching' ); ?>" value="<?php echo get_search_query(); ?>" name="s"></label>
	<button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'gravitas-coaching' ); ?>"><?php echo elevora_icon( 'search' ); // phpcs:ignore ?></button>
</form>

