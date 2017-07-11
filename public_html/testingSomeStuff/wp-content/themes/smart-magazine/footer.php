<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Smart Magazine
 */

?>

	</div><!-- content_border-->
</div><!-- content_wrapper-->


<footer class="site-footer-wrapper container" role="contentinfo">
	<div class="site-footer col-sm-12">
		<div class="col-sm-4">
			<?php dynamic_sidebar('smart-magazine-footer-1'); ?>
		</div><!-- col-main -->
		<div class="col-sm-4">
			<?php dynamic_sidebar('smart-magazine-footer-2'); ?>
		</div><!-- col-main -->
		<div class="col-sm-4">
			<?php dynamic_sidebar('smart-magazine-footer-3'); ?>
		</div><!-- col-main -->
		
		<div class="clearfix"></div>
			
			
	</div><!-- site-footer -->
	
	<div class="clearfix"></div>
	<div class="col-sm-12 copyright">
		<div class="col-sm-4"><?php echo  esc_attr(get_theme_mod("copyright_textbox")); ?></div>
		<div class="col-sm-4 pull-right themeby">	<?php _e( 'Theme by', 'smart-magazine' ); ?>
		
		<a href="<?php echo esc_url( __('http://mag-themes.com/', 'smart-magazine'));?>" target="_blank" rel="designer" >mag-themes</a></div>
		<div class="clearfix"></div>
	</div>
</footer><!-- .site-footer-wrapper -->

<?php wp_footer(); ?>

</body>
</html>
