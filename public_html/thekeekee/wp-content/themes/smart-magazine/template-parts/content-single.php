<?php
/**
 * Template part for displaying single posts.
 *
 * @package Smart Magazine
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="gum_breadcrumb">
		<?php
			$categories_list = get_the_category_list( esc_html__( ', ', 'smart-magazine' ) );
		?>
		<a href="">home </a> <i class="fa fa-angle-double-right"></i> <?php echo $categories_list; ?> <i class="fa fa-angle-double-right"></i> <?php the_title('<span class="bc_title">', '</span>' ); ?>
	</div>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		<div class="entry-meta">
			<?php smart_magazine_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="featured_image">
				<?php the_post_thumbnail( 'large' ); ?> 
			</div><!-- featured_image-->
		<?php endif; ?>
	
		
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'smart-magazine' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php smart_magazine_entry_footer(); ?>
		
		 <?php if ( get_the_author_meta( 'description' ) ) : ?>
	         <div class="author-box">
	            <div class="author-img col-xs-2"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '100' ); ?></div>
	               <div class="col-xs-10">
	               <h4 class="author-name"><?php the_author_meta( 'display_name' ); ?></h4>
	               <p class="author-description"><?php the_author_meta( 'description' ); ?></p>
	               </div><!-- col-xs-8 -->
	               <div class="clearfix"></div>
	         </div>
			 <?php endif; ?>
	</footer><!-- .entry-footer -->
	
	
</article><!-- #post-## -->

