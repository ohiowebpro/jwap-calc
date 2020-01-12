<?php
function custom_post_type_pipe_calc() {

// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Insulation Calculator', 'Post Type General Name', 'owp-wp' ),
        'singular_name'       => _x( 'Insulation Calculator', 'Post Type Singular Name', 'owp-wp' ),
        'menu_name'           => __( 'Insul. Calc', 'owp-wp' ),
        'parent_item_colon'   => __( 'Parent Insulation Calc', 'owp-wp' ),
        'all_items'           => __( 'All Insulation Calcs', 'owp-wp' ),
        'view_item'           => __( 'View Insulation Calc', 'owp-wp' ),
        'add_new_item'        => __( 'Add New Insulation Calc', 'owp-wp' ),
        'add_new'             => __( 'Add New', 'owp-wp' ),
        'edit_item'           => __( 'Edit Insulation Calc', 'owp-wp' ),
        'update_item'         => __( 'Update Insulation Calc', 'owp-wp' ),
        'search_items'        => __( 'Search Insulation Calc', 'owp-wp' ),
        'not_found'           => __( 'Not Found', 'owp-wp' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'owp-wp' ),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label'               => __( 'insulationcalc', 'owp-wp' ),
        'description'         => __( 'Calculations for Insulation.', 'owp-wp' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array(
            'title',
            //'thumbnail',
            //'revisions',
            //'custom-fields',
        ),
        // You can associate this CPT with a taxonomy or custom taxonomy.
        //'taxonomies'          => array( 'genres' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 25,
        'can_export'          => false,
        'has_archive'         => true,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'capability_type'     => 'page',
        'menu_icon'           => 'dashicons-welcome-write-blog',
        //'register_meta_box_cb'=> 'wpt_add_event_metaboxes',
    );

    // Registering your Custom Post Type
    register_post_type( 'insulationcalc', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'custom_post_type_pipe_calc', 0 );


//ACF Items
if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_5e1b93d361033',
        'title' => 'Calculator Items',
        'fields' => array(
            array(
                'key' => 'field_5e1b93e55334e',
                'label' => 'Savings per foot',
                'name' => 'savings_per_foot',
                'type' => 'text',
                'instructions' => 'Dollar value. Do not include dollar sign.',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5e1b94105334f',
                'label' => 'Savings per inline flange',
                'name' => 'savings_per_inline_flange',
                'type' => 'text',
                'instructions' => 'Dollar value. Do not include dollar sign.',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5e1b943453350',
                'label' => 'Savings per flanged valve',
                'name' => 'savings_per_flanged_valve',
                'type' => 'text',
                'instructions' => 'Dollar value. Do not include dollar sign.',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'insulationcalc',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'testing',
    ));

endif;