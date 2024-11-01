<?php
/*
Plugin Name: Simple Post Type Visibility
Description: This plugin lets you simply add a visiblity option for logged in and logged out users
Version: 1.01
Author: Rajilesh Panoli
Author URI: http://www.rajilesh.in
Text Domain: simple-post-type-visibility
License: GPL2
*/

function sptv_post_submitbox_misc_actions($post){
     if ( !current_user_can( 'manage_options', $postid ) ) return false;
     $value = get_post_meta($post->ID, '_rj_visibility_type', true);
    echo '<span id="rj_vibility-span" style="padding:20px;"><input id="sticky" name="_rj_visibility_type" ' . ((!empty($value) && $value=='loggedin') ? ' checked="checked" ' : null) . ' type="checkbox" value="loggedin"> <label for="sticky" class="selectit">Visible only for loggedin users</label><br></span>';
}
add_action('post_submitbox_misc_actions','sptv_post_submitbox_misc_actions');

function sptv_save_postdata($postid)
{   
    if ( !current_user_can( 'manage_options', $postid ) ) return false;
    if(empty($postid) ) return false;

   if($_POST['_rj_visibility_type'] !=''){
    update_post_meta($postid, '_rj_visibility_type', $_POST['_rj_visibility_type']);
   }else{
    update_post_meta($postid, '_rj_visibility_type', 'loggedout');
       
   }
}
add_action( 'save_post', 'sptv_save_postdata');

function sptv_exclude_posts($query) {
    global $wpdb;
			$t_posts = $wpdb->posts;
			$t_meta = $wpdb->prefix . "postmeta";
			
	if ( !is_user_logged_in() ) {
    if ( !is_admin() ) {
     $sql="SELECT ID FROM $wpdb->posts as wp INNER JOIN $t_meta as mt on mt.post_id=wp.ID where mt.meta_key='_rj_visibility_type' and mt.meta_value='loggedin'";
		$all_private_posts = $wpdb->get_results($sql, ARRAY_A   );
		$all_private_post_ids = array();
		if(!empty($all_private_posts)){
    		foreach( $all_private_posts as $result )
    		$all_private_post_ids[] = $result[ID];
		}
        $query->set('post__not_in', $all_private_post_ids);
	}
  }
}
add_action('pre_get_posts', 'sptv_exclude_posts');