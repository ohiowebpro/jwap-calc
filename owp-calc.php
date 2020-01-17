<?php
/**
 * Plugin Name: OWP Calculator
 * Plugin URI: https://ohiowebpro.com
 * Description: Calculator for JWrap Site
 * Version: 0.5
 * Author: Eric Griffiths
 * Author URI: https://ohiowebpro.com
 **/

require "custom-content-type.php";

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'owp-calc',
        plugin_dir_url(__FILE__) . 'css/owp-calc.css',
        null,
        '0.5'
    );
    wp_enqueue_script(
        'owp-calc',
        plugin_dir_url(__FILE__ ).'js/owp-calc.js',
        array('jquery'),
        '0.5',
        true
    );
});



add_action('admin_notices', 'showAdminMessages');

function showAdminMessages() {
    $plugin_messages = array();
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
    $fieldArr = '';
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
            $fieldArr .= sanitize_file_name($title).',';
        $cnt++;
    }
    $op .= '
            
            <button type="submit" class="owp-calc-submit">Calculate Now!</button>
            <div class="owp-calc-output"><div class="owp-calc-savings">Select pipe sizes and quantities above to calulate your savings.</div>               
            </div>
            <input type="hidden" id="calculated_savings" name="calculated_savings" value="" />
            </form>
            <div class="owp-calc-action" id="owp-calc-action">
                <hr />
                <form class="owp-calc-action-form" method="get" action="#">
                    <input type="hidden" name="action" value="owp_calc_action" />
                    <input type="hidden" name="fields" value="'.rtrim($fieldArr,',').'" />
                    <h4>YES! Please contact me about saving money.</h4>
                    <p>
                        *First Name:<br />
                        <input type="text" name="first_name" id="first_name" />
                    </p>
                    <p>
                        *Last Name:<br />
                        <input type="text" name="last_name" id="last_name" />
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
                    <p>
                        <strong>Contact me because I’m interested in the following:</strong>
                    </p>
                    <p>
                        <input type="checkbox" name="assessment" value="true" id="assessment" /> <label for="assessment">Custom No-Obligation Energy Assessment</label>
                    </p>
                    <p>
                        <input type="checkbox" name="free_sample" value="true" id="free_sample" /> <label for="free_sample">Free Insulation Sample</label>
                    </p>
                    <p>
                        <input type="checkbox" name="estimate" value="true" id="estimate" /> <label for="estimate">Estimate Insulation Costs</label>
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


function owp_calc_email_field($val) {
    return strtoupper(str_ireplace(array('1_2','_'),array('1/2',' '),$val));
}

//wp Ajax handle
function owp_calc_action() {
    $email = '';
    foreach ($_POST as $k => $v) {
        $v = sanitize_text_field($v);
        $k = sanitize_text_field($k);
        $_POST[$k] =  sanitize_text_field($v);
        if ($k != 'action' && $k != 'fields') {
            $email .= '<p><strong>' . str_replace('_', ' ', $k) . ':</strong> ' . $v . '</p>';
        }

    }
    $customerEmail = '

    <p>
    '.$_POST['company'].'<br />
    Attn.: '.$_POST['first_name'].' '.$_POST['last_name'].'<br />
    '.$_POST['phone'].'
    </p>
    
    <p>Date: '.date('m/d/Y').'</p>
    
    <p>'.$_POST['first_name'].',<br />
        Thank you for using the JWrap Insulation Energy Savings Calculator! Your estimated savings is based on the data you entered and the Conditions &amp; Variables* below. 
    </p>
    
    <p>
        Did you know that insulating hot pipes, valves, flanges, elbows, tees and other hot components provides these benefits? And, JWrap Insulation is the ideal insulation product for difficult-to-insulate components often left by insulators.
    </p>
    
    <p>
        1. Reduces Energy Costs<br />
        2. Improves process control<br />
        3. Provides increased personnel protection from hot surfaces<br />
        4. Reduces workload on pumps and heating equipment<br />
        5. Reduces emissions<br />
        6. Provides a short term payback with significant long-term energy savings
    
    </p>
    
    <p>
    If you haven\'t already, please call 855-867-8200 to get a modified evaluation using variables specific to your design and conditions. Free JWrap Insulation samples available on request.
    </p>
    
    <p>
    I often say... "If the surface is HOT, just cover it up! The savings is tremendous!"
    </p>
    
    <p>
        <img src="https://jwrapinsulation.com/wp-content/uploads/2020/01/ray-braun-profile.jpg" width="200" height="200" alt="Ray Braun"><br />
        Best regards,<br />
        Ray Braun<br />
        855-867-8200<br />
        President and Energy Advisor<br />
        JWrap Insulation<br />
        Manufactured by Energy Reduction Solutions
    </p>

    <p>
        JWrap Insulation is fabricated in the USA
    </p>
    <p>
        Note: This estimate is deemed reliable based on common, typical, and standard conditions/variables for hot mix asphalt production. Cost of fuel and types of fuel vary by state/region.
    </p>
    <hr />
    <h4><center>JWrap Insulation</center></h4>
    <p><strong><center>Estimated Energy Savings*</center></strong></p>
    <hr />
    <table width="500" align="center" cellpadding="0" cellspacing="0" style=" border-collapse: separate;">
        
        
    ';

    $fields = explode(',',$_POST['fields']);
    $cnt = 1;
    foreach ($fields as $field) {
        if ($cnt == 1) {
            $customerEmail .= '<tr>';
        }
        $customerEmail .= '
            <td width="250" >
                <p><strong>'.owp_calc_email_field($field).'</strong><br />
                Linear Feet: '.$_POST[$field.'_linear_ft'].'<br />
                Inline flanges: '.$_POST[$field.'_inline_flanges'].'<br />
                Flanged valves: '.$_POST[$field.'_flanged_valves'].'<br />
                </p>
            </td>
        ';
        if ($cnt == 2) {
            $customerEmail .= '</tr>';
            $cnt = 1;
        }
        $cnt++;

    }


    $customerEmail .= '
        </table>
        <hr />
        <p><strong>Estimated Energy Savings: '.$_POST['calculated_savings'].'*</strong></p>
        <hr />
        <p>
            <i>
            *Conditions & Variables: Heat loss and fuel savings calculations are based on NAIMA 3EPlus V4.1. The online calculator estimates cost of energy based on the following:<br />
            Process temperature = 300 degrees F<br />
            Ambient temperature = 60 degrees F <br />
            Wind speed = 8 MPH<br />
            Fuel type = natural gas<br />
            Fuel cost = $6.84 per MMBTU<br />
            Efficiency of heating equipment = 87%<br />
            Hours of operation = approximately 5040 hours / 7 month production season<br />
            Insulation = 1 1/2” thick fiberglass with aluminum jacket
            </i>
        </p>
    ';


    $subject = 'Request for contact from savings calculator';
    $subCust = 'Your Calculated Energy Savings from JWrap Insulation';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    if (wp_mail( get_option('admin_email'), $subject, $email.$customerEmail,$headers)) {
        add_filter( 'wp_mail_from_name', 'custom_wpse_mail_from_name' );
        function custom_wpse_mail_from_name( $original_email_from ) {
            return 'Ray at JWrap Insulation';
        }
        wp_mail( $_POST['email'], $subCust, $customerEmail,$headers);
        wp_send_json_success ('success');
    } else {
        wp_send_json_error('Error sending email');
    }
}

add_action('wp_ajax_owp_calc_action','owp_calc_action');
add_action('wp_ajax_nopriv_owp_calc_action','owp_calc_action');


require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://ohiowebpro.com/wordpress/plugins/owpcalc.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'owp_calc'
);
