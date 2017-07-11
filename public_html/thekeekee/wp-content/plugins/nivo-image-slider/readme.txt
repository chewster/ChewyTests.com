=== Nivo Image Slider ===
Contributors: sayful
Tags: slider, image slider, nivo slider, images, wordpress slider, jquery slider, responsive
Requires at least: 3.0
Tested up to: 4.2
Stable tag: 1.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress Image slider based on Nivo Slider to display image slider on post, page or theme.

== Description ==

A WordPress Image slider based on <a target="_blank" href="http://dev7studios.com/plugins/nivo-slider/">Nivo Slider</a> to display image slider on post, page or theme. It's an easy, simple & responsive slider, which looks awesome in every single theme.

= Usage - Custom Post & Shortcode =

Just create your slides from the Slider menu & paste the following shortcode where you want to display this slider:

`[all-nivoslider theme='default|dark|light|bar']`

For example: if you want to add 'light' theme just write as following code

`[all-nivoslider theme='light']`

You can change Slide transition speed by adding `animation_speed=''` and giving value in millisecond.
You can also change duration of slide showing by adding `pause_time=''` and giving value in millisecond.
Example:

`[all-nivoslider theme='light' animation_speed='500' pause_time='3000']`

If you want to add multiple slider at your site you can do it from ( plugin version 1.3.0 ). To do this you must create Slide Categories when you creating  New Slide. Now you can show your slide by categories slug. So add the following parameter to your shortcode:

`category_slug=''`

Set category to a comma separated list of Slide Categories Slug to only show those. You also need to give a unique ID if you want to show multiple slider at the same page. Example:

`[all-nivoslider id='1' theme='light' category_slug='one']`

`[all-nivoslider id='2' theme='light' category_slug='one,two,three,four']`


= Usage - Shortcode =

You can also use multiple slider at diffrent page or post at your theme write the following code

`[nivo-slider][nivoslides image_link=''][/nivo-slider]`

Inside [nivo-slider], you can add id="" to make it unique and you can add theme by adding theme="" and the value of theme that you can use is 'default', 'dark', 'light' or 'bar'. Example:

`[nivo-slider id='unique-1' theme='default'][/nivo-slider]`
`[nivo-slider id='unique-2' theme='bar'][/nivo-slider]`

Repeat "[nivoslides image_link='']" as many image as you want. Inside image_link='' put you image link like "http://lorempixel.com/400/200/sports/1/"

Inside [nivoslides image_link=''], you can also add 

alt="" to add alternative text,

caption="" to add caption to slide like this

`[nivoslides image_link='' alt='' caption='']`

= Here is a complete example of Nivo Slider with ten image and dark theme. =

`[nivo-slider id='unique-2' theme='dark'][nivoslides image_link='http://lorempixel.com/400/200/sports/1' caption='This is caption one'][nivoslides image_link='http://lorempixel.com/400/200/sports/2' caption='This is caption two'][nivoslides image_link='http://lorempixel.com/400/200/sports/3' caption='This is caption three'][nivoslides image_link='http://lorempixel.com/400/200/sports/4' caption='This is caption four'][nivoslides image_link='http://lorempixel.com/400/200/sports/5' caption='This is caption five'][nivoslides image_link='http://lorempixel.com/400/200/sports/6' caption='This is caption six'][nivoslides image_link='http://lorempixel.com/400/200/sports/7' caption='This is caption seven'][nivoslides image_link='http://lorempixel.com/400/200/sports/8' caption='This is caption eight'][/nivo-slider]`

= Still need help? Watch this Youtube Video. =

https://www.youtube.com/watch?v=ZfuD0Phpz-4

= Features =

1. Fully Responsive 
2. Unlimited Slides
3. Supported in all major browsers
4. Beautiful Transition Effects
5. Custom Post and Shortcode system

== Installation ==

Installing the plugins is just like installing other WordPress plugins. If you don't know how to install plugins, please review the two options below:

= Install by Search =

* From your WordPress dashboard, choose 'Add New' under the 'Plugins' category.
* Search for 'Nivo Image Slider' a plugin will come called 'Nivo Image Slider by Sayful Islam' and Click 'Install Now' and confirm your installation by clicking 'ok'
* The plugin will download and install. Just click 'Activate Plugin' to activate it.

= Install by ZIP File =

* From your WordPress dashboard, choose 'Add New' under the 'Plugins' category.
* Select 'Upload' from the set of links at the top of the page (the second link)
* From here, browse for the zip file included in your plugin titled 'nivo-image-slider.zip' and click the 'Install Now' button
* Once installation is complete, activate the plugin to enable its features.

= Install by FTP =

* Find the directory titles 'nivo-image-slider' and upload it and all files within to the plugins directory of your WordPress install (WORDPRESS-DIRECTORY/wp-content/plugins/) [e.g. www.yourdomain.com/wp-content/plugins/]
* From your WordPress dashboard, choose 'Installed Plugins' option under the 'Plugins' category
* Locate the newly added plugin and click on the 'Activate' link to enable its features.


== Frequently Asked Questions ==
Do you have questions or issues with Nivo Images Slider? [Ask for support here.](http://wordpress.org/support/plugin/nivo-image-slider)

== Screenshots ==

1. Screenshot of Nivo Images Slider Custom Post.
2. Screenshot of Nivo Images Slider Custom Post Catagory.
3. Screenshot of Nivo Images Slider on Page.

== Changelog ==

= version 1.3.2 =
* Added option to link each slide to a URL
* Added option to open link in the same frame as it was clicked or in a new window or tab.
* Added option to change Slide transition speed
* Added option to change duration of slide showing

= version 1.3.1 =
* Fix bug regarding previous release

= version 1.3.0 =
* Re-write with Object-oriented programming (OOP)
* Added feature to add multiple slider at page, post or theme by custom post category slug
* Added feature to add or change custom transitions per slide
* Added feature to add caption per slide

= version 1.2.3 =
* Tested at WordPress Version 4.1.
* Some small changes in language file.

= version 1.2 =
* A new option has been added to add theme for Slider from custom post type.

= version 1.1 =
* To make the plugin translation ready.
* The plugin is transtated to Bengali Language.

= version 1.0 =
* Implementation of basic functionality.

== CREDIT ==

1.This plugin was developed by [Sayful Islam](http://sayful.net)

== CONTACT ==

[Sayful Islam](http://sayful1.wordpress.com/100-2/)

== Upgrade Notice ==

Upgrade the plugin to get latest feature and faster speed and compatibility with new version.
