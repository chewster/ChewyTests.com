# A Featured Page Widget #
*A WordPress widget to feature a page and display its excerpt and post thumbnail.*  


**Official WordPress plugin directory**: http://wordpress.org/plugins/a-featured-page-widget/  
**Contributors:** eduardozulian  
**Tags:** widget, sidebar, page widget, featured page, pages  
**Requires at least:** 3.0  
**Tested up to:** 4.3  
**Stable tag:** 1.3  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

## Description ##

This plugin creates a widget that features a specific page, showing its excerpt. You can also choose a post thumbnail among the registered ones in your theme.

### Languages ###

* Portuguese (Brazil)

### Contributing ###
[A Featured Page Widget](https://github.com/eduardozulian/a-featured-page-widget) is available on GitHub. If you want to contribute, please fork it and send a pull request!

## Installation ##

1. Upload `a-featured-page-widget` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Widgets' menu and drag it to your sidebar

## Frequently Asked Questions ##

### Can I use it outside my sidebar / widget area?

Sure you can! Just call [`the_widget()`](http://codex.wordpress.org/Function_Reference/the_widget) wherever you want to display your featured page. If you want to customize it, there's four arguments:

* `page`: The page ID. *Required*.
* `title`: The widget title.
* `image-size`: The post thumbnail size. 
* `link-text`: The text that will replace "Continue reading". You may leave it empty too.

Example:
```
<?php
the_widget( 'A_Featured_Page_Widget', array( 'page' => 734, 'link-text' => '', 'image-size' => 'large' ) );
?>
```

If you don't know the page ID, you can try [`get_page_by_path()`](http://codex.wordpress.org/Function_Reference/get_page_by_path) function:
```
<?php
the_widget( 'A_Featured_Page_Widget', 'page=' . get_page_by_path( 'about' )->ID );
?>
```

### Can I change the default text "Continue reading"? ###

Yes. In your `functions.php` file, you cand use the `afpw_link_text` filter:
```
<?php
function mytheme_change_afpw_link_text() {
    return 'Learn more';
}

add_filter( 'afpw_link_text', 'mytheme_change_afpw_link_text' );
?>
```

### Why am I unable to define a manual excerpt for my pages? ###

First, check if the option for excerpts is not showing under "Screen Options". If that's the case, probably your theme doesn't support excerpts in pages. You need to use [`add_post_type_support()`](http://codex.wordpress.org/Function_Reference/add_post_type_support) inside your `functions.php` file:
```
<?php
function mytheme_add_page_excerpt() {
    add_post_type_support( 'page', 'excerpt' );
}

add_action( 'init', 'mytheme_add_page_excerpt' );
?>
```

## Changelog ##

### 1.3 ###
* Introduce classes generally used for post thumbnails, such as `post-thumbnail` and `entry-image`.
* Update coding standards.

### 1.2 ###
* Added widget defaults and removed cache functionality to work correctly with `the_widget()`.
* Replaced `page-link` argument with `link-text`.

### 1.1 ###
* Added a filter for the "Continue reading" text.
* Now we have a FAQ.

### 1.0 ###
* First version.
