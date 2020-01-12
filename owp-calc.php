<?php
/**
 * Plugin Name: OWP Calculator
 * Plugin URI: https://ohiowebpro.com
 * Description: Calculator for JWrap Site
 * Version: 1.0
 * Author: Eric Griffiths
 * Author URI: https://ohiowebpro.com
 **/

require "custom-content-type.php";

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'owp-calc',
        plugin_dir_url(__FILE__) . 'css/owp-calc.css',
        null,
        '0.2'
    );
    wp_enqueue_script(
        'owp-calc',
        plugin_dir_url(__FILE__ ).'js/owp-calc.js',
        array('jquery'),
        '0.1',
        true
    );
});



add_action('admin_notices', 'showAdminMessages');

function showAdminMessages() {
    $plugin_messages = array();
    $aRequired_plugins = array();

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    $aRequired_plugins = array(
        array(
            'name'=>'Advanced Custom Fields', 'download'=>'http://wordpress.org/plugins/advanced-custom-fields/', 'path'=>'advanced-custom-fields/acf.php',
        ),
    );

    foreach($aRequired_plugins as $aPlugin) {
        // Check if plugin exists
        if(!is_plugin_active( $aPlugin['path'] )) {
            $plugin_messages[] = '<div class="notice notice-error"> <p>Calc plugin requires you to install the '.$aPlugin['name'].' plugin.</p></div>';
        }
    }
    if(count($plugin_messages) > 0) {

        foreach($plugin_messages as $message) {
            echo '

                '.$message.'
            ';
        }
    }
}




add_shortcode('owp-calc',function () {
    global $wp_query, $post;
    $op = '<div class="owp-calc"><form class="" method="get" action="#">';

    $op .= '<p>The Calculator</p>';
    $temp = $wp_query;
    $wp_query= null;
    $query_args = array (
        'post_type' => 'insulationcalc',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    $wp_query = new WP_Query();
    $wp_query->query($query_args);
    $cnt =1;
    while ($wp_query->have_posts()) {
        $wp_query->the_post();
        $op .=  '<div class="owp-calc-item">
                    
                    <a href="#">+ Add '.get_the_title().' insulation</a>
                    <div class="owp-calc-val">
                        <p>Linear Feet:<br /><input type="number" value="0" name="owpcalc'.$cnt.'" id="owpcalc'.$cnt.'" /></p>
                        <p>Inline Flange Count:<br /><input type="number" value="0" name="owpcalc'.$cnt.'" id="owpcalc'.$cnt.'" /></p>
                        <p>Flanged Valve Count:<br /><input type="number" value="0" name="owpcalc'.$cnt.'" id="owpcalc'.$cnt.'" /></p>
                    </div>
                 </div>
                ';

        $cnt++;
    }
    $op .= '</form></div>';

    $wp_query = null;
    $wp_query = $temp;
    wp_reset_query();
    return $op;

});
