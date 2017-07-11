<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Smart Magazine
 */

get_header(); ?>
    <link href="https://fonts.googleapis.com/css?family=Oleo+Script+Swash+Caps" rel="stylesheet">
    <div class="top-stories slider">
    	<h1>Top Stories</h1>
			<?php
			if( function_exists('fa_display_slider') ){
			    fa_display_slider( 106);
			}
			?>
    </div>
	<div id="primary" class="content-area col-md-8 col-sm-8 col-main">
		<main id="main" class="site-main" role="main">
			<div class="category-posts">
				<h1>Beauty</h1>
				<?php
				    $args = array( 'posts_per_page' => 3, 'offset'=> 1, 'category_name' => 'beauty' );
				    $myposts = get_posts( $args );
				    foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
				    <div>
				    	<?php the_post_thumbnail( array(500, 500)); ?>
				        <h2><?php the_title(); ?></h2>
				        <p><?php the_content(); ?></p>
				        <a class="moretag" href="<?php echo get_permalink($post->ID);?>"> Read More...</a>'
				    </div>
				    <?php endforeach; 
				    wp_reset_postdata();

				?>
			</div>
			<div class="category-posts">
				<h1>News</h1>
				<?php
				    $args = array( 'posts_per_page' => 3, 'offset'=> 1, 'category_name' => 'news' );
				    $myposts = get_posts( $args );
				    foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
				    <div>
				    	<?php the_post_thumbnail( array(500, 500)); ?>
				        <a href=<?php get_permalnk(); ?>><h1><?php the_title(); ?></h1></a>
				        <p><?php the_content(); ?></p>
				    </div>
				    <?php endforeach; 
				    wp_reset_postdata();

				?>
			</div>
			<div class="category-posts">
			<h1>Love</h1>
				<?php
				    $args = array( 'posts_per_page' => 3, 'offset'=> 1, 'category_name' => 'love' );
				    $myposts = get_posts( $args );
				    foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
				    <div>
				    	<?php the_post_thumbnail( array(500, 500)); ?>
				        <h1><?php the_title(); ?></h1>
				        <p><?php the_content(); ?></p>
				    </div>
				    <?php endforeach; 
				    wp_reset_postdata();

				?>
			</div>
			<div class="category-posts">
				<h1>Sex</h1>
				<?php
				    $args = array( 'posts_per_page' => 3, 'offset'=> 1, 'category_name' => 'sex' );
				    $myposts = get_posts( $args );
				    foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
				    <div>
				    	<?php the_post_thumbnail( array(500, 500)); ?>
				        <h1><?php the_title(); ?></h1>
				        <p><?php the_content(); ?></p>
				    </div>
				    <?php endforeach; 
				    wp_reset_postdata();

				?>
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
