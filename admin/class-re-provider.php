<?php
/**
 *
 */

namespace Reactive\Admin;
use Reactive\Admin\Re_Admin_Scripts;
use Reactive\App\Graph;
class RedQ_Provider {
  public function __construct() {

  }

  public function geobox_preview_array(){
    $geobox_preview_array = array(
      array(
        'id'      => 'location',
        'type'    => 'geobox',
        'label'   => 'Location',
      ),
    );
    return $geobox_preview_array;
  }

  public function geobox_settings_array(){
    $provider = new Re_Admin_Scripts();
    $post_types = $provider->redq_get_all_posts();
    $geobox_settings_array = array(
      array(
        'id'        => 'geobox_post_type_select',
        'type'      => 'select',
        'label'     => 'Post Types Select',
        'param'     => 'geobox_post_type_select_param',
        'multiple'  => 'true',
        'options'   => $post_types,
      ),
      array(
        "id"               => "save_geobox_settings",
        "label"            => "Save",
        "type"             => "compoundbutton",
        "parentId"         => [],
        "getallData"       => "true",
        "getFormData"      => "true",
        "fullWidthControl" => "reuseFluidButton",
        "className"        => "reuseButton"
      ),
    );
    return $geobox_settings_array;
  }
  public function general_settings_array(){
    $provider = new Re_Admin_Scripts();
    $pages = $provider->redq_get_all_pages();
    $general_settings_array = array(
      array(
        'id'        => 'reactive_search_page_url',
        'type'      => 'select',
        'label'     => 'Reactive Search Page Url',
        'param'     => 'reactive_search_page_url',
        'options'    => $pages
      ),
      array(
        "id"               => "search_override",
        "label"            => "Enable Default Search Override",
        "type"             => "switch",
        "value"            => "false"
      ),
      array(
        "id"               => "sync_builder_data",
        "label"            => "Sync Builder Data",
        "type"             => "compoundbutton",
        "getallData"       => "true",
        "getFormData"      => "true",
        "fullWidthControl" => "reuseFluidButton",
        "className"        => "reuseButton"
      ),
      array(
        "id"               => "save_general_settings",
        "label"            => "Save",
        "type"             => "compoundbutton",
        "parentId"         => [],
        "getallData"       => "true",
        "getFormData"      => "true",
        "fullWidthControl" => "reuseFluidButton",
        "className"        => "reuseButton"
      ),
    );
    return $general_settings_array;
  }
  public function meta_restrictions_array(){
    $provider = new Re_Admin_Scripts();
    $meta_keys = $provider->redq_get_all_meta_keys();
    $meta_restrictions_array = array(
      array(
        'id'        => 'restrict_metas',
        'type'      => 'checkbox',
        'label'     => 'Select Restricted Metas',
        'param'     => 'restrict_metas_param',
        'multiple'  => 'true',
        'options'   => $meta_keys,
      ),
      array(
        "id"               => "save_restrict_metas",
        "label"            => "Save",
        "type"             => "compoundbutton",
        "parentId"         => [],
        "getallData"       => "true",
        "getFormData"      => "true",
        "fullWidthControl" => "reuseFluidButton",
        "className"        => "reuseButton"
      ),
    );
    return $meta_restrictions_array;
  }

  public function rebuilder_settings_array(){
    $provider = new Re_Admin_Scripts();
    $graph = new Graph();
    $post_types = $provider->redq_get_all_posts();
    $taxonomies = $provider->redq_get_all_taxonomies();
    $meta_keys = $provider->redq_get_all_meta_keys();
    $pages = $provider->redq_get_all_pages();
    $term_meta_keys = $graph->get_all_terms_meta_key();
    $sorting_atributes = array_merge(['post_title' => 'Post Title', 'post_date' => 'Post Date', 'comment_count' => 'Post Comment Count' ], $meta_keys);
    $rebuilder_settings_array = array(
      array(
        'id'        => 'rebuilder_post_type_select',
        'type'      => 'select',
        'label'     => 'Post Types Select',
        'param'     => 'rebuilder_post_type_select_param',
        'multiple'  => 'false',
        'options'   => $post_types,
      ),
      array(
        'id'        => 'rebuilder_taxonomy_select',
        'type'      => 'select',
        'label'     => 'Taxonomies Select',
        'param'     => 'rebuilder_taxonomy_select_param',
        'multiple'  => 'true',
        'options'   => $taxonomies,
      ),
      array(
        'id'        => 'rebuilder_meta_keys_select',
        'type'      => 'select',
        'label'     => 'Meta Keys Select',
        'param'     => 'rebuilder_meta_keys_select_param',
        'multiple'  => 'true',
        'options'   => $meta_keys,
      ),
    );

    return $rebuilder_settings_array;
  }
  public function _reactive_grid_builder_settings_array(){
    $provider = new Re_Admin_Scripts();
    $post_types = $provider->redq_get_all_posts();
    $grid_builder_settings_array = array(
      array(
        'id'        => 'rebuilder_post_type_select',
        'type'      => 'select',
        'label'     => 'Post Types Select',
        'param'     => 'rebuilder_post_type_select_param',
        'multiple'  => 'true',
        'options'   => $post_types,
      ),
    );

    return $grid_builder_settings_array;
  }

  /**
  *
  * @param string [comma seperated]
  *
  */
  public function get_all_taxonomies( $post_types='post' )
  {
   $all_post_types = explode(",",$post_types);
   $taxonomies = array();
   foreach ($all_post_types as $type ) {
     $taxonomies = array_merge( $taxonomies, get_object_taxonomies( $type) ) ;
   }
   return $taxonomies;
  }

  public function get_post_image($post_id)
  {
    if (has_post_thumbnail( $post_id ) ){
      $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
      return $image[0];
    }
  }

  public function get_post_image_alt_text($post_id)
  {
    if (has_post_thumbnail( $post_id ) ){
      $image_id = get_post_thumbnail_id( $post_id );
      $alt_text = get_post_meta($image_id , '_wp_attachment_image_alt', true);
      return $alt_text;
    }
  }

  public function get_post_gallery($content_post)
  {
    $content = $content_post->post_content;
    $gallery_ids = array();
    $temp = array();
    if (strpos($content, 'gallery ids') !== false) {
      if(preg_match('/"([^"]+)"/', $content, $attachment_ids)){
        $gallery_ids = $attachment_ids[1];
      }
      $gallery_ids = explode(',', $gallery_ids);
    }
    foreach( $gallery_ids as $gallery_id ) {
      $image_link = wp_get_attachment_url( $gallery_id );
      $temp[] = $image_link;
    }
    return $temp;
  }

  public function get_post_link($post_id)
  {
    return get_the_permalink( $post_id );
  }


}
