<?php
/**
 *
 */

namespace Reactive\Admin;

class Re_Admin_Rebuilder {

  public function __construct() {
    add_action('init', array($this, 'register_post_type'));
  }

  public function register_post_type() {

    $dynamic_post_types = array();

    new RedQ_Generate_Post_Type( array_merge( array(
      array(
        "name" => "reactive_builder",
        "showName" => __("Reactive Builder", "reactive"),
        'supports' => array(
          'title' => true,
          'editor' => false,
        ),
        'menuIcon' => 'dashicons-screenoptions',
        'menuPosition' => 87,
      ),
    ), $dynamic_post_types ) );
  }
}
