<?php
/**
 * Template Name: Homepage
 */

get_header(); ?>
	<?php dynamic_sidebar('smart-magazine-homepage-header'); ?>
	<div class="col-md-8 col-sm-8 col-main">
		<?php dynamic_sidebar('smart-magazine-homepage-main'); ?>
	</div><!-- col-main -->
	<div class="col-sm-4 col-md-4 sidebar">
	<?php dynamic_sidebar('smart-magazine-homepage-sidebar'); ?>
	</div><!-- sidebar -->
	<div class="clearfix"></div>
	<div id="primary" class="content-area col-sm-12">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry-content">
						<?php the_content(); ?>
			
					</div><!-- .entry-content -->
	
				</article><!-- #post-## -->

			<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->
<div class="clearfix"></div>

<?php get_footer(); ?>
