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
    <!-- container -->
	<div class="clearfix"></div>
	<div class="container">
	    <div class="top">
	    </div>
		<div class="logo">
            The KeeKee
		</div>
		<div class="bottom">
		</div>
	</div><!-- container -->
   </header>
  <div class="nav_wrapper clearfix">
		<div class="container">
		<!--
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

   							   			
   			</nav>-->
		</div><!--container -->
	</div><!--nav_wrapper -->
 	<div class="container content_wrapper" id="content_wrapper">
		   		<div class="content_border">