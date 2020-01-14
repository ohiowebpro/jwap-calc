<?php
/**
 * Plugin Name: OWP Calculator
 * Plugin URI: https://ohiowebpro.com
 * Description: Calculator for JWrap Site
 * Version: 1.1
 * Author: Eric Griffiths
 * Author URI: https://ohiowebpro.com
 **/

require "custom-content-type.php";

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'owp-calc',
        plugin_dir_url(__FILE__) . 'css/owp-calc.css',
        null,
        '0.4'
    );
    wp_enqueue_script(
        'owp-calc',
        plugin_dir_url(__FILE__ ).'js/owp-calc.js',
        array('jquery'),
        '0.4',
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
    $op = '<div class="owp-calc"><form class="owp-calc-form" method="get" action="#">';

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
        $title = preg_replace( '/[^a-z0-9]+/', '_', strtolower(get_the_title()));
        $op .=  '<div class="owp-calc-item">
                    
                    <a href="#"><span class="owp-calc-sign">+</span> '.get_the_title().' insulation</a>
                    <div class="owp-calc-area">
                        # of Linear Feet:<br />
                        <div class="input-group">
                            <input type="button" value="-" class="button-minus" data-field="quantity">
                            <input type="text" value="0" class="owp-calc-val" data-val="'.get_field('savings_per_foot').         '" name="'.sanitize_file_name($title).'_linear_ft" id="owpcalc'.$cnt.'"  />
                            <input type="button" value="+" class="button-plus" data-field="quantity">
                        </div>
                         # of Inline Flanges:<br />
                        <div class="input-group">
                            <input type="button" value="-" class="button-minus" data-field="quantity">
                            <input type="text" value="0" class="owp-calc-val" data-val="'.get_field('savings_per_inline_flange').'" name="'.sanitize_file_name($title).'_inline_flanges" id="owpcalc'.$cnt.'" />
                            <input type="button" value="+" class="button-plus" data-field="quantity">
                        </div>
                        # of Flanged Valves:<br />
                        <div class="input-group">
                            <input type="button" value="-" class="button-minus" data-field="quantity">
                            <input type="text" value="0" class="owp-calc-val" data-val="'.get_field('savings_per_flanged_valve').'" name="'.sanitize_file_name($title).'_flanged_valves" id="owpcalc'.$cnt.'" />
                            <input type="button" value="+" class="button-plus" data-field="quantity">
                        </div>
                    </div>
                    
                 </div>
                ';

        $cnt++;
    }
    $op .= '
            
            <button type="submit" class="owp-calc-submit">Calculate</button>
            <div class="owp-calc-output"><div class="owp-calc-savings">Select pipe sizes and quantities above to calulate your savings.</div>
                <div class="owp-calc-note">
                    <ul>
                        <li>Please note conditions/variables used may be construed as common/typical/standard for hot mix asphalt production.</li>
                        <li>Cost of fuel/types of fuel, vary by state/region.</li>
                        <li>Information is deemed to be reliable.</li>
                        <li>Other fuel saving calculation may be modified at client request using variables specific to their design conditions.</li>
                    </ul>
                </div>
                
            </div>
            <input type="hidden" id="calculated_savings" name="calculated_savings" value="" />
            </form>
            <div class="owp-calc-action" id="owp-calc-action">
                <hr />
                <form class="owp-calc-action-form" method="get" action="#">
                    <input type="hidden" name="action" value="owp_calc_action" />
                    <h4>YES! Please contact me about saving money.</h4>
                    <p>
                        *Your Name:<br />
                        <input type="text" name="your_name" id="your_name" />
                    </p>
                    <p>
                        *Company Name:<br />
                        <input type="text" name="company" id="company" />
                    </p>
                    <p>
                        *Email Address:<br />
                        <input type="text" name="email" id="email" />
                    </p>
                     <p>
                        *Phone Number:<br />
                        <input type="text" name="phone" id="phone" />
                    </p>
                    <p><button type="submit" class="owp-calc-send">SEND</button></p>
                </form>
            </div>
            <div class="owp-calc-action-form-resp"></div>
        </div>';

    $wp_query = null;
    $wp_query = $temp;
    wp_reset_query();
    return $op;

});




//wp Ajax handle

function owp_calc_action() {
    $email = '';
    foreach ($_POST as $k => $v) {
        $v = sanitize_text_field($v);
        $k = sanitize_text_field($k);
        $_POST[$k] =  sanitize_text_field($v);
        if ($k != 'action') {
            $email .= '<p><strong>' . str_replace('_', ' ', $k) . ':</strong> ' . $v . '</p>';
        }

    }

    $customerEmail = '
    <p>Thank you for using our calculator. Below is the data you entered and the potential savings. We will contact you shortly.</p>
    <p><i>Please note conditions/variables used may be construed as common/typical/standard for hot mix asphalt production.<br />
    Cost of fuel/types of fuel, vary by state/region.<br />
    Information is deemed to be reliable.<br />
    Other fuel saving calculation may be modified at client request using variables specific to their design conditions.</i></p>
    ';
    $subject = 'Request for contact from savings calculator';
    $subCust = 'Savings Calculator from '.get_bloginfo('name');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    if (wp_mail( get_option('admin_email'), $subject, $email,$headers)) {
        wp_mail( $_POST['email'], $subCust, $customerEmail.$email,$headers);
        wp_send_json_success ('success');
    } else {
        wp_send_json_error('Error sending email');
    }





}

add_action('wp_ajax_owp_calc_action','owp_calc_action');
add_action('wp_ajax_nopriv_owp_calc_action','owp_calc_action');