<?php

/**
 * Determine if Sitepress WPML is installed
 * @return boolean
 */
function fa_is_wpml(){
    return defined('ICL_PLUGIN_PATH') && defined('ICL_LANGUAGE_CODE') && function_exists('icl_object_id');    
}

/**
 * Language switcher for WPML plugin. Outputs available languages
 * @param string $url - URL to add the lang query arg to
 * @param bool $echo - output the result (true)
 * @return boolean|string
 */
function fa_wpml_lang_switcher( $url, $before = '<div>', $after = '</div>', $echo = true ){
    $output = $before;

    $languages = icl_get_languages('skip_missing=0');
    if( $languages ){
        foreach( $languages as $lang ){
            $url = add_query_arg( array( 'lang' => $lang['language_code'] ), $url );
            $output .= sprintf(
                '<a href="%s" class="fa_wpml_language %s"><img src="%s" /> %s</a> ',
                $url,
                ( $lang['active'] ? 'active' : '' ),
                $lang['country_flag_url'],
                $lang['native_name'] );
        }
    }

    $output .= $after;

    if( $echo ){
        echo $output;
    }
    return $output;
}

/**
 * Allow filters in WP_Query for slider posts queries
 * 
 * @param array $args - extra WP_Query args
 */
function fa_allow_query_filters( $args ){
    if( !fa_is_wpml() ){
        return $args;
    }
    $args['suppress_filters'] = false;
    return $args;
}
add_filter( '_fa_slider_posts_query_args', 'fa_allow_query_filters' );
add_filter( '_fa_slider_mixed_content_query_args', 'fa_allow_query_filters' );

/**
 * Display language switcher in FA posts table to allow users to choose between 
 * posts written in different languages.
 * @param string $post_type
 */
function fa_posts_table_wpml_language_switch( $post_type ){
    if( !fa_is_wpml() ){
        return;
    }
    
    $url = fa_iframe_admin_page_url( 'fa-mixed-content-modal', array( 'post_type' => $post_type ), false );
    fa_wpml_lang_switcher( $url );
}
add_action( '_fa_posts_table_views' , 'fa_posts_table_wpml_language_switch' );

/**
 * Display language switcher in FA taxonomies table to allow users to choose between
 * categories written in different languages.
 * @param string $post_type
 */
function fa_tax_table_wpml_language_switch( $post_type, $taxonomy ){
    if( !fa_is_wpml() ){
        return;
    }
    
    $url = fa_iframe_admin_page_url( 'fa-tax-modal', array(), false );
    fa_wpml_lang_switcher( $url );
}
add_action( '_fa_taxonomies_table_views', 'fa_tax_table_wpml_language_switch', 10, 2 );

/**
 * Show post WPML language on slides made from mixed content
 * @param object $post - current post
 */
function fa_show_slide_wpml_language( $post ){
    if( !fa_is_wpml() ){
        return;
    }
    
    $langs = icl_get_languages('skip_missing=0');
    $lang = wpml_get_language_information( null, $post->ID );    
    $img = false;
    if( array_key_exists( $lang['language_code'] , $langs ) ){
        $img = sprintf( '<img src="%s" style="width:auto; height:auto; padding:1px;" />', $langs[ $lang['language_code'] ]['country_flag_url'] );  
    }
    
    echo '<li><strong>';
    printf( __('Language: %s %s', 'featured-articles-lite'), $img, $lang['display_name'] );
    echo '</strong></li>';
}
add_action( '_fa_slide_panel', 'fa_show_slide_wpml_language' );

function fa_wpml_post_language( $post_id ){
    
}

