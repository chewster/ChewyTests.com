<?php
/**
 * The template for displaying search results pages.
 *
 * @package Smart Magazine
 */

get_header(); ?>

	<section id="primary" class="content-area  col-md-8 col-sm-8 col-main">
		<main id="main" class="site-main" role="main">
			
		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'smart-magazine' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'search' );
				?>

			<?php endwhile; ?>

			<?php
				// Previous/next page navigation.
				the_posts_pagination( array(
					'prev_text'          => __( 'Previous page', 'smart-magazine' ),
					'next_text'          => __( 'Next page', 'smart-magazine' ),
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'smart-magazine' ) . ' </span>',
				) ); 
			?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
