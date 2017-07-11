<?php
/**
 * Template part for displaying posts.
 *
 * @package Smart Magazine
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta"> 
			<?php smart_magazine_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
		
	</header><!-- .entry-header -->
	<div class="featured_image col-sm-4 col-xs-6">
			<?php if ( has_post_thumbnail() ) : ?>
				
					<?php the_post_thumbnail( 'medium' ); ?> 
				
			<?php endif; ?>
	</div><!-- featured_image-->
	<div class="excerpt col-sm-8  col-xs-6">
		<?php
			/* translators: %s: Name of current post */
			the_excerpt(  );
		?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'smart-magazine' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
	<div class="clearfix"></div>
	
</article><!-- #post-## -->
