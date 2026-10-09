<?php
/**
 * Trainer directory.
 *
 * @package Elevora
 */

get_header();

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return;
}

function elevora_directory_select( $taxonomy, $name, $label ) {
	$current = isset( $_GET[ $name ] ) ? sanitize_title( wp_unslash( $_GET[ $name ] ) ) : '';
	$terms   = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}
	echo '<label><span>' . esc_html( $label ) . '</span><select name="' . esc_attr( $name ) . '"><option value="">' . esc_html__( 'Any', 'gravitas-coaching' ) . '</option>';
	foreach ( $terms as $term ) {
		echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $current, $term->slug, false ) . '>' . esc_html( $term->name ) . '</option>';
	}
	echo '</select></label>';
}
?>
<main id="primary" class="site-main directory-main">
	<section class="archive-hero archive-hero--trainers">
		<div class="container">
			<?php elevora_breadcrumbs(); ?>
			<div class="archive-hero__content"><span class="eyebrow eyebrow--light"><?php esc_html_e( 'Consultant directory', 'gravitas-coaching' ); ?></span><h1><?php esc_html_e( 'Find the expert for your professional presence.', 'gravitas-coaching' ); ?></h1><p><?php esc_html_e( 'Search soft-skills coaches and image consultants by expertise, location, language, and format.', 'gravitas-coaching' ); ?></p></div>
		</div>
	</section>
	<section class="directory-section">
		<div class="container">
			<form id="trainer-directory-form" class="directory-filters" action="<?php echo esc_url( get_post_type_archive_link( 'trainer' ) ); ?>" method="get" data-directory-form>
				<div class="directory-search"><label for="trainer-search"><?php esc_html_e( 'Search', 'gravitas-coaching' ); ?></label><div class="input-with-icon"><?php echo elevora_icon( 'search' ); // phpcs:ignore ?><input id="trainer-search" type="search" name="trainer_search" value="<?php echo isset( $_GET['trainer_search'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['trainer_search'] ) ) ) : ''; ?>" placeholder="<?php esc_attr_e( 'Name, goal, or keyword', 'gravitas-coaching' ); ?>"></div></div>
				<?php elevora_directory_select( 'trainer_specialization', 'specialization', __( 'Specialization', 'gravitas-coaching' ) ); ?>
				<?php elevora_directory_select( 'trainer_location', 'location', __( 'Location', 'gravitas-coaching' ) ); ?>
				<?php elevora_directory_select( 'coaching_format', 'format', __( 'Format', 'gravitas-coaching' ) ); ?>
				<?php elevora_directory_select( 'trainer_language', 'language', __( 'Language', 'gravitas-coaching' ) ); ?>
				<div class="filter-actions"><button class="button button--dark" type="submit"><?php esc_html_e( 'Apply filters', 'gravitas-coaching' ); ?></button><a href="<?php echo esc_url( get_post_type_archive_link( 'trainer' ) ); ?>" data-clear-filters><?php esc_html_e( 'Clear', 'gravitas-coaching' ); ?></a></div>
			</form>
			<div class="directory-toolbar">
					<p><strong data-result-count><?php echo esc_html( $wp_query->found_posts ); ?></strong> <?php esc_html_e( 'consultants available', 'gravitas-coaching' ); ?></p>
				<div class="directory-toolbar__actions"><label><?php esc_html_e( 'Sort by', 'gravitas-coaching' ); ?><select name="sort" form="trainer-directory-form" data-directory-sort><option value="recommended"><?php esc_html_e( 'Recommended', 'gravitas-coaching' ); ?></option><option value="rating" <?php selected( isset( $_GET['sort'] ) ? $_GET['sort'] : '', 'rating' ); ?>><?php esc_html_e( 'Highest rated', 'gravitas-coaching' ); ?></option><option value="experience" <?php selected( isset( $_GET['sort'] ) ? $_GET['sort'] : '', 'experience' ); ?>><?php esc_html_e( 'Most experienced', 'gravitas-coaching' ); ?></option><option value="price-low" <?php selected( isset( $_GET['sort'] ) ? $_GET['sort'] : '', 'price-low' ); ?>><?php esc_html_e( 'Price: low to high', 'gravitas-coaching' ); ?></option><option value="newest" <?php selected( isset( $_GET['sort'] ) ? $_GET['sort'] : '', 'newest' ); ?>><?php esc_html_e( 'Newest', 'gravitas-coaching' ); ?></option></select></label><div class="view-toggles"><button type="button" class="is-active" data-view="grid" aria-label="<?php esc_attr_e( 'Grid view', 'gravitas-coaching' ); ?>"><?php echo elevora_icon( 'grid' ); // phpcs:ignore ?></button><button type="button" data-view="list" aria-label="<?php esc_attr_e( 'List view', 'gravitas-coaching' ); ?>"><?php echo elevora_icon( 'list' ); // phpcs:ignore ?></button></div></div>
			</div>
			<div class="directory-results" data-directory-results aria-live="polite">
				<div class="directory-loader" data-directory-loader hidden><span></span><?php esc_html_e( 'Finding coaches…', 'gravitas-coaching' ); ?></div>
				<div class="card-grid card-grid--three trainer-results" data-directory-cards>
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); get_template_part( 'template-parts/card', 'trainer' ); endwhile; else : get_template_part( 'template-parts/content', 'none', array( 'message' => __( 'No trainers match those filters yet. Try widening your search.', 'gravitas-coaching' ) ) ); endif; ?>
				</div>
				<div data-directory-pagination><?php elevora_pagination(); ?></div>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
