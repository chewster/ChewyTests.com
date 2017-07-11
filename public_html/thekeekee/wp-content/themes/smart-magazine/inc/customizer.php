<?php
/**
 * Smart Magazine Theme Customizer
 *
 * @package Smart Magazine
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */

function smart_magazine_customize_preview_js() {
	wp_enqueue_script( 'smart-magazine_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'smart_magazine_customize_preview_js' );



/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
function smart_magazine_customizer( $wp_customize ) {
    $wp_customize->add_section(
        'gum_theme',
        array(
            'title' => __( 'Theme Options', 'smart-magazine' ),
            'description' => __( 'This is a settings section.', 'smart-magazine' ),
            'priority' => 35,
            'capability' => 'edit_theme_options',
        )
    );
    
	$wp_customize->add_setting(
    'theme_color_setting',
    array(
        	'default' => '#A9C131',
        	'sanitize_callback' => 'sanitize_hex_color',
			'capability' => 'edit_theme_options',
		 )
	);
	
	$wp_customize->add_control(
	    new WP_Customize_Color_Control(
	        $wp_customize,
	        'theme_color_setting',
	        array(
	            'label' => __( 'Theme Color Scheme', 'smart-magazine' ),
	            'section' => 'colors',
	            'settings' =>'theme_color_setting',
	        )
	    )
	);
	
	$wp_customize->add_setting(
    'theme_cat_color',
    array(
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability' => 'edit_theme_options',
    )
	);
	
	$wp_customize->add_control(
	    new WP_Customize_Color_Control(
	        $wp_customize,
	        'theme_cat_color',
	        array(
	            'label' => __('Category text', 'smart-magazine' ),
	            'section' => 'colors',
	            'settings' => 'theme_cat_color',
	        )
	    )
	);


	$wp_customize->add_setting( 'logo-upload' ,
	array(
		'sanitize_callback' == 'esc_url_raw',
		 'capability' => 'edit_theme_options',
	),
	array(
        'default' => '',
        
       
    ) );
 
	$wp_customize->add_control(
	    new WP_Customize_Image_Control(
	        $wp_customize,
	        'logo-upload',
	        array(
	            'label' => __('Logo Upload', 'smart-magazine' ),
	            'section' => 'gum_theme',
	            'settings' => 'logo-upload'
	        )
	    )
	);
	
	/**********
	Social Media links 
	
	**********/

	$wp_customize->add_setting(
    'facebook_link',
    array(
		'sanitize_callback' == 'esc_url_raw',
		 'capability' => 'edit_theme_options',
	),
    array(
    
        'default' => '',
         
        
    )
	);
	
	$wp_customize->add_control(
	    'facebook_link',
	    array(
	        'label' => __('Facebook Link', 'smart-magazine' ),
	        'section' => 'gum_theme',
	        'type' => 'text',
	    )
	);
	
	$wp_customize->add_setting(
   		'twitter_link',
	    array(
			'sanitize_callback' == 'esc_url_raw',
			 'capability' => 'edit_theme_options',
		),
	    array(
	        'default' => '',
	    )
	);
	$wp_customize->add_control(
	    'twitter_link',
	    array(
	        'label' => __('Twitter Link', 'smart-magazine' ),
	        'section' => 'gum_theme',
	        'type' => 'text',
	    )
	);
	$wp_customize->add_setting(
    	'googleplus_link',
	 	array(
		'sanitize_callback' == 'esc_url_raw',
		 'capability' => 'edit_theme_options',
		),
	    array(
	        'default' => '',
	    )
	);
	$wp_customize->add_control(
	    'googleplus_link',
	    array(
	        'label' => __('Google Plus Link', 'smart-magazine' ),
	        'section' => 'gum_theme',
	        'type' => 'text',
	    )
	);
	$wp_customize->add_setting(
	    'youtube_link',
	     array(
			'sanitize_callback' == 'esc_url_raw',
			 'capability' => 'edit_theme_options',
		),
	    array(
	        'default' => '',
	    )
	);
	$wp_customize->add_control(
	    'youtube_link',
	    array(
	        'label' => __('Youtube Link', 'smart-magazine' ),
	        'section' => 'gum_theme',
	        'type' => 'text',
	    )
	);
	
	$wp_customize->add_setting(
    	'linkedin_link',
	     array(
			'sanitize_callback' == 'esc_url_raw',
			 'capability' => 'edit_theme_options',
		),
	    array(
	        'default' => '',
	    )
	);
	
	$wp_customize->add_control(
	    'linkedin_link',
	    array(
	        'label' => __('Linkedin Link', 'smart-magazine' ),
	        'section' => 'gum_theme',
	        'type' => 'text',
	    )
	);

	/****
	Footer Copyright
	****/
	$wp_customize->add_setting(
    'copyright_textbox',
    array( 'sanitize_callback' == 'sanitize_text_field',
      	'capability' => 'edit_theme_options',
      	),
    array(
        'default' => '&copy; 2015',
    )
	);
	
	$wp_customize->add_control(
	    'copyright_textbox',
	    array(
	        'label' => __('Copyright text', 'smart-magazine' ),
	        'section' => 'gum_theme',
	        'type' => 'text',
	    )
	);
	
	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	
	$wp_customize->remove_control('header_textcolor');
	
	$wp_customize->remove_section('header_image');
}


add_action( 'customize_register', 'smart_magazine_customizer' );


add_action( 'wp_head', 'smart_magazine_styles' );
function smart_magazine_styles() {
	$theme_color = esc_attr(get_theme_mod("theme_color_setting"));
	$theme_cat_color = esc_attr(get_theme_mod("theme_cat_color"));
	?>
  <style type="text/css">
	  .main_nav, .main_nav .sf-menu .sub-menu{    border-top: 5px solid <?php echo $theme_color;?>;}
	  .main_nav .sf-menu .sub-menu:before{
		      border-bottom-color: <?php echo $theme_color;?>;
	  }
	  .byline .cat-links a, .gum_post_data ul li.cat, .gum_post_block_meta ul li.cat, .gum_post_block_meta ul li.cat{
		  background: <?php echo $theme_color;?>;
		   color: <?php echo $theme_cat_color;?>;
	  }
	  .gum_post_data ul li.cat a, .gum_post_block_meta ul li.cat a, .gum_post_block_meta ul li.cat a{
		 		   color: <?php echo $theme_cat_color;?>;
	  }
 </style>
  
<?php
}