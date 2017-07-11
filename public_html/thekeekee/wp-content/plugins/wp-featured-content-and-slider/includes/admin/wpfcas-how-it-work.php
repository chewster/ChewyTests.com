<?php
/**
 * Pro Designs and Plugins Feed
 *
 * @package Album and Image Gallery Plus Lightbox
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Action to add menu
add_action('admin_menu', 'wpfcasm_register_design_page');

/**
 * Register plugin design page in admin menu
 * 
 * @package Album and Image Gallery Plus Lightbox
 * @since 1.0.0
 */
function wpfcasm_register_design_page() {
	add_submenu_page( 'edit.php?post_type='.WPFCAS_POST_TYPE, __('How it works, our plugins and offers', 'wp-featured-content-and-slider'), __('How It Works', 'wp-featured-content-and-slider'), 'manage_options', 'wpfcasm-designs', 'wpfcasm_designs_page' );
}

/**
 * Function to display plugin design HTML
 * 
 * @package Album and Image Gallery Plus Lightbox
 * @since 1.0.0
 */
function wpfcasm_designs_page() {

	$wpos_feed_tabs = wpfcasm_help_tabs();
	$active_tab 	= isset($_GET['tab']) ? $_GET['tab'] : 'how-it-work';
?>
		
	<div class="wrap wpfcasm-wrap">

		<h2 class="nav-tab-wrapper">
			<?php
			foreach ($wpos_feed_tabs as $tab_key => $tab_val) {
				$tab_name	= $tab_val['name'];
				$active_cls = ($tab_key == $active_tab) ? 'nav-tab-active' : '';
				$tab_link 	= add_query_arg( array( 'post_type' => WPFCAS_POST_TYPE, 'page' => 'wpfcasm-designs', 'tab' => $tab_key), admin_url('edit.php') );
			?>

			<a class="nav-tab <?php echo $active_cls; ?>" href="<?php echo $tab_link; ?>"><?php echo $tab_name; ?></a>

			<?php } ?>
		</h2>
		
		<div class="wpfcasm-tab-cnt-wrp">
		<?php
			if( isset($active_tab) && $active_tab == 'how-it-work' ) {
				wpfcasm_howitwork_page();
			}
			else if( isset($active_tab) && $active_tab == 'plugins-feed' ) {
				echo wpfcasm_get_plugin_design( 'plugins-feed' );
			} else {
				echo wpfcasm_get_plugin_design( 'offers-feed' );
			}
		?>
		</div><!-- end .wpfcasm-tab-cnt-wrp -->

	</div><!-- end .wpfcasm-wrap -->

<?php
}

/**
 * Gets the plugin design part feed
 *
 * @package Album and Image Gallery Plus Lightbox
 * @since 1.0.0
 */
function wpfcasm_get_plugin_design( $feed_type = '' ) {
	
	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
	
	// If tab is not set then return
	if( empty($active_tab) ) {
		return false;
	}

	// Taking some variables
	$wpos_feed_tabs = wpfcasm_help_tabs();
	$transient_key 	= isset($wpos_feed_tabs[$active_tab]['transient_key']) 	? $wpos_feed_tabs[$active_tab]['transient_key'] 	: 'wpfcasm_' . $active_tab;
	$url 			= isset($wpos_feed_tabs[$active_tab]['url']) 			? $wpos_feed_tabs[$active_tab]['url'] 				: '';
	$transient_time = isset($wpos_feed_tabs[$active_tab]['transient_time']) ? $wpos_feed_tabs[$active_tab]['transient_time'] 	: 172800;
	$cache 			= get_transient( $transient_key );
	
	if ( false === $cache ) {
		
		$feed 			= wp_remote_get( esc_url_raw( $url ), array( 'timeout' => 120, 'sslverify' => false ) );
		$response_code 	= wp_remote_retrieve_response_code( $feed );
		
		if ( ! is_wp_error( $feed ) && $response_code == 200 ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( $transient_key, $cache, $transient_time );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the data from the server. Please try again later.', 'wp-featured-content-and-slider' ) . '</div>';
		}
	}
	return $cache;	
}

/**
 * Function to get plugin feed tabs
 *
 * @package Album and Image Gallery Plus Lightbox
 * @since 1.0.0
 */
function wpfcasm_help_tabs() {
	$wpos_feed_tabs = array(
						'how-it-work' 	=> array(
													'name' => __('How It Works', 'wp-featured-content-and-slider'),
												),
						'plugins-feed' 	=> array(
													'name' 				=> __('Our Plugins', 'wp-featured-content-and-slider'),
													'url'				=> 'http://wponlinesupport.com/plugin-data-api/plugins-data.php',
													'transient_key'		=> 'wpos_plugins_feed',
													'transient_time'	=> 172800
												),
						'offers-feed' 	=> array(
													'name'				=> __('WPOS Offers', 'wp-featured-content-and-slider'),
													'url'				=> 'http://wponlinesupport.com/plugin-data-api/wpos-offers.php',
													'transient_key'		=> 'wpos_offers_feed',
													'transient_time'	=> 86400,
												)
					);
	return $wpos_feed_tabs;
}

/**
 * Function to get 'How It Works' HTML
 *
 * @package Album and Image Gallery Plus Lightbox
 * @since 1.0.0
 */
function wpfcasm_howitwork_page() { ?>
	
	<style type="text/css">
		.wpos-pro-box .hndle{background-color:#0073AA; color:#fff;}
		.wpos-pro-box .postbox{background:#dbf0fa none repeat scroll 0 0; border:1px solid #0073aa; color:#191e23;}
		.postbox-container .wpos-list li:before{font-family: dashicons; content: "\f139"; font-size:20px; color: #0073aa; vertical-align: middle;}
		.wpfcasm-wrap .wpos-button-full{display:block; text-align:center; box-shadow:none; border-radius:0;}
		.wpfcasm-shortcode-preview{background-color: #e7e7e7; font-weight: bold; padding: 2px 5px; display: inline-block; margin:0 0 2px 0;}
	</style>

	<div class="post-box-container">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
			
				<!--How it workd HTML -->
				<div id="post-body-content">
					<div class="metabox-holder">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								
								<h3 class="hndle">
									<span><?php _e( 'How It Works - Display and shortcode', 'wp-featured-content-and-slider' ); ?></span>
								</h3>
								
								<div class="inside">
									<table class="form-table">
										<tbody>
											<tr>
												<th>
													<label><?php _e('Geeting Started with Featured Content', 'wp-featured-content-and-slider'); ?>:</label>
												</th>
												<td>
													<ul>
														<li><?php _e('Step-1. Go to "Featured Content --> Add conetnt tab".', 'wp-featured-content-and-slider'); ?></li>
														<li><?php _e('Step-2. Add Title, description and image as a featured image OR Add Font Awesome (If not adding Featured Image).', 'wp-featured-content-and-slider'); ?></li>
														<li><?php _e('Step-3. To display multiple, Create category under "Featured Content -->category" and add Featured Content to respective categories.', 'wp-featured-content-and-slider'); ?></li>
														<li><?php _e('Step-4. Use category shortcode under "Featured Content -->category"', 'wp-featured-content-and-slider'); ?></li>
														<li><?php _e('Step-5. Plugin alos work with custom post type', 'wp-featured-content-and-slider'); ?></li>
													</ul>
												</td>
											</tr>

											<tr>
												<th>
													<label><?php _e('How Shortcode Works', 'wp-featured-content-and-slider'); ?>:</label>
												</th>
												<td>
													<ul>
														<li><?php _e('Step-1. Create a page like Featured Content OR add the shortcode in a page.', 'wp-featured-content-and-slider'); ?></li>
														<li><?php _e('Step-2. Put below shortcode as per your need.', 'wp-featured-content-and-slider'); ?></li>
													</ul>
												</td>
											</tr>

											<tr>
												<th>
													<label><?php _e('All Shortcodes', 'wp-featured-content-and-slider'); ?>:</label>
												</th>
												<td>
													<span class="wpfcasm-shortcode-preview">[featured-content]</span> – <?php _e('Featured Content Grid Shortcode', 'wp-featured-content-and-slider'); ?> <br />
													<span class="wpfcasm-shortcode-preview">[featured-content-slider]</span> – <?php _e('Featured Content Slider Shortcode', 'wp-featured-content-and-slider'); ?> <br />
													<span class="wpfcasm-shortcode-preview">[featured-content grid="2" design="design-1"]</span> – <?php _e('design and grid parameters', 'wp-featured-content-and-slider'); ?> <br />
													<span class="wpfcasm-shortcode-preview">[featured-content post_type="featured_post"]  and [featured-content-slider post_type="featured_post"]</span> – <?php _e('Shortcode also work with custom post type', 'wp-featured-content-and-slider'); ?> 
													
												</td>
											</tr>						
												
											<tr>
												<th>
													<label><?php _e('Need Support?', 'wp-featured-content-and-slider'); ?></label>
												</th>
												<td>
													<p><?php _e('Check plugin document for shortcode parameters and demo for designs.', 'wp-featured-content-and-slider'); ?></p> <br/>
													<a class="button button-primary" href="http://www.wponlinesupport.com/plugins-documentation/document-wp-featured-content-and-slider/?utm_source=hp&event=doc" target="_blank"><?php _e('Documentation', 'wp-featured-content-and-slider'); ?></a>									
													<a class="button button-primary" href="http://demo.wponlinesupport.com/featured-content-and-slider-demo/?utm_source=hp&event=demo" target="_blank"><?php _e('Demo for Designs', 'wp-featured-content-and-slider'); ?></a>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!-- .inside -->
							</div><!-- #general -->
						</div><!-- .meta-box-sortables ui-sortable -->
					</div><!-- .metabox-holder -->
				</div><!-- #post-body-content -->
				
				<!--Upgrad to Pro HTML -->
				<div id="postbox-container-1" class="postbox-container">
					<div class="metabox-holder wpos-pro-box">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox" style="">
									
								<h3 class="hndle">
									<span><?php _e( 'Upgrate to Pro', 'wp-featured-content-and-slider' ); ?></span>
								</h3>
								<div class="inside">										
									<ul class="wpos-list">
										<li>35+ Designs</li>
										<li>Display category wise</li>
										<li>3 Shortcodes</li>
										<li>Visual Composer Support</li>
										<li>Drag & Drop order change</li>									
										<li>Custom read more button and link</li>
										<li>Custom css</li>
										<li>Slider RTL support</li>
										<li>Fully responsive</li>
										<li>100% Multi language</li>
									</ul>
									<a class="button button-primary wpos-button-full" href="https://www.wponlinesupport.com/wp-plugin/wp-featured-content-and-slider/?utm_source=hp&event=go_premium" target="_blank"><?php _e('Go Premium ', 'wp-featured-content-and-slider'); ?></a>	
									<p><a class="button button-primary wpos-button-full" href="http://demo.wponlinesupport.com/prodemo/pro-wp-featured-content-and-slider/?utm_source=hp&event=pro_demo" target="_blank"><?php _e('View PRO Demo ', 'wp-featured-content-and-slider'); ?></a>			</p>								
								</div><!-- .inside -->
							</div><!-- #general -->
						</div><!-- .meta-box-sortables ui-sortable -->
					</div><!-- .metabox-holder -->

					<!-- Help to improve this plugin! -->
					<div class="metabox-holder">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
									<h3 class="hndle">
										<span><?php _e( 'Help to improve this plugin!', 'wp-featured-content-and-slider' ); ?></span>
									</h3>									
									<div class="inside">										
										<p>Enjoyed this plugin? You can help by rate this plugin <a href="https://wordpress.org/support/plugin/wp-featured-content-and-slider/reviews/?filter=5" target="_blank">5 stars!</a></p>
									</div><!-- .inside -->
							</div><!-- #general -->
						</div><!-- .meta-box-sortables ui-sortable -->
					</div><!-- .metabox-holder -->
				</div><!-- #post-container-1 -->

			</div><!-- #post-body -->
		</div><!-- #poststuff -->
	</div><!-- #post-box-container -->
<?php }