<?php
/*
Plugin Name: izPostUser
Plugin URI: 
Description: Restricted display post for specified user
Version: 1.0
Author: Iznyn
Author URI: http://iznyn.com/
*/

/*  
    LICENSE: This source file to be part of a system developed by Iznyn 
    All copyrights use this file and the code in it owned by Iznyn. 
    It is strictly forbidden to use, copy, modify or distribute this file and 
    the code in it without the permission of the developer.
*/

require_once( plugin_dir_path( __FILE__ ) . "includes/admin.php" );

//Run plugin class
izUserPost::init();

/**
 * Bind plugin function in a class to avoid naming conflicts
 */
class izUserPost
{
    /**
     * Setup plugin
     *
     * @return void
     */
    public function init()
    {
        add_action( 'admin_menu', array( 'izUserPost', 'addAdminMenu' ) );
        add_action( 'add_meta_boxes', array( 'izUserPost', 'addMetaBox' ) );
        add_action( 'save_post', array( 'izUserPost', 'saveMetaBox' ) );
        add_action( 'admin_print_styles', array( 'izUserPost', 'printAdminStyle' ), 1000 );
        
        add_action( 'loop_start', array( 'izUserPost', 'loopStart' ) );
    }
    
    
    /**
     * Manipulate displaying posts
     *
     * @param  array $query
     * @return void
     */
    public function loopStart( $query )
    {
        if ( empty( $query->posts )) {
            return true;
        }
        $posts = $query->posts;
        $user  = wp_get_current_user();
        $auth  = false;
        if ( ! empty( $user->ID )) {
            $auth  = true;
        }
        foreach( $posts as $idx => $post ) {
            
            //Setting show post
            $show = get_post_meta( $post->ID, 'izup_show_post', true );
            if ( $auth ) {
                $roles      = $user->roles;
                $id         = $user->ID;
                
                $isAllowRole = $roles;
                $isNotRole   = array();
                $isAllowUser = true;
                $isNotUser   = false;
                
                $allowRoles = maybe_unserialize( get_post_meta( $post->ID, 'izup_allow_roles', true ) );
                $notRoles   = maybe_unserialize( get_post_meta( $post->ID, 'izup_not_allow_roles', true ) );
                $allowUsers = maybe_unserialize( get_post_meta( $post->ID, 'izup_allow_users', true ) );
                $notUser    = maybe_unserialize( get_post_meta( $post->ID, 'izup_not_allow_users', true ) );
                
                if ( ! empty( $allowRoles )) {
                    $isAllowRole = array_intersect( $allowRoles, $roles );
                }
                if ( ! empty( $notRoles )) {
                    $isNotRole = array_diff( $roles, $notRoles );
                }
                if ( ! empty( $allowUsers )) {
                    $isAllowUser = in_array( $id, $allowUsers );
                }
                if ( ! empty( $notUser )) {
                    $isNotUser = ! in_array( $id, $allowUsers );
                }
                
            } else {
                if ( ! $show ) {
                    unset( $post[$idx] );
                }
            }
        }
    }
    
    
    /**
     * Add administrator meta box
     *
     * @return void
     */
    public function addMetaBox()
    {
        $screens = array( 'post' );
        foreach( $screens as $screen ) {
            add_meta_box(
                'iz_user_post',
                __( 'User Restrictions' ),
                array( 'izUserPost', 'metaBoxContent' ),
                $screen
            );
        }
    }
    
    /**
     * Add administrator menu
     *
     * @return void
     */
    public function addAdminMenu()
    {
        add_options_page( 
            'User Post', 
            'User Post', 
            9, 
            'iz_user_post', 
            array( 'izUserPost', 'settingPage' )
        );
    }
    
    /**
     * Content for administrator meta box
     *
     * @return void
     */
    public function metaBoxContent()
    {
        $display = new izUserPost_Admin();
        $display->metabox();
    }
    
    /**
     * Menyimpan pengaturan di meta box
     *
     * @return void
     */
    public function saveMetaBox( $post_id )
    {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        // Check if the user intended to change this value.
        if ( ! isset( $_POST['iz_post_user_nonce'] ) ) {
            return;
        }
        
        $post_ID = $_POST['post_ID'];
        $fields  = array(
            'izup_allow_roles',
            'izup_not_allow_roles',
            'izup_allow_users',
            'izup_not_allow_users'
        );
        foreach( $fields as $field ) {
            if ( ! empty( $_POST[$field] )) {
                $value = maybe_serialize( $_POST[$field] );
                update_post_meta( $post_ID, $field, $value );
            }
        }
        
        $fields  = array(
            'izup_restricted',
            'izup_show_post',
        );
        foreach( $fields as $field ) {
            if ( ! empty( $_POST[$field] )) {
                $value = absint( $_POST[$field] );
                update_post_meta( $post_ID, $field, $value );
            }
        }
        
        $fields  = array(
            'izup_login_message',
            'izup_restricted_message',
        );
        foreach( $fields as $field ) {
            if ( ! empty( $_POST[$field] )) {
                $value = sanitize_text_field( $_POST[$field] );
                update_post_meta( $post_ID, $field, $value );
            }
        }
    }
    
    /**
     * Content for setting page
     *
     * @return void
     */
    public function settingPage()
    {
        $display = new izUserPost_Admin();
        $display->setting();
    }
    
    /**
     * Print admin style
     *
     * @return void
     */
    public function printAdminStyle()
    {
        $href1 = plugin_dir_url( __FILE__ ) . 'css/admin.css';
        echo '<link media="all" type="text/css" href="' . $href1 . '" rel="stylesheet">';
    }
}
?>
