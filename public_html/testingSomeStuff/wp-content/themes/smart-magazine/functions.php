<?php
/**
 * Smart Magazine functions and definitions
 *
 * @package Smart Magazine
 */


if (!function_exists('smart_magazine_setup')): /**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
    function smart_magazine_setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Smart Magazine, use a find and replace
         * to change 'smart-magazine' to the name of your theme in all the template files
         */
        load_theme_textdomain('smart-magazine', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
         */
        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(array(
            'primary' => esc_html__('Primary Menu', 'smart-magazine'),
            'top' => esc_html__('Top Menu', 'smart-magazine'),
            'footer' => esc_html__('Footer Menu', 'smart-magazine')
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption'
        ));

        // Set up the WordPress core custom background feature.
        add_theme_support('custom-background', apply_filters('smart_magazine_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => ''
        )));
    }
endif; // smart_magazine_setup

add_action('after_setup_theme', 'smart_magazine_setup');

function smart_magazine_new_excerpt_more($more)
{
    return ' ...';
}
add_filter('excerpt_more', 'smart_magazine_new_excerpt_more');

function smart_magazine_pagination($pages = '', $range = 2)
{
    $showitems = ($range * 2) + 1;

    global $paged;
    if (empty($paged))
        $paged = 1;

    if ($pages == '') {
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if (!$pages) {
            $pages = 1;
        }
    }

    if (1 != $pages) {
        echo "<nav><ul class='pagination pagination-sm'>";
        if ($paged > 2 && $paged > $range + 1 && $showitems < $pages)
            echo "<li><a href='" . get_pagenum_link(1) . "'>&laquo;</a></li>";
        if ($paged > 1 && $showitems < $pages)
            echo "<li><a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a></li>";

        for ($i = 1; $i <= $pages; $i++) {
            if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                echo ($paged == $i) ? "<li class='active'><span class='current'>" . $i . "</span><li>" : "<li><a href='" . get_pagenum_link($i) . "' class='inactive' >" . $i . "</a></li>";
            }
        }

        if ($paged < $pages && $showitems < $pages)
            echo "<li><a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a></li>";
        if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages)
            echo "<li><a href='" . get_pagenum_link($pages) . "'>&raquo;</a></li>";
        echo "</ul></nav>\n";
    }
}


/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function smart_magazine_content_width()
{
    $GLOBALS['content_width'] = apply_filters('smart_magazine_content_width', 640);
}
add_action('after_setup_theme', 'smart_magazine_content_width', 0);

if (!isset($content_width)) {
    $content_width = 600;
}

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function smart_magazine_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Sidebar', 'smart-magazine'),
        'id' => 'smart-magazine-sidebar-1',
        'description' => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));

    register_sidebar(array(
        'name' => esc_html__('Homepage Header', 'smart-magazine'),
        'id' => 'smart-magazine-homepage-header',
        'description' => '',
        'before_widget' => '<div class="homepage_header" id="">',
        'after_widget' => '</div>',
        'before_title' => '<h1 class="widget-title">',
        'after_title' => '</h1>'
    ));

    register_sidebar(array(
        'name' => esc_html__('Homepage Main', 'smart-magazine'),
        'id' => 'smart-magazine-homepage-main',
        'description' => '',
        'before_widget' => '<div class="homepage_main home_main_widget" id="">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));
    register_sidebar(array(
        'name' => esc_html__('Homepage Sidebar', 'smart-magazine'),
        'id' => 'smart-magazine-homepage-sidebar',
        'description' => '',
        'before_widget' => '<div class="widget gum_widget gum_home_sidebar" id="">',
        'after_widget' => '</div>',
        'before_title' => '<div class="gum_post_grid_header"> <div class="grid_heading"><h3 class="widget-title">',
        'after_title' => '</h3></div></div>'
    ));

    /**** Footer Widgets *****/
    register_sidebar(array(
        'name' => esc_html__('Footer 1', 'smart-magazine'),
        'id' => 'smart-magazine-footer-1',
        'description' => '',
        'before_widget' => '<div class="widget gum_footer_widget" id="">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-widget-title">',
        'after_title' => '</h3>'
    ));
    register_sidebar(array(
        'name' => esc_html__('Footer 2', 'smart-magazine'),
        'id' => 'smart-magazine-footer-2',
        'description' => '',
        'before_widget' => '<div class="widget gum_footer_widget" id="">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-widget-title">',
        'after_title' => '</h3>'
    ));
    register_sidebar(array(
        'name' => esc_html__('Footer 3', 'smart-magazine'),
        'id' => 'smart-magazine-footer-3',
        'description' => '',
        'before_widget' => '<div class="widget gum_footer_widget" id="">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-widget-title">',
        'after_title' => '</h3>'
    ));


}
add_action('widgets_init', 'smart_magazine_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function smart_magazine_scripts()
{

    wp_enqueue_style('smart-magazine-google-font', '//fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400,700');
    wp_enqueue_style('smart-magazine-bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css');
    wp_enqueue_style('smart-magazine-font-awesome', get_template_directory_uri() . '/assets/font-awesome/css/font-awesome.min.css');
    wp_enqueue_style('smart-magazine-superfish', get_template_directory_uri() . '/assets/js/superfish/css/superfish.css');
    wp_enqueue_style('smart-magazine-style', get_stylesheet_uri()."?14");

    wp_enqueue_script('smart-magazine-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true);

    wp_enqueue_script('smart-magazine-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true);

    wp_enqueue_script('smart-magazine-modernizr', get_template_directory_uri() . '/assets/js/modernizr.custom.js', array( 'jquery'), '20150615');

    wp_enqueue_script('smart-magazine-easing', get_template_directory_uri() . '/assets/js/jquery.easing.js', array( 'jquery'), '20150615');

    wp_enqueue_script('smart-magazine-hoverIntent', get_template_directory_uri() . '/assets/js/jquery.hoverIntent.js', array(), '20150615');

    wp_enqueue_script('smart-magazine-superfish', get_template_directory_uri() . '/assets/js/superfish/js/superfish.js', array(
        'jquery'
    ), '20150615');

    wp_enqueue_script('smart-magazine-script', get_template_directory_uri() . '/assets/js/script.js', array(
        'jquery'
    ), '20150615');


    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'smart_magazine_scripts');

function smart_magazine_add_editor_styles()
{
    add_editor_style(get_template_directory_uri() . '/assets/css/custom-editor-style.css');
}
add_action('admin_init', 'smart_magazine_add_editor_styles');


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load custom widgets.
 */
require get_template_directory() . '/inc/smart_magazine_widgets.php';

