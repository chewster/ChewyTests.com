<?php 
/**
 * @package Featured articles Lite - Wordpress plugin
 * @author CodeFlavors ( codeflavors@codeflavors.com )
 * @version 2.4
 */
?>
<?php if( !isset( $_GET['action'] ) || 'edit' != $_GET['action'] ):?>
<p><?php _e('Make sure that you first save the slider. Once that is done, the code needed to place the slider into your theme files will be displayed here.', 'featured-articles-lite');?></p>
<?php else:?>
<p><?php _e('If you want to display this Slider into your WordPress theme template, simply copy and paste the code below in the theme file where you want it displayed.', 'featured-articles-lite');?></p>
<p style="padding:10px; border:1px #ccc solid; background:#FFFFCC; cursor:text;">
	&lt;?php<br />
    if( function_exists('fa_display_slider') ){<br />
    &nbsp;&nbsp;&nbsp;&nbsp;fa_display_slider( <?php echo $post->ID;?> );<br />
    }<br />
    ?&gt;
</p>
<p class="description">
<?php _e( 'A better way of displaying sliders into specific WordPress theme files is by using Dynamic areas.', 'featured-articles-lite' );?> 
<?php printf( __( 'More details about how to use Dynamic areas can be found %shere%s.', 'featured-articles-lite' ), '<a href="http://www.codeflavors.com/documentation/featured-articles-pro-3/dynamic-areas/?utm_source=plugin&utm_medium=doc_link&utm_campaign=fa_lite" target="_blank">', '</a>' )?>
</p>
<?php endif;?>