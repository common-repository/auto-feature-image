<?php
/*
Plugin Name: Auto Feature Image
Plugin URI: http://ewsel.com
Version: v1.2.1
Author: <a href="http://www.ewsel.com">EWSEL</a>
Description: A plugin that will automatically attach a featured image.  It will randomly pick from your
media any file that is NOT attached and starts with a EW-

Copyright 2012  EWSEL Team  (email : info [at]ewsel DOT com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function ewsel_autoset_featured() {
     global $post;
     $already_has_thumb = has_post_thumbnail($post->ID);
     if (!$already_has_thumb)  {
         $attached_image = get_children( "post_parent=$post->ID&post_type=attachment&post_mime_type=image&numberposts=1" );
         if ($attached_image) {
              foreach ($attached_image as $attachment_id => $attachment) {
                   set_post_thumbnail($post->ID, $attachment_id);
              }
         } else {
              set_post_thumbnail($post->ID, get_image());
         }
     }
}

function get_image(){
     $list = array();
     $data = array();

     $media_query = new WP_Query(
                    array(  'post_status' => 'inherit',
                            'post_parent' => 0,
                            'post_type' => 'attachment',
                            'post_mime_type' =>	'image/jpeg'
			  ));

     foreach ($media_query->posts as $post) {
          $fullpath = wp_get_attachment_url($post->ID);
	  $list     = (explode("/", $fullpath));
	  $string   = $post->ID.'|'.$list[sizeof($list)-1];

          if (substr($list[sizeof($list)-1],0,3) == 'EW-') {
     		array_push($data,(explode("|", $string)));
                $idx      = mt_rand(0, count($data)-1);
		$img_id   = $data[$idx][0];
     	  }
     }

     return $img_id;
}

add_action('the_post', 'ewsel_autoset_featured');
add_action('save_post', 'ewsel_autoset_featured');
add_action('draft_to_publish', 'ewsel_autoset_featured');
add_action('new_to_publish', 'ewsel_autoset_featured');
add_action('pending_to_publish', 'ewsel_autoset_featured');
add_action('future_to_publish', 'ewsel_autoset_featured');

?>