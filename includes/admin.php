<?php
/**
 * Class for display administrator page
 */
class izUserPost_Admin
{
    /**
     * Show content for meta box
     *
     * @return void
     */
    public function metabox()
    {
    ?>
        <div id="izup_metaBox">
            <div class="izup_fields clearfix">
                <div class="izup_userrole izup_field">
                    <p><strong><?php _e( 'Show post for this roles:' ); ?></strong></p>
                    <div class="roles checkboxs">
                        <?php
                        global $wp_roles, $post_ID;
                        
                        $allows = array();
                        $meta   = get_post_meta( $post_ID, 'izup_allow_roles', true );
                        if ( ! empty( $meta )) {
                            $allows = maybe_unserialize( $meta );
                        }
                        if ( ! empty( $wp_roles->roles )) {
                            foreach( $wp_roles->roles as $key => $role ) {
                                
                                $checked = '';
                                if ( in_array( $key, $allows )) {
                                    $checked = ' checked="checked"';
                                }
                                echo '<label><input' . $checked . ' type="checkbox" name="izup_allow_roles[]" value="' . $key . '" />' . $role['name'] . '</label>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="izup_userrole izup_field">
                    <p><strong><?php _e( 'Hide post for this roles:' ); ?></strong></p>
                    <div class="roles checkboxs">
                        <?php
                        $allows = array();
                        $meta   = get_post_meta( $post_ID, 'izup_not_allow_roles', true );
                        if ( ! empty( $meta )) {
                            $allows = maybe_unserialize( $meta );
                        }
                        if ( ! empty( $wp_roles->roles )) {
                            foreach( $wp_roles->roles as $key => $role ) {
                                $checked = '';
                                if ( in_array( $key, $allows )) {
                                    $checked = ' checked="checked"';
                                }
                                echo '<label><input' . $checked . ' type="checkbox" name="izup_not_allow_roles[]" value="' . $key . '" />' . $role['name'] . '</label>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="izup_fields clearfix">
                <div class="izup_user izup_field">
                    <p><strong><?php _e( 'Show post for this users:' ); ?></strong></p>
                    <div class="users checkboxs">
                        <?php
                        $allows = array();
                        $meta   = get_post_meta( $post_ID, 'izup_allow_users',true );
                        if ( ! empty( $meta )) {
                            $allows = maybe_unserialize( $meta );
                        }
                        if ( $users = get_users() ) {
                            foreach( $users as $user ) {
                                $checked = '';
                                if ( in_array( $user->ID, $allows )) {
                                    $checked = ' checked="checked"';
                                }
                                echo '<label><input' . $checked . ' type="checkbox" name="izup_allow_users[]" value="' . $user->ID . '" />' . $user->user_login . '</label>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="izup_user izup_field">
                    <p><strong><?php _e( 'Hide post for this users:' ); ?></strong></p>
                    <div class="users checkboxs">
                        <?php
                        $allows = array();
                        $meta   = get_post_meta( $post_ID, 'izup_not_allow_users', true );
                        if ( ! empty( $meta )) {
                            $allows = maybe_unserialize( $meta );
                        }
                        if ( $users = get_users() ) {
                            foreach( $users as $user ) {
                                $checked = '';
                                if ( in_array( $user->ID, $allows )) {
                                    $checked = ' checked="checked"';
                                }
                                echo '<label><input' . $checked . ' type="checkbox" name="izup_not_allow_users[]" value="' . $user->ID . '" />' . $user->user_login . '</label>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="izup_fields clearfix">
                <div class="izup_user izup_field">
                    <p><strong><?php _e( 'Reading Post' ); ?></strong></p>
                    <div class="input">
                        <p><?php _e( 'Whether the post will be displayed for users who are not eligible?' ); ?></p>
                        <?php
                        $options = array(
                            '1' => __( 'Show' ),
                            '0' => __( 'Hide' ),
                        );
                        $current = get_post_meta( $post_ID, 'izup_show_post', true );
                        foreach( $options as $key => $label ) {
                            $checked = '';
                            if ( $current == $key ) {
                                $checked = ' checked="checked"';
                            }
                            echo '<label><input' . $checked . ' type="radio" name="izup_show_post" value="' . $key . '" />' . $label . '</label>';
                        }
                        ?>
                    </div>
                    <div class="input">
                        <p><?php _e( 'Whether the user is forced to login when viewing the post detail?' ); ?></p>
                        <?php
                        $options = array(
                            '1' => __( 'Forced to login' ),
                            '2' => __( 'Show restricted post' ),
                            '3' => __( 'Show Error 404 page' ),
                        );
                        $current = get_post_meta( $post_ID, 'izup_restricted', true );
                        foreach( $options as $key => $label ) {
                            $checked = '';
                            if ( $current == $key ) {
                                $checked = ' checked="checked"';
                            }
                            echo '<label><input' . $checked . ' type="radio" name="izup_restricted" value="' . $key . '" />' . $label . '</label>';
                        }
                        ?>
                    </div>
                </div>
                <div class="izup_msg izup_user izup_field">
                    <p><strong><label for="izup_login_message"><?php _e( 'Login Message' ); ?></label></strong></p>
                    <textarea id="izup_login_message" name="izup_login_message"><?php echo get_post_meta( $post_ID, 'izup_login_message', true ); ?></textarea>
                    
                    <p><strong><label for="izup_restricted_message"><?php _e( 'Restricted Message' ); ?></label></strong></p>
                    <textarea id="izup_restricted_message" name="izup_restricted_message"><?php echo get_post_meta( $post_ID, 'izup_restricted_message', true ); ?></textarea>
                </div>
            </div>
            <?php wp_nonce_field( plugin_basename( __FILE__ ), 'iz_post_user_nonce' ); ?>
        </div>
    <?php
    }
    
    /**
     * Show content for setting page in administrator
     *
     * @return void
     */
    public function setting()
    {
        $submit  = false;
        $success = true;
        if ( isset( $_POST['izup_dosave'] )) {
            $submit  = true;
            $msg     = sanitize_text_field( $_POST['izup_loginMessage'] );
            $success = update_option( 'izup_login_message', $msg );
            
            $msg     = sanitize_text_field( $_POST['izup_restrictedMessage'] );
            $success = update_option( 'izup_restricted_message', $msg );
        }
    ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32">
                <br>
            </div>
            <h2><?php _e( 'User Post Settings' ); ?></h2>
            
            <?php
            if ( $submit ) {
                echo '<div id="messages" class="updated"><p>' . __( 'Successfully saving settings' ) . '</p></div>';
            }
            ?>
            
            <div id="izup_setting">
                <form id="ip_popupHomeForm" method="post" action="">
                    <h3><?php _e( 'Global Message' ); ?></h3>
                    <table class="form-table izup_msg">
                        <tbody>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="izup_loginMessage"><?php _e( 'Login Message' ); ?></label>
                                </th>
                                <td>
                                    <textarea id="izup_loginMessage" name="izup_loginMessage"><?php echo get_option( 'izup_login_message', '' ); ?></textarea>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="izup_restrictedMessage"><?php _e( 'Restricted Message' ); ?></label>
                                </th>
                                <td>
                                    <textarea id="izup_restrictedMessage" name="izup_restrictedMessage"><?php echo get_option( 'izup_restricted_message', '' ); ?></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="buttons">
                        <input type="submit" class="button button-primary button-large" id="izup_dosave" name="izup_dosave" value="<?php _e( 'Save Changes' ); ?>" />
                    </div>
                </form>
            </div>
        </div>
    <?php
    }
}
?>