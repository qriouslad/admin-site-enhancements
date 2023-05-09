<?php

namespace ASENHA\Classes;

/**
 * Class that provides common methods used throughout the plugin
 *
 * @since 2.5.0
 */
class Common_Methods
{
    /**
     * Get IP of the current visitor/user. In use by at least the Limit Login Attempts feature.
     *
     * @since 2.5.0
     */
    public function get_user_ip_address()
    {
        
        if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip_address = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip_address = sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] );
        } elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip_address = sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] );
        } else {
            $ip_address = '0.0.0.0';
        }
        
        return $ip_address;
    }
    
    /**
     * Convert number of seconds into hours, minutes, seconds. In use by at least the Limit Login Attempts feature.
     *
     * @since 2.5.0
     */
    public function seconds_to_period( $seconds, $conversion_type )
    {
        $period_start = new \DateTime( '@0' );
        $period_end = new \DateTime( "@{$seconds}" );
        
        if ( $conversion_type == 'to-days-hours-minutes-seconds' ) {
            return $period_start->diff( $period_end )->format( '%a days, %h hours, %i minutes and %s seconds' );
        } elseif ( $conversion_type == 'to-hours-minutes-seconds' ) {
            return $period_start->diff( $period_end )->format( '%h hours, %i minutes and %s seconds' );
        } elseif ( $conversion_type == 'to-minutes-seconds' ) {
            return $period_start->diff( $period_end )->format( '%i minutes and %s seconds' );
        } else {
            return $period_start->diff( $period_end )->format( '%a days, %h hours, %i minutes and %s seconds' );
        }
    
    }
    
    /**
     * Remove html tags and content inside the tags from a string
     *
     * @since 3.0.3
     */
    public function strip_html_tags_and_content( $string )
    {
        // Strip HTML tags and content inside them. Ref: https://stackoverflow.com/a/39320168
        $string = preg_replace( '@<(\\w+)\\b.*?>.*?</\\1>@si', '', $string );
        // Strip any remaining HTML or PHP tags
        $string = strip_tags( $string );
        return $string;
    }
    
    /**
     * Get menu hidden by toggle
     * 
     * @since 5.1.0
     */
    public function get_menu_hidden_by_toggle()
    {
        $menu_hidden_by_toggle = array();
        $options = get_option( ASENHA_SLUG_U, array() );
        
        if ( array_key_exists( 'custom_menu_hidden', $options ) ) {
            $menu_hidden = $options['custom_menu_hidden'];
            $menu_hidden = explode( ',', $menu_hidden );
            $menu_hidden_by_toggle = array();
            foreach ( $menu_hidden as $menu_id ) {
                $menu_hidden_by_toggle[] = $this->restore_menu_item_id( $menu_id );
            }
        }
        
        return $menu_hidden_by_toggle;
    }
    
    /**
     * Get user capabilities for which the "Show All/Less" menu toggle should be shown for
     * 
     * @since 5.1.0
     */
    public function get_user_capabilities_to_show_menu_toggle_for()
    {
        global  $menu ;
        $menu_always_hidden = array();
        $user_capabilities_menus_are_hidden_for = array();
        $menu_hidden_by_toggle = $this->get_menu_hidden_by_toggle();
        // indexed array
        foreach ( $menu as $menu_key => $menu_info ) {
            foreach ( $menu_hidden_by_toggle as $hidden_menu_id ) {
                
                if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
                    $menu_item_id = $menu_info[2];
                } else {
                    $menu_item_id = $menu_info[5];
                }
                
                $menu_item_id_transformed = $this->transform_menu_item_id( $menu_item_id );
                if ( $menu_item_id_transformed == $hidden_menu_id ) {
                    $user_capabilities_menus_are_hidden_for[] = $menu_info[1];
                }
            }
        }
        $user_capabilities_menus_are_hidden_for = array_unique( $user_capabilities_menus_are_hidden_for );
        return $user_capabilities_menus_are_hidden_for;
        // indexed array
    }
    
    /**
     * Transform menu item's ID
     * 
     * @since 5.1.0
     */
    public function transform_menu_item_id( $menu_item_id )
    {
        // Transform e.g. edit.php?post_type=page ==> edit__php___post_type____page
        $menu_item_id_transformed = str_replace( array( ".", "?", "=" ), array( "__", "___", "____" ), $menu_item_id );
        return $menu_item_id_transformed;
    }
    
    /**
     * Transform menu item's ID
     * 
     * @since 5.1.0
     */
    public function restore_menu_item_id( $menu_item_id_transformed )
    {
        // Transform e.g. edit__php___post_type____page ==> edit.php?post_type=page
        $menu_item_id = str_replace( array( "____", "___", "__" ), array( "=", "?", "." ), $menu_item_id_transformed );
        return $menu_item_id;
    }

}