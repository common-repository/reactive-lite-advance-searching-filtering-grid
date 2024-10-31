<?php

namespace Reactive\App;
class Scripts {
  protected $custom_scripts = array(
    're_base',
    're_preview'
  );

  public function __construct() {
    add_action('wp_enqueue_scripts', array($this , 'load_frontend_scripts' ));
    add_filter('script_loader_tag', array($this, 'add_custom_attribute' ), 10, 2);
  }

  public function load_frontend_scripts() {
    global $post;
      wp_register_script( 'reuse-form-variable-reactive', RE_VEN.'reuse-form-variable.js', array(), $ver = true, false);
      wp_enqueue_script( 'reuse-form-variable-reactive' );
      wp_register_style('flexbox-grid-css', RE_VEN.'flexboxgrid.css', array(), $ver = false, $media = 'all');
      wp_register_style('grid-layout', RE_VEN.'grid-layout.css', array(), $ver = true, $media = 'all');
      wp_register_script( 'react', RE_VEN.'react.min.js', array(), $ver = true, true);
      wp_register_script( 'react-dom', RE_VEN.'react-dom.min.js', array(), $ver = true, true);
      wp_register_script( 'nicescroll_js', '//cdn.jsdelivr.net/jquery.nicescroll/3.6.8/jquery.nicescroll.min.js', array(), $ver = true, false);      
      wp_register_style('owlcarousel_css', RE_VEN.'owl.carousel.min.css', array(), $ver = false, $media = 'all');
      wp_register_style('owlcarousel-theme-css', RE_VEN.'owl.theme.default.min.css', array(), $ver = false, $media = 'all');
      wp_register_script( 'owlcarousel_js', RE_VEN.'owl.carousel.min.js', array(), $ver = true, false);
      wp_register_style('magnific-popup-css', RE_VEN.'magnific-popup.css', array(), $ver = false, $media = 'all');
      wp_register_script( 'magnific-popup-js', RE_VEN.'jquery.magnific-popup.min.js', array(), $ver = true, false);
      wp_register_style('gridavada-css', RE_VEN.'gridavada.css', array(), $ver = false, $media = 'all');
      wp_register_style('gridginie-css', RE_VEN.'gridginie.css', array(), $ver = false, $media = 'all');
      wp_register_style('gridUncode-css', RE_VEN.'gridUncode.css', array(), $ver = false, $media = 'all');
      wp_register_style('gridUncodeAlt-css', RE_VEN.'gridUncodeAlt.css', array(), $ver = false, $media = 'all');
      wp_register_style('gridproduct-css', RE_VEN.'gridproduct.css', array(), $ver = false, $media = 'all');
      wp_register_style('gridsimple-css', RE_VEN.'gridsimple.css', array(), $ver = false, $media = 'all');
      wp_register_style('gridTemplate-css', RE_VEN.'gridTemplate.css', array(), $ver = false, $media = 'all');
      wp_register_script( 'gridTemplate-js', RE_VEN.'gridTemplate.js', array('jquery', 'underscore'), $ver = true, false);
      wp_register_style('reactive-front-one', RE_CSS.'/reactive-front.css', array(), $ver = false, $media = 'all');
      wp_register_style('reactive-front-two', RE_CSS.'/reactive-front-two.css', array(), $ver = false, $media = 'all');
      wp_register_style('reactive-popover', RE_VEN.'/reactive-popover.css', array(), $ver = false, $media = 'all');
      wp_register_style('ionicons',
        'https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css',
        array(), $ver = false, $media = 'all');
      wp_register_script('OverlappingMarkerSpiderfier', RE_VEN.'OverlappingMarkerSpiderfier.js', array(), $ver = false, $media = 'all');

        $grid_posts = get_posts(array('post_type' => 'reactive_grid', 'posts_per_page' => '-1'));
        $category_posts = get_posts(array('post_type' => 'reactive_category', 'posts_per_page' => '-1'));
        $map_markers = get_posts(array('post_type' => 'reactive_map_marker', 'posts_per_page' => '-1'));
        $map_infos = get_posts(array('post_type' => 'reactive_map_info', 'posts_per_page' => '-1'));
        $preview_popups = get_posts(array('post_type' => 're_preview_popup', 'posts_per_page' => '-1'));
$custom_script = '';
foreach ($grid_posts as $grid) {
  $gridName = 'grid_'. str_replace("-", "", $grid->post_name);
$custom_script .= <<<EOD
    ReactiveGridLayouts.push(
	  {
	    value: "$gridName",
	    title: "$grid->post_title",
	  }
  );
EOD;
}

foreach ($category_posts as $category) {
  $categoryName = 'category_' . str_replace("-", "", $category->post_name);
$custom_script .= <<<EOD
    ReactiveCategoryLayouts.push(
    {
      value: "$categoryName",
      title: "$category->post_title",
    }
  );
EOD;
}

foreach ($map_markers as $marker) {
  $markerName = 'map_marker_' . str_replace("-", "", $marker->post_name);
$custom_script .= <<<EOD
    ReactiveMarkerIconsLayouts.push(
    {
      value: "$markerName",
      title: "$marker->post_title",
    }
  );
EOD;
}

foreach ($map_infos as $info) {
  $infoName =  'map_info_window_' . str_replace("-", "", $info->post_name);
$custom_script .= <<<EOD
    ReactiveInfoWindowLayouts.push(
    {
      value: "$infoName",
      title: "$info->post_title",
    }
  );
EOD;
}

foreach ($preview_popups as $preview_popup) {
  $preview_popupName =  'preview_popup_' . str_replace("-", "", $preview_popup->post_name);
$custom_script .= <<<EOD
    ReactivePreviewPopupLayouts.push(
    {
      value: "$preview_popupName",
      title: "$preview_popup->post_title",
    }
  );
EOD;
}

  	wp_add_inline_script('reuse-form-variable-reactive', $custom_script);
    $this->re_load_reuse_form_scripts();
    $this->load_reactive_scripts();
  }

  public function re_load_reuse_form_scripts(){
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if ( !is_plugin_active( 'redq-reuse-form/redq-reuse-form.php' ) ) {
      wp_register_style('reuse-form-two', RE_REUSE_FORM_CSS.'reuse-form-two.css', array(), $ver = false, $media = 'all');
      wp_register_style('reuse-form', RE_REUSE_FORM_CSS.'reuse-form.css', array(), $ver = false, $media = 'all');
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
      wp_register_script( 're_vendor', RE_JS. $reactive_frontend_scripts['vendor']['js'] , array(), $ver = false, true);
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
          wp_register_script( $filename, RE_JS. $file['js'] , array('jquery', 'underscore'), $ver = false, true);
          $this->localize($filename);
        }
      }
    }
    foreach ($reactive_frontend_scripts as $filename => $file) {
      if (in_array($filename, $this->custom_scripts)) {
        if ($base_loaded === false && $editMode === 'user' && $filename === 're_preview') {
          wp_register_script( $filename, RE_JS. $file['js'] , array('jquery', 'underscore'), $ver = false, true);
          $this->localize($filename);
        }
      }
    }
  }

  public function localize($filename) {
    $grid_shortcode_posts = get_posts( array('post_type' => 're_grid_shortcode', 'status' => 'publish') );
    $data = [];
    foreach ($grid_shortcode_posts as $key => $post) {
      $grid_builder_data = json_decode(get_post_meta($post->ID, '_reactive_grid_builder_settings', true));
      if (isset($grid_builder_data->rebuilder_post_type_select)) {
        $post_types = explode(',', $grid_builder_data->rebuilder_post_type_select);
      }
      $data[$post->ID] = $post_types;
    }
    wp_localize_script( $filename, 'REACTIVE_GRID', array(
      'data' => $data
    ) );
    wp_localize_script( $filename, 'REACTIVE_ADMIN', array(
        'LANG'          => $this->redq_admin_language(),
        'ERROR_MESSAGE'	=> null,
    ) );

    wp_localize_script( $filename, 'REACTIVE_AJAX_DATA', array(
			'action'    => 'reactive_ajax',
			'nonce'     => wp_create_nonce('reactive_ajax_nonce'),
			'admin_url' => admin_url( 'admin-ajax.php' ),
      'IMG'       => RE_IMG,
      'VENDOR'       => RE_VEN,
		) );
  }


  public function redq_admin_language() {
		/**
		 * Localize language files for js rendering
		 */
		$lang = array(
      'SAVE' => esc_html__('Save', 'reactive'),
      'EDIT' => esc_html__('Edit', 'reactive'),
      'DELETE_TXT' => esc_html__('Delete', 'reactive'),
      'ADD' => esc_html__('Add', 'reactive'),
      'CANCEL' => esc_html__('Cancel', 'reactive'),
      'APPLY_FILTER' => esc_html__('Apply Filter', 'reactive'),
      'DOESNT_HAVE_ANY_OPTION_FOR_THIS_COMPONENT' => esc_html__('Doesnt have any option for this component', 'reactive'),
      'PLEASE_ADD_SOME_OPTION' => esc_html__('Please Add Some Options', 'reactive'),
      'POSTS_FOUND' => esc_html__('results found ', 'reactive'),
      'POST_FOUND' => esc_html__('result found ', 'reactive'),
      'CLEAR' => esc_html__('clear', 'reactive'),
      'GET_ALL' => esc_html__('Get All', 'reactive'),
      'MY_LOCATION' => esc_html__('My Location', 'reactive'),
      'BROWSER_GEO_LOCATION_ERROR' => esc_html__('Browser Geolocation Error !', 'reactive'),
      'TIMEOUT' => esc_html__('Timeout.', 'reactive'),
      'POSITION_UNAVALABLE' => esc_html__('Position Unavailable.', 'reactive'),
      'SAVE' => esc_html__('Save', 'reactive'),
      'MORE_FILTERS' => esc_html__('More Filters', 'reactive'),
      'SAVE_TEMPLATE' => esc_html__('Save Template', 'reactive'),
      'SAVE_AS_TEMPLATE' => esc_html__('Save Template As', 'reactive'),
      'ADD_BOX' => esc_html__('Add Box', 'reactive'),
      'GLOBAL_SETTINGS' => esc_html__('GLOBAL SETTINGS', 'reactive'),
      'ADMIN_VIEW' => esc_html__('Admin View', 'reactive'),
      'USER_VIEW' => esc_html__('User View', 'reactive'),
      'BLOCK' => esc_html__('Block', 'reactive'),
      'GLOBAL' => esc_html__('Global', 'reactive'),
      'SETTINGS' => esc_html__('Settings', 'reactive'),
      'ADD_BAR_FIELDS' => esc_html__('Add Bar Fields', 'reactive'),
      'ADD_SEARCH_FIELDS' => esc_html__('Add Search Fields', 'reactive'),
      'ADD_WIDGET' => esc_html__('Add Widget', 'reactive'),
      'EDIT_SEARCH_BLOCK' => esc_html__('Edit Search Block', 'reactive'),
      'EDIT_BAR_BLOCK' => esc_html__('Edit Bar Block', 'reactive'),
      'EDIT_WIDGET' => esc_html__('Edit Widget', 'reactive'),
      'MODAL_DELETE_SWAL_TITLE' => esc_html__('Are you sure?','reactive'),
      'MODAL_DELETE_SWAL_TEXT' => esc_html__('You will not be able to recover this Block','reactive'),
      'MODAL_DELETE_SWAL_CONFIRM_BUTTON_TEXT' => esc_html__('Yes, delete it!','reactive'),
      'MODAL_DELETE_SWAL_CANCEL_BUTTON_TEXT' => esc_html__('No, cancel delete!','reactive'),
      'MODAL_DELETE_SWAL_CONFIRM_TITLE' => esc_html__('Deleted!','reactive'),
      'MODAL_DELETE_SWAL_CONFIRM_TEXT' => esc_html__('Your Reactive Block has been deleted.','reactive'),
      'MODAL_DELETE_SWAL_CANCEL_TITLE' => esc_html__('Cancelled!','reactive'),
      'MODAL_DELETE_SWAL_CANCEL_TEXT' => esc_html__('Your Reactive Block is safe :)','reactive'),


			'POST_TYPE' => __('Post Type', 'reactive'),
			'PLEASE_SELECT_ANY_POST_TYPE_YOU_WANT_TO_ADD_THIS_TAXONOMY' => __('Please select any post type you want to add this taxonomy', 'reactive'),
			'ENABLE_HIERARCHY' => __('Enable Hierarchy', 'reactive'),
			'IF_YOU_WANT_TO_ENABLE_THE_TAXONOMY_HIERARCHY_SET_TRUE' => __('If you want to enable the taxonomy hierarchy set true', 'reactive'),
			'POST_FORMATS' => __('Post Formats', 'reactive'),
			'ENABLE_POST_FORMATS_INTO_THIS_POST' => __('Enable post formats into this post.', 'reactive'),
			'PAGE_ATTRIBUTES' => __('Page Attributes', 'reactive'),
			'ENABLE_PAGE_ATTRIBUTES_INTO_THIS_POST' => __('Enable page attributes into this post.', 'reactive'),
			'REVISIONS' => __('Revisions', 'reactive'),
			'ENABLE_REVISIONS_INTO_THIS_POST' => __('Enable revisions into this post.', 'reactive'),
			'COMMENTS' => __('Comments', 'reactive'),
			'ENABLE_COMMENTS_INTO_THIS_POST' => __('Enable comments into this post.', 'reactive'),
			'CUSTOM_FIELDS' => __('Custom Fields', 'reactive'),
			'ENABLE_CUSTOM_FIELDS_INTO_THIS_POST' => __('Enable custom fields into this post.', 'reactive'),
			'TRACKBACKS' => __('Trackbacks', 'reactive'),
			'ENABLE_TRACKBACKS_INTO_THIS_POST' => __('Enable trackbacks into this post.', 'reactive'),
			'EXCERPT' => __('Excerpt', 'reactive'),
			'ENABLE_EXCERPT_INTO_THIS_POST' => __('Enable excerpt into this post.', 'reactive'),
			'THUMBNAIL' => __('Thumbnail', 'reactive'),
			'ENABLE_THUMBNAIL_INTO_THIS_POST' => __('Enable thumbnail into this post.', 'reactive'),
			'AUTHOR' => __('Author', 'reactive'),
			'ENABLE_AUTHOR_INTO_THIS_POST' => __('Enable author into this post.', 'reactive'),
			'EDITOR' => __('Editor', 'reactive'),
			'ENABLE_EDITOR_INTO_THIS_POST' => __('Enable editor into this post.', 'reactive'),
			'TITLE' => __('Title', 'reactive'),
			'ENABLE_TITILE_INTO_THIS_POST' => __('Enable title into this post.', 'reactive'),
			'ALL_ITEMS' => __('All Items', 'reactive'),
			'SINGULAR_NAME' => __('Singular Name', 'reactive'),
			'POST_SLUG' => __('Post Slug', 'reactive'),
			'IF_WANT_TO_CHANGE_THE_DEFAULT_ALL_ITEMS_NAME_ADD_THE_NAME_HERE' => __('If want to change the default all items name, add the name here', 'reactive'),
			'IF_WANT_TO_CHANGE_THE_DEFAULT_SINGULAR_NAME_ADD_THE_NAME_HERE' => __('If want to change the default singular name, add the name here', 'reactive'),
			'IF_WANT_TO_CHANGE_THE_DEFAULT_POST_SLUG_ADD_THE_NAME_HERE' => __('If want to change the default post slug, add the slug here', 'reactive'),
			'MENU_POSITION' => __('Menu Position', 'reactive'),
			'SELECT_THE_POST_TYPE_MENU_POSITION' => __('Select the post type menu position.', 'reactive'),
			'MENU_ICON' => __('Menu Icon', 'reactive'),
			'SELECT_MENU_ICON' => __('Select a menu icon.', 'reactive'),
			'BELOW_FIRST_SEPARATOR' => __('Below First Separator', 'reactive'),
			'BELOW_POSTS' => __('Below Posts', 'reactive'),
			'BELOW_MEDIA' => __('Below Media', 'reactive'),
			'BELOW_LINKS' => __('Below Links', 'reactive'),
			'BELOW_PAGES' => __('Below Pages', 'reactive'),
			'BELOW_COMMENTS' => __('Below Comments', 'reactive'),
			'BELOW_SECOND_SEPARATOR' => __('Below Second Separator', 'reactive'),
			'BELOW_PLUGINS' => __('Below Plugins', 'reactive'),
			'BELOW_USERS' => __('Below Users', 'reactive'),
			'BELOW_TOOLS' => __('Below Tools', 'reactive'),
			'BELOW_SETTINGS' => __('Below Settings', 'reactive'),
			'DEFAULT_ICON' => __('Default Icon', 'reactive'),
			'UPLOAD_ICON' => __('Upload Icon', 'reactive'),
			'ICON_TYPE' => __('Icon Type', 'reactive'),
			'SELECT_THE_DEFAULT_ICON_TYPE_OR_UPLOAD_A_NEW' => __('Select the default icon type or upload a new.', 'reactive'),
			'UPLOAD_CUSTOM_ICON' => __('Upload Custom Icon', 'reactive'),
			'YOU_CAN_UPLOAD_ANY_CUSTOM_IMAGE_ICON' => __('You can upload any custom image icon.', 'reactive'),
      'BUNDLE_COMPONENT' => __('Bundle Component', 'reactive'),
      'PICK_COLOR' => __('Pick Color','reactive'),
      'NO_RESULT_FOUND' => __('No result found', 'reactive'),
      'SEARCH' => __('search','reactive'),
      'OPEN_ON_SELECTED_HOURS' => __('Open on selected hours', 'reactive'),
      'ALWAYS_OPEN' => __('Always open', 'reactive'),
      'NO_HOURS_AVAILABLE' => __('No hours available', 'reactive'),
      'PERMANENTLY_CLOSE' => __('Permanently closed', 'reactive'),
      'MONDAY' => __('Monday', 'reactive'),
      'TUESDAY' => __('Tuesday', 'reactive'),
      'WEDNESDAY' => __('Wednesday', 'reactive'),
      'THURSDAY' => __('Thursday', 'reactive'),
      'FRIDAY' => __('Friday', 'reactive'),
      'SATURDAY' => __('Saturday', 'reactive'),
      'SUNDAY' => __('Sunday', 'reactive'),
      'WRONG_PASS' => __('Wrong Password', 'reactive'),
      'PASS_MATCH' => __('Password Matched', 'reactive'),
      'CONFIRM_PASS' => __('Confirm Password', 'reactive'),
      'CURRENTLY_WORK' => __('I currently work here', 'reactive'),
      'NO_POST_FOUND' => __('No Post found', 'reactive'),
		);

		return $lang;
	}

  public function add_custom_attribute($tag, $handle) {
    foreach($this->custom_scripts as $script) {
       if ($script === $handle) {
          return str_replace(' src', ' defer="defer" src', $tag);
       }
    }
    return $tag;
  }

}
