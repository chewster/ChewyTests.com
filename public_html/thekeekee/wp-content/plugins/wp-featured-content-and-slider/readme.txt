=== WP Featured Content and Slider ===
Contributors: wponlinesupport, anoopranawat 
Tags: content slider, slider, featured, features, services, custom post slider, custom post type display, featured content, featured services, featured content rotator, featured content slider, content gallery, content slideshow, featured content slideshow, featured posts, featured content slider, shortcode
Requires at least: 3.1
Tested up to: 4.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A quick, easy way to add and display what features your company, product or service offers, using our shortcode OR template code.

== Description ==
Many CMS site needs to display Featured Content/Featured services on website. "WP Featured Content and Slider" is a clean and easy-to-use features showcase
management system for WordPress. Display Featured Content/Featured services, features your product, company or services offers, and display them via a shortcode OR template code.

View [DEMO](http://wponlinesupport.com/wp-plugin/wp-featured-content-and-slider/) for more details.

Check [PRO DEMO and Features](http://wponlinesupport.com/wp-plugin/wp-featured-content-and-slider/) for more details.

Now added Custom Post Type support where you can display custom post type content with this plugin.
<code>[featured-content post_type="featured_post"]  and
[featured-content-slider post_type="featured_post"]</code>
Where : Select Post type for featured content. You can select post type: post, page, any custom post type 

**We have given 3 designs with 2 shortcode.**
<code>[featured-content] and [featured-content-slider]</code>

Where you can display Featured Content in list view, in grid view and Featured Content Slider with responsive.
You can also select design theme from "Featured Content -> Featured Content Designs".


= Shortcode Examples =

<code>
1. Simple list view
[featured-content]

2. Grid View 
[featured-content grid="2"]

3. Slider (per row one)
[featured-content-slider]

4. Slider (per row two)
[featured-content-slider slides_column="2"]</code>


= You can use Following parameters with shortcode =

<code>[featured-content]</code>

* **limit:**
[featured-content limit="5"] ( ie Display 5 featured content on your website )
* **Design:**
[featured-content design="design-1"] ( ie Select design for featured content. Designs are design-1, design-2, design-3)
* **post_type:**
[featured-content post_type="featured_post"] ( ie Select Post type for featured content. You can select post type: post, page, any custom post type)
* **cat_id:**
[featured-content cat_id="category_id"] ( ie Display featured content categories wise)
* **grid:**
[featured-content grid="2"]  ( ie Display featured content in grid view)
* **fa_icon_color:**
[featured-content fa_icon_color="#000000"]  ( ie Change the color of Font Awesome Icon - If added insted of featured image)
* **image_style:**
[featured-content image_style="square"]  ( ie Image style "square" OR "circle")
* **display_read_more:**
[featured-content display_read_more="true"]  ( ie Display Read More Button OR Not. Values are "true" and "false")
* **content_words_limit:**
[featured-content content_words_limit="50"]  ( ie Limit the words limit in the content section.)
* **show_content:**
[featured-content show_content="true"]  ( ie show short content or not. By default value is "true". Values are "true" and "false")

= You can use Following parameters with shortcode =

<code>[featured-content-slider]</code>

* **Display Number of Featured content at time**
[featured-content-slider slides_column="2"] (Display no of columns in featured content )
* **Number of featured content slides at a time:**
[featured-content-slider slides_scroll="2"] (Controls number of featured content rotate at a time)
* **Pagination and arrows:**
[featured-content-slider dots="false" arrows="false"]
* **Autoplay and Autoplay Interval:**
[featured-content-slider autoplay="true" autoplay_interval="100"]
* **Slide Speed:**
[featured-content-slider speed="3000"]
* **limit:**
[featured-content-slider limit="5"] ( ie Display 5 featured content on your website )
* **Design:**
[featured-content-slider design="design-1"] ( ie Select design for featured content. Designs are design-1, design-2, design-3)
* **post_type:**
[featured-content-slider post_type="featured_post"] ( ie Select Post type for featured content. You can select post type: post, page, any custom post type)
* **cat_id:**
[featured-content-slider cat_id="category_id"] ( ie Display featured content categories wise)
* **fa_icon_color:**
[featured-content-slider fa_icon_color="#000000"]  ( ie Change the color of Font Awesome Icon - If added insted of featured image)
* **image_style:**
[featured-content-slider image_style="square"]  ( ie Image style "square" OR "circle")
* **display_read_more:**
[featured-content-slider display_read_more="true"]  ( ie Display Read More Button OR Not. Values are "true" and "false")
* **content_words_limit:**
[featured-content-slider content_words_limit="50"]  ( ie Limit the words limit in the content section.)
* **show_content:**
[featured-content-slider show_content="true"]  ( ie show short content or not. By default value is "true". Values are "true" and "false")


= Here is Template code =
<code><?php echo do_shortcode('[featured-content]'); ?> and <?php echo do_shortcode('[featured-content-slider]'); ?> </code>

= Added New Features  : =
* Added One more design
* Added Font Awesome Icons
* Added New shortcode parameters ie **post_type, fa_icon_color, content_words_limit, show_content**
* Added new design ie design-4

= Available fields : =
* Read More Link
* Add either Featured Image OR Font Awesome Icons (Note: for design you can us both)
* Title
* Contents

== Installation ==

1. Upload the 'WP Featured Content and Slider' folder to the '/wp-content/plugins/' directory.
2. Activate the "WP Featured Content and Slider" list plugin through the 'Plugins' menu in WordPress.
3. Add a new page and add this short code 
<code>[featured-content]</code>
4. If you want to display using slider then use this short code 
<code>[featured-content-slider]</code>
5. Here is Template code 
<code><?php echo do_shortcode('[featured-content]'); ?> </code>
6. If you want to display using slider then use this template code
<code><?php echo do_shortcode('[featured-content-slider]'); ?> </code>



== Screenshots ==

1. List and grid view.
2. Slider with designs
3. Designs
4. Category Shortcode


== Changelog ==

= 1.2.4 (28/10/2016) =
* [+] Added "How it work" tab
* Fixed slick.js load issue
* Fixed some css issues. 

= 1.2.3 =
* Fixed some css issues. 

= 1.2.2 =
* Added css for outline issue.

= 1.2.1 =
* Fixed some css issues.
* Resolved multiple slider jquery conflict issue.

= 1.2 =
* Fixed some bugs
* Added New shortcode parameters ie show_content
* Added new design ie design-4

= 1.1 =
* Added New shortcode parameters ie post_type, fa_icon_color, content_words_limit
* Added new design-3
* Added Font Awesome Icons
* Fixed some bug

= 1.0 =
* Initial release.
* Adds custom post type.


== Upgrade Notice ==

= 1.2.4 (28/10/2016) =
* [+] Added "How it work" tab
* Fixed slick.js load issue
* Fixed some css issues. 

= 1.2.3 =
* Fixed some css issues. 

= 1.2.2 =
* Added css for outline issue.

= 1.2.1 =
* Fixed some css issues.
* Resolved multiple slider jquery conflict issue.

= 1.2 =
* Fixed some bugs
* Added New shortcode parameters ie show_content
* Added new design ie design-4

= 1.1 =
* Added New shortcode parameters ie post_type, fa_icon_color, content_words_limit
* Added new design-3
* Added Font Awesome Icons
* Fixed some bug

= 1.0 =
* Initial release