<?php
/**
 * Template part for displaying top stories.
 *
 * @package 
 */

?>

<article id="post-<?php the_ID(); ?>"  class="col-sm-4 col-xs-6">
	<div class="featured_image">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( array(1000, 1000)); ?>
		<?php endif; ?>
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
	</div>
	<div class="clearfix"></div>
</article><!-- #post-## -->
