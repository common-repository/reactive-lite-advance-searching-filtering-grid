<?php

namespace Reactive\App;

/**
* Enqueue Scripts
*/
class EnqueueScripts
{
  protected $custom_scripts = array(
    're_base',
    're_preview'
  );
	public function enqueue_all_scripts() {
		global $post;
    wp_enqueue_script( 'reuse-form-variable-reactive' );
    wp_enqueue_script( 'wp-util' );
    wp_enqueue_style('flexbox-grid-css');
    wp_enqueue_style('grid-layout');
    wp_enqueue_script( 'react' );
    wp_enqueue_script( 'react-dom' );
    wp_enqueue_style('owlcarousel_css');
    wp_enqueue_style('owlcarousel-theme-css');
    wp_enqueue_script( 'owlcarousel_js' );
    wp_enqueue_script( 'nicescroll_js' );    
    wp_enqueue_style('magnific-popup-css');
    wp_enqueue_script( 'magnific-popup-js' );
    wp_enqueue_style('gridavada-css');
    wp_enqueue_style('gridginie-css');
    wp_enqueue_style('gridUncode-css');
    wp_enqueue_style('gridUncodeAlt-css');
    wp_enqueue_style('gridproduct-css');
    wp_enqueue_style('gridsimple-css');
    wp_enqueue_style('gridTemplate-css');
    wp_enqueue_script( 'gridTemplate-js' );
    wp_enqueue_style('reactive-front-one');
    wp_enqueue_style('reactive-front-two');
    wp_enqueue_style('reactive-popover');
    wp_enqueue_style('ionicons');
    wp_enqueue_script('OverlappingMarkerSpiderfier');
    
		$this->re_load_reuse_form_scripts();
		$this->load_reactive_scripts();
	}

  public function re_load_reuse_form_scripts(){
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if ( !is_plugin_active( 'redq-reuse-form/redq-reuse-form.php' ) ) {
      wp_enqueue_style('reuse-form-two');
      wp_enqueue_style('reuse-form');
      $reuse_form_scripts = new Reuse;
      $webpack_public_path = get_option('webpack_public_path_url', true);
      //if (file_exists($webpack_public_path)) {
        $reuse_form_scripts->load($webpack_public_path);
      //}
    }
  }

  public function load_reactive_scripts() {
    // All other assets
    $is_admin = 'false';
    if (current_user_can('editor') || current_user_can('administrator') ) {
      $is_admin = 'true';
    }
    $editMode = $is_admin === 'true' ? 'admin' : 'user';
    if (isset($_POST['editMode'])) {
      $editMode = $_POST['editMode'];
    }

    $reactive_frontend_scripts = json_decode(file_get_contents( RE_FILE . "/resource/frontend-assets.json"),true);
    if (isset($reactive_frontend_scripts['vendor'])) {
      wp_enqueue_script( 're_vendor' );
    }
    $base_loaded = false;
    $is_admin = 'false';
    if (current_user_can('editor') || current_user_can('administrator') ) {
      $is_admin = 'true';
    }
    $wpml_lang = '';
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
      //plugin is activated
      $wpml_lang = ICL_LANGUAGE_CODE;
    }
    foreach ($reactive_frontend_scripts as $filename => $file) {
      if (in_array($filename, $this->custom_scripts)) {
        if ($filename === 're_base' && $editMode === 'admin') {
          $base_loaded = true;
          wp_enqueue_script( $filename );
        }
      }
    }
    foreach ($reactive_frontend_scripts as $filename => $file) {
      if (in_array($filename, $this->custom_scripts)) {
        if ($base_loaded === false && $editMode === 'user' && $filename === 're_preview') {
          wp_enqueue_script( $filename );
        }
      }
    }
  }

}
