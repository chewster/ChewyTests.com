<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Smart Magazine
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <header class="main-header">
  	
    <div class="container">
	    <div class="top_bar">
       <nav class="top_nav col-sm-8">
	       <div class="topdate"><?php echo date('l, jS F Y'); ?>
</div>
       <?php 
			 	$arg = array(
				'theme_location'  => 'top',
				'container_class' => 'menu-top',
				'container_id'    => '',
				'menu_class'      => 'sf-menu',
				'menu_id'         => 'top-menu',
			);
			
			wp_nav_menu( $arg ); 
		?>


       </nav>
       <nav class="social col-sm-4">
          <ul>
          	<?php
			$facebook_link = esc_url(get_theme_mod("facebook_link"));
			$twitter_link = esc_url(get_theme_mod("twitter_link"));
			$g_link = esc_url(get_theme_mod("googleplus_link"));
			$youtube_link = esc_url(get_theme_mod("youtube_link"));
			$linkedin_link = esc_url(get_theme_mod("linkedin_link"));
			
			
             if(strlen($facebook_link) > 0) echo '<li class="facebook"><a href="'.$facebook_link.'"><i class="fa fa-facebook-square"></i></a></li>';
             if(strlen($twitter_link) > 0) echo '<li class="twitter"><a href="'.$twitter_link.'"><i class="fa fa-twitter-square"></i></a></li>';
             if(strlen($g_link) > 0) echo '<li class="gplus"><a href="'.$g_link.'"><i class="fa fa-google-plus-square"></i></a></li>';
             if(strlen($youtube_link) > 0) echo '<li class="youtube"><a href="'.$youtube_link.'"><i class="fa fa-youtube-square"></i></a></li>';
             if(strlen($linkedin_link) > 0) echo '<li class="linkedin"><a href="'.$linkedin_link.'"><i class="fa fa-linkedin-square"></i></a></li>';
            
             
             ?>
          </ul>
       </nav>
       <div class="clearfix"></div>
       </div>
    <!-- top_bar -->
    </div>
    <!-- container -->
	<div class="clearfix"></div>
	<div class="container">
		<div class="logo">
			<?php
			$logo = esc_url(get_theme_mod("logo-upload"));
			
			?>
			<a href="<?php echo  esc_url( home_url( '/' ) ); ?>">
			<?php echo (strlen($logo) > 0)?  '<img src="'.$logo.'" alt="" />' :  get_bloginfo('name'); ?>
			</a>
		</div>
	</div><!-- container -->
   </header>
  <div class="nav_wrapper clearfix">
		<div class="container">
   			<nav class="main_nav">
            <a id="menu-icon" class="visible-xs mob_menu" href="#"><i class="fa fa-bars"></i></a>
   				 <?php 
   				 	$arg = array(
						'theme_location'  => 'primary',
						
						'container_class' => 'menu-top-container',
						'container_id'    => '',
						'menu_class'      => 'sf-menu',
						'menu_id'         => 'primary-menu',
					);
					
					wp_nav_menu( $arg ); 
				?>

   							   			
   			</nav>
		</div><!--container -->
	</div><!--nav_wrapper -->
 	<div class="container content_wrapper" id="content_wrapper">
		   		<div class="content_border">