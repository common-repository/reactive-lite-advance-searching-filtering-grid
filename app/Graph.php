<?php

namespace Reactive\App;

/**
* Generate graph
*/
class Graph
{

  public function allPosts($post_types, $wpml_lang){
    global $wpdb;
    if (empty($post_types)) $post_types = array('post');
    $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $post_types[] = 'inherit';
    $post_types[] = 'publish';
    if($wpml_lang === '') {
      $query = $wpdb->prepare("SELECT CASE WHEN {$wpdb->posts}.post_parent <> 0 AND {$wpdb->posts}.post_type <> 'attachment' THEN {$wpdb->posts}.post_parent ELSE {$wpdb->posts}.ID END as ID FROM {$wpdb->posts}
        WHERE post_type IN ($post_type_placeholder)
        AND (post_status = %s OR post_status = %s)", $post_types) ;
    }else{
      $post_types[] = '%post_%';
      $post_types[] = $wpml_lang;
      $translations_table_name = $wpdb->prefix . 'icl_translations';
      $query = $wpdb->prepare("SELECT CASE WHEN {$wpdb->posts}.post_parent <> 0 THEN {$wpdb->posts}.post_parent ELSE {$wpdb->posts}.ID END as ID FROM {$wpdb->posts}
        LEFT JOIN {$translations_table_name}
        ON {$translations_table_name}.element_id = {$wpdb->posts}.ID
        WHERE post_type IN ($post_type_placeholder)
        AND (post_status = %s OR post_status = %s)
        AND element_type LIKE %s
        AND language_code = %s", $post_types);
    }
    $results = $wpdb->get_results($query, 'ARRAY_A');
    return $results;
  }

  public function get_all_terms($taxonomies, $post_types, $wpml_lang) {
    global $wpdb;
    $wpdb->get_results("SET SESSION group_concat_max_len = 999999999999999", 'ARRAY_A');
    if (empty($taxonomies)) $taxonomies = array('category');
    $taxonomy_placeholder = implode(', ', array_fill(0, count($taxonomies), '%s'));
    if (empty($post_types)) $post_types = array('post');
    $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $taxonomies = array_merge($taxonomies, $post_types);
    $taxonomies[] = 'publish';
    $taxonomies[] = 'inherit';
    if($wpml_lang === '') {
      $query = $wpdb->prepare("SELECT CASE WHEN {$wpdb->posts}.post_parent <> 0 THEN GROUP_CONCAT(DISTINCT post_parent) ELSE GROUP_CONCAT(DISTINCT object_id) END as allPosts, {$wpdb->terms}.term_id, slug, taxonomy
        FROM {$wpdb->term_relationships}
        LEFT JOIN {$wpdb->term_taxonomy}
        ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
        LEFT JOIN {$wpdb->posts}
        ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
        LEFT JOIN {$wpdb->terms}
        ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
        WHERE  {$wpdb->term_taxonomy}.taxonomy IN ($taxonomy_placeholder)
        AND {$wpdb->posts}.post_type IN ($post_type_placeholder)
        AND ({$wpdb->posts}.post_status = %s OR {$wpdb->posts}.post_status = %s)
        GROUP BY {$wpdb->term_taxonomy}.term_id", $taxonomies);
    } else {
    $taxonomies[] = '%tax_%';
    $taxonomies[] = $wpml_lang;
    $translations_table_name = $wpdb->prefix . 'icl_translations';
      $query = $wpdb->prepare("SELECT CASE WHEN {$wpdb->posts}.post_parent <> 0 THEN GROUP_CONCAT(DISTINCT post_parent) ELSE GROUP_CONCAT(DISTINCT object_id) END as allPosts, {$wpdb->terms}.term_id, slug, taxonomy
      FROM {$wpdb->term_relationships}
      LEFT JOIN {$wpdb->term_taxonomy}
      ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
      LEFT JOIN {$wpdb->posts}
      ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
      LEFT JOIN {$wpdb->terms}
      ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
      LEFT JOIN {$translations_table_name}
      ON {$translations_table_name}.element_id = {$wpdb->terms}.term_id
      WHERE {$wpdb->term_taxonomy}.taxonomy IN ($taxonomy_placeholder)
      AND {$wpdb->posts}.post_type IN ($post_type_placeholder)
      AND ({$wpdb->posts}.post_status = %s OR {$wpdb->posts}.post_status = %s)
      AND element_type LIKE %s
      AND language_code = %s
      GROUP BY {$wpdb->term_taxonomy}.term_id", $taxonomies);
    }
    $results = $wpdb->get_results($query, 'ARRAY_A');
    return $results;
  }

  // public function get_term_to_term($taxonomies, $wpml_lang) {
  //   global $wpdb;
  //   if (empty($taxonomies)) $taxonomies = array('category');
  //   $taxonomy_placeholder = implode(', ', array_fill(0, count($taxonomies), '%s'));
  //   if($wpml_lang === '') {
  //     $query = $wpdb->prepare("SELECT parent, GROUP_CONCAT(DISTINCT term_id) AS allterms
  //       FROM {$wpdb->term_taxonomy}
  //       WHERE {$wpdb->term_taxonomy}.count > 0
  //       AND {$wpdb->term_taxonomy}.taxonomy IN ($taxonomy_placeholder)
  //       GROUP BY {$wpdb->term_taxonomy}.parent", $taxonomies);
  //   } else {
  //   $taxonomies[] = '%tax_%';
  //   $taxonomies[] = $wpml_lang;
  //   $translations_table_name = $wpdb->prefix . 'icl_translations';
  //   $query = $wpdb->prepare("SELECT parent, GROUP_CONCAT(DISTINCT term_id) AS allterms
  //     FROM {$wpdb->term_taxonomy}
  //     WHERE {$wpdb->term_taxonomy}.count > 0
  //     AND {$wpdb->term_taxonomy}.taxonomy IN ($taxonomy_placeholder)
  //     AND element_type LIKE %s
  //     AND language_code = %s
  //     GROUP BY {$wpdb->term_taxonomy}.parent", $taxonomies);
  //   }
  //   $results = $wpdb->get_results($query , 'ARRAY_A');
  //   return $results;
  // }
  public function get_term_to_term($taxonomies, $post_types, $wpml_lang='') {
    global $wpdb;
    $wpdb->get_results("SET SESSION group_concat_max_len = 999999999999999", 'ARRAY_A');
    if (empty($taxonomies)) $taxonomies = array('category');
    $taxonomy_placeholder = implode(', ', array_fill(0, count($taxonomies), '%s'));
    if (empty($post_types)) $post_types = array('post');
    $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $taxonomies = array_merge($taxonomies, $post_types);
    if($wpml_lang === '') {
      $query = $wpdb->prepare("SELECT firstTermTax.parent, GROUP_CONCAT( DISTINCT firstTermTax.term_id) AS allterms, firstTermTax.taxonomy
        FROM {$wpdb->term_taxonomy} as firstTermTax
        LEFT JOIN {$wpdb->term_taxonomy} as secondTermTax
        ON firstTermTax.parent = secondTermTax.term_id
        LEFT JOIN {$wpdb->term_relationships}
        ON firstTermTax.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
        LEFT JOIN {$wpdb->posts}
        ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
        WHERE  firstTermTax.taxonomy IN ($taxonomy_placeholder)
        AND {$wpdb->posts}.post_type IN ($post_type_placeholder)
        AND (secondTermTax.count <> 0 OR firstTermTax.parent = 0)
        GROUP BY firstTermTax.parent", $taxonomies);
    } else {
    $taxonomies[] = '%tax_%';
    $taxonomies[] = $wpml_lang;
    $translations_table_name = $wpdb->prefix . 'icl_translations';
    $query = $wpdb->prepare("SELECT firstTermTax.parent, GROUP_CONCAT( DISTINCT firstTermTax.term_id) AS allterms, firstTermTax.taxonomy
      FROM {$wpdb->term_taxonomy} as firstTermTax
      LEFT JOIN {$wpdb->term_taxonomy} as secondTermTax
      ON firstTermTax.parent = secondTermTax.term_id
      LEFT JOIN {$wpdb->term_relationships}
      ON firstTermTax.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
      LEFT JOIN {$wpdb->posts}
      ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
      LEFT JOIN {$translations_table_name}
      ON {$translations_table_name}.element_id = firstTermTax.term_id
      WHERE  firstTermTax.taxonomy IN ($taxonomy_placeholder)
      AND {$wpdb->posts}.post_type IN ($post_type_placeholder)
      AND (secondTermTax.count <> 0 OR firstTermTax.parent = 0)
      AND element_type LIKE %s
      AND language_code = %s
      GROUP BY firstTermTax.parent", $taxonomies);
    }
    $results = $wpdb->get_results($query , 'ARRAY_A');
    return $results;
  }

  public function get_term_to_post($taxonomies, $post_types, $wpml_lang) {
    global $wpdb;
    $wpdb->get_results("SET SESSION group_concat_max_len = 999999999999999", 'ARRAY_A');
    if (empty($taxonomies)) $taxonomies = array('category');
    $taxonomy_placeholder = implode(', ', array_fill(0, count($taxonomies), '%s'));
    if (empty($post_types)) $post_types = array('post');
    $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $taxonomies = array_merge($taxonomies, $post_types);
    $taxonomies[] = 'publish';
    if($wpml_lang === '') {
      $query = $wpdb->prepare("SELECT CASE WHEN {$wpdb->posts}.post_parent <> 0 THEN GROUP_CONCAT(post_parent) ELSE GROUP_CONCAT(object_id) END as allposts, term_id, taxonomy
      FROM {$wpdb->term_relationships}
      LEFT JOIN {$wpdb->term_taxonomy}
      ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
      LEFT JOIN {$wpdb->posts}
      ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
      WHERE {$wpdb->term_taxonomy}.taxonomy IN ($taxonomy_placeholder)
      AND {$wpdb->posts}.post_type IN ($post_type_placeholder)
      AND {$wpdb->posts}.post_status = %s
      GROUP BY {$wpdb->term_taxonomy}.term_id", $taxonomies);
    } else {
      $taxonomies[] = '%tax_%';
      $taxonomies[] = $wpml_lang;
      $translations_table_name = $wpdb->prefix . 'icl_translations';
      $query = $wpdb->prepare("SELECT CASE WHEN {$wpdb->posts}.post_parent <> 0 THEN GROUP_CONCAT(post_parent) ELSE GROUP_CONCAT(object_id) END as allposts, term_id, taxonomy
      FROM {$wpdb->term_relationships}
      LEFT JOIN {$wpdb->term_taxonomy}
      ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
      LEFT JOIN {$wpdb->posts}
      ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
      LEFT JOIN {$translations_table_name}
      ON {$translations_table_name}.element_id = {$wpdb->term_taxonomy}.term_id
      WHERE {$wpdb->term_taxonomy}.taxonomy IN ($taxonomy_placeholder)
      AND {$wpdb->posts}.post_type IN ($post_type_placeholder)
      AND {$wpdb->posts}.post_status = %s
      AND element_type LIKE %s
      AND language_code = %s
      GROUP BY {$wpdb->term_taxonomy}.term_id", $taxonomies);
    }
    $results = $wpdb->get_results($query , 'ARRAY_A');
    return $results;
  }


  public function get_meta_to_post($metakeys, $wpml_lang, $post_types, $metakeys_type_data) {
    global $wpdb;
    $wpdb->get_results("SET SESSION group_concat_max_len = 999999999999999", 'ARRAY_A');
    if (empty($metakeys)) $metakeys = array('nothing');
    $metakey_placeholder = implode(', ', array_fill(0, count($metakeys), '%s'));

    if (empty($post_types)) $post_types = array('nothing');
    $post_types_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $metakeys = array_merge($metakeys, $post_types);
    $metakeys[] = 'publish';
    $metakeys[] = 'inherit';
    //if($wpml_lang === '') {
      $query = $wpdb->prepare("SELECT GROUP_CONCAT(DISTINCT post_id) as allPosts, meta_value, meta_key
      FROM {$wpdb->postmeta}
      LEFT JOIN {$wpdb->posts}
      ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
      WHERE {$wpdb->postmeta}.meta_key IN ($metakey_placeholder)
      AND {$wpdb->posts}.post_type IN ($post_types_placeholder)
      AND {$wpdb->postmeta}.meta_value <> ''
      AND ({$wpdb->posts}.post_status = %s OR {$wpdb->posts}.post_status = %s)
      GROUP BY {$wpdb->postmeta}.meta_value, {$wpdb->postmeta}.meta_key", $metakeys);
    // } else {
    //   $translations_table_name = $wpdb->prefix . 'icl_translations';
    //   $metakeys[] = '%post_%';
    //   $metakeys[] = $wpml_lang;
    //   $query = $wpdb->prepare("SELECT GROUP_CONCAT(DISTINCT post_id) as allPosts, meta_value, meta_key
    //   FROM {$wpdb->postmeta}
    //   LEFT JOIN {$wpdb->posts}
    //   ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
    //   LEFT JOIN {$translations_table_name}
    //   ON {$translations_table_name}.element_id = {$wpdb->postmeta}.post_id
    //   WHERE {$wpdb->postmeta}.meta_key IN ($metakey_placeholder)
    //   AND meta_value <> ''
    //   AND ({$wpdb->posts}.post_status = %s OR {$wpdb->posts}.post_status = %s)
    //   AND element_type LIKE %s
    //   AND language_code = %s

    //   GROUP BY {$wpdb->postmeta}.meta_value, {$wpdb->postmeta}.meta_key", $metakeys);
    // }
    $results = $wpdb->get_results($query , 'ARRAY_A');
    foreach ($results as $key => $single_meta_info) {
      if(in_array($single_meta_info['meta_key'], $metakeys_type_data)){
        $results[$key]['meta_value'] = maybe_unserialize( $single_meta_info['meta_value'] );
      }
    }
    return $results;
  }
// SELECT group_concat(slug), taxonomy FROM wp_term_taxonomy left join wp_terms on wp_term_taxonomy.term_id = wp_terms.`term_id` where taxonomy in ('product_cat') group by taxonomy


  public function get_taxonomy_to_term($taxonomies, $post_types, $wpml_lang) {
    global $wpdb;
    $wpdb->get_results("SET SESSION group_concat_max_len = 999999999999999", 'ARRAY_A');
    if (empty($taxonomies)) $taxonomies = array('category');
    $taxonomy_placeholder = implode(', ', array_fill(0, count($taxonomies), '%s'));
    if (empty($post_types)) $post_types = array('post');
    $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $taxonomies = array_merge($taxonomies, $post_types);

    if($wpml_lang === '') {
      $query = $wpdb->prepare("SELECT GROUP_CONCAT(DISTINCT slug) as slugs, GROUP_CONCAT(DISTINCT {$wpdb->terms}.term_id) as ids, taxonomy
      FROM {$wpdb->term_taxonomy}
      LEFT JOIN {$wpdb->term_relationships}
      ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
      LEFT JOIN {$wpdb->posts}
      ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
      LEFT JOIN {$wpdb->terms}
      ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
      WHERE taxonomy IN ($taxonomy_placeholder)
      AND {$wpdb->posts}.post_type IN ($post_type_placeholder)
      GROUP BY taxonomy", $taxonomies);
    } else {
      $taxonomies[] = '%tax_%';
      $taxonomies[] = $wpml_lang;
      $translations_table_name = $wpdb->prefix . 'icl_translations';
      $query = $wpdb->prepare("SELECT GROUP_CONCAT(DISTINCT slug) as slugs, GROUP_CONCAT(DISTINCT {$wpdb->terms}.term_id) as ids, taxonomy
      FROM {$wpdb->term_taxonomy}
      LEFT JOIN {$wpdb->term_relationships}
      ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
      LEFT JOIN {$wpdb->posts}
      ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
      LEFT JOIN {$wpdb->terms}
      ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
      LEFT JOIN {$translations_table_name}
      ON {$translations_table_name}.element_id = {$wpdb->terms}.term_id
      WHERE taxonomy IN ($taxonomy_placeholder)
      AND {$wpdb->posts}.post_type IN ($post_type_placeholder)
      AND element_type LIKE %s
      AND language_code = %s
      GROUP BY taxonomy", $taxonomies);
    }
    $results = $wpdb->get_results($query , 'ARRAY_A');
    if(isset($results[0]['termNames'])){
      $results[0]['termNames'] = html_entity_decode($results[0]['termNames']);
    }
    return $results;
  }
  public function get_term_names($wpml_lang) {
    global $wpdb;
    $wpdb->get_results("SET SESSION group_concat_max_len = 999999999999999", 'ARRAY_A');
    // if (empty($taxonomies)) $taxonomies = array('category');
    // $taxonomy_placeholder = implode(', ', array_fill(0, count($taxonomies), '%s'));
    // if (empty($post_types)) $post_types = array('post');
    // $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $taxonomies[] = '';

    if($wpml_lang === '') {
      $query = $wpdb->prepare("SELECT * FROM {$wpdb->terms} WHERE term_id <> %s", $taxonomies);
    } else {
      $taxonomies[] = '%tax_%';
      $taxonomies[] = $wpml_lang;
      $translations_table_name = $wpdb->prefix . 'icl_translations';
      $query = $wpdb->prepare("SELECT * FROM {$wpdb->terms} WHERE term_id <> %s
      AND element_type LIKE %s
      AND language_code = %s
      GROUP BY taxonomy", $taxonomies);
    }
    $results = $wpdb->get_results($query , 'ARRAY_A');
    foreach ($results as $key => $result) {
      if(isset($result['name'])){
        $results[$key]['name'] = html_entity_decode($result['name'], ENT_COMPAT, 'UTF-8');
      }
    }
    return $results;
  }

  public function get_all_terms_meta_key(){
    global $wpdb;
    $all_term_meta_key = [];
    $term_meta_array = $wpdb->get_results("SELECT DISTINCT meta_key from {$wpdb->termmeta}");
    foreach ($term_meta_array as $key => $meta_key) {
      $all_term_meta_key[$meta_key->meta_key] = $meta_key->meta_key;
    }
    return $all_term_meta_key;
  }

  public function get_term_meta_to_term_data($term_meta_key) {
    global $wpdb;
    if (empty($term_meta_key)) $term_meta_key = array('nothing');
    $metakey_placeholder = implode(', ', array_fill(0, count($term_meta_key), '%s'));
    $query = $wpdb->prepare("SELECT GROUP_CONCAT({$wpdb->termmeta}.term_id) as allTerms,
    meta_value as id, meta_key as termMetaKey, taxonomy
    FROM {$wpdb->termmeta}
    LEFT JOIN {$wpdb->term_taxonomy}
    ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->termmeta}.term_id
    WHERE {$wpdb->termmeta}.meta_key IN ($metakey_placeholder)
    AND {$wpdb->termmeta}.meta_value <> ''
    AND {$wpdb->term_taxonomy}.count <> 0
    GROUP BY {$wpdb->termmeta}.meta_value, {$wpdb->termmeta}.meta_key", $term_meta_key);

    $results = $wpdb->get_results($query , 'ARRAY_A');
    return $results;
  }


  // public function text_search_result($text_search_string, $selected_posts){
  //   global $wpdb;
  //   $text_search_string = trim(urldecode($text_search_string));
  //   if (empty($selected_posts)) $selected_posts = array('nothing');
  //   $selected_posts_placeholder = implode(', ', array_fill(0, count($selected_posts), '%d'));
  //   $post_table_search = $selected_posts;
  //   $post_table_search[]= '%'.$text_search_string.'%';
  //   $post_table_search[]= '%'.$text_search_string.'%';
  //   $post_table_search[]= '%'.$text_search_string.'%';
  //   $post_query = $wpdb->prepare("SELECT GROUP_CONCAT(ID) as searchResults
  //    FROM {$wpdb->posts}
  //    WHERE ID IN ($selected_posts_placeholder)
  //    AND post_title LIKE %s
  //    OR post_content LIKE %s
  //    OR post_excerpt LIKE %s",
  //    $post_table_search);
  //   $post_table_results = $wpdb->get_results($post_query , 'ARRAY_A');
  //   $meta_table_search = $selected_posts;
  //   $meta_table_search[] = '%'.$text_search_string.'%';
  //   $meta_query = $wpdb->prepare("SELECT GROUP_CONCAT(post_id) as searchResults
  //     FROM {$wpdb->postmeta}
  //     WHERE post_id IN ($selected_posts_placeholder)
  //     AND meta_value LIKE %s",
  //     $meta_table_search);
  //   $post_meta_table_results = $wpdb->get_results($meta_query , 'ARRAY_A');
  //   $taxonomies_search_table = $selected_posts;
  //   $taxonomies_search_table[] = '%'.$text_search_string.'%';
  //   $term_query = $wpdb->prepare("SELECT GROUP_CONCAT(object_id) as searchResults
  //     FROM {$wpdb->term_relationships}
  //     LEFT JOIN {$wpdb->terms}
  //     ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->terms}.term_id
  //     LEFT JOIN {$wpdb->term_taxonomy}
  //     ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_taxonomy_id
  //     WHERE {$wpdb->term_relationships}.object_id IN ($selected_posts_placeholder)
  //     AND {$wpdb->terms}.name LIKE %s",
  //     $taxonomies_search_table);
  //   $term_table_results = $wpdb->get_results($term_query , 'ARRAY_A');
  //   $allSearchResult = $post_table_results[0]['searchResults'].','. $post_meta_table_results[0]['searchResults'].','. $term_table_results[0]['searchResults'];
  //   return $allSearchResult;
  // }
  public function text_search_result($text_search_string){
    global $wpdb;
    $text_search_string = trim(urldecode($text_search_string));
    $post_table_search[]= '%'.$text_search_string.'%';
    $post_table_search[]= '%'.$text_search_string.'%';
    $post_table_search[]= '%'.$text_search_string.'%';
    $post_query = $wpdb->prepare("SELECT GROUP_CONCAT(DISTINCT ID) as searchResults
     FROM {$wpdb->posts}
     WHERE post_title LIKE %s
     OR post_content LIKE %s
     OR post_excerpt LIKE %s",
     $post_table_search);
    $post_table_results = $wpdb->get_results($post_query , 'ARRAY_A');
    $meta_table_search = '%'.$text_search_string.'%';
    $meta_query = $wpdb->prepare("SELECT GROUP_CONCAT(DISTINCT post_id) as searchResults
      FROM {$wpdb->postmeta}
      WHERE meta_value LIKE %s",
      $meta_table_search);
    $post_meta_table_results = $wpdb->get_results($meta_query , 'ARRAY_A');

    $taxonomies_search_table = '%'.$text_search_string.'%';
    $term_query = $wpdb->prepare("SELECT GROUP_CONCAT(object_id) as searchResults
      FROM {$wpdb->term_relationships}
      LEFT JOIN {$wpdb->terms}
      ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->terms}.term_id
      LEFT JOIN {$wpdb->term_taxonomy}
      ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_taxonomy_id
      WHERE {$wpdb->terms}.name LIKE %s",
      $taxonomies_search_table);
    $term_table_results = $wpdb->get_results($term_query , 'ARRAY_A');
    $allSearchResult = $post_table_results[0]['searchResults'].','. $post_meta_table_results[0]['searchResults'].','. $term_table_results[0]['searchResults'];
    return $allSearchResult;
  }

  public function sort_by_meta_key_value($post_types, $sort_key){
    global $wpdb;
    $post_types_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $post_types[] = $sort_key;
    $post_types[] = 'publish';
    $query = $wpdb->prepare("SELECT DISTINCT post_id as ID FROM {$wpdb->postmeta}
      LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
      WHERE {$wpdb->posts}.post_type IN ($post_types_placeholder)
      AND {$wpdb->postmeta}.meta_key = %s
      AND {$wpdb->posts}.post_status = %s
      ORDER BY CASE WHEN concat('',{$wpdb->postmeta}.meta_value * 1) = {$wpdb->postmeta}.meta_value THEN ABS({$wpdb->postmeta}.meta_value) ELSE LENGTH({$wpdb->postmeta}.meta_value) END ASC", $post_types);
      //ORDER BY ABS({$wpdb->postmeta}.meta_value) ASC", $post_types);
    $results = $wpdb->get_results($query, 'ARRAY_A');
    return $results;
  }

  public function sorting_by_post_key($post_types = array(), $sort_key){
    global $wpdb;
    $post_types_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $post_types[] = 'publish';
    $query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts}
                WHERE post_type IN ($post_types_placeholder)
                AND post_status = %s
                ORDER BY $sort_key ASC", $post_types);
    $results = $wpdb->get_results($query, 'ARRAY_A');
    return $results;
  }

  public function get_geo_search_result($address, $radius){
    $location = $this->get_latlang($address);
    if ( $location ) {
      $this->location = $location;
      $map_search_data = $this->location_posts_where($radius);
      return $map_search_data;
    }
  }

  public function get_latlang($address) {
    $reactive_settings['gmap_api_key'] = 'AIzaSyBF0FPDHlurGkDKua7PfZjpD2fr2rQsRw0';
    if( isset( $reactive_settings['gmap_api_key'] ) && !empty( $reactive_settings['gmap_api_key'] ) ) {
      $url = 'https://maps.googleapis.com/maps/api/geocode/json?key='
        . $reactive_settings['gmap_api_key']
        . '&address='.urlencode($address) ;

      $http = new \WP_Http();
      $request = $http->request($url);
      if( $request && !isset( $request->errors ) ) {
        $response = $request[ "body" ];
        $geo = json_decode( $response );
        if (isset($geo) && !empty($geo)) {
        $this->geoData = $geo->results[0];
        $location = $geo->results[ 0 ]->geometry->location;
        return $location;
        }
      }
    } else {
      return array();
    }
  }


  public function location_posts_where($radius = 100) {
    global $wpdb;
    // if (empty($selected_posts)) $selected_posts = array('1');
    // $selected_posts_placeholder = implode(', ', array_fill(0, count($selected_posts), '%d'));
    $lat = $this->location->lat;
    $lng = $this->location->lng;
    $selected_posts [] = $lat;
    $selected_posts [] = $lng;
    $selected_posts [] = $lat;
    $selected_posts [] = $radius;
    $geo_searched_posts =[];
    // $radius = 100; // should be dynamic
    if( in_array('country', $this->geoData->types) && in_array('political', $this->geoData->types) ) {
      $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}re_lat_lng WHERE country= %s", $this->geoData->formatted_address);
    } else {
      // $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}re_lat_lng WHERE
      //      ( 3959 * acos( cos( radians(%s) )
      //                     * cos( radians( lat ) )
      //                     * cos( radians( lng )
      //                     - radians(%s) )
      //                     + sin( radians(%s) )
      //                     * sin( radians( lat ) ) ) ) <= %s", $lat, $lng, $lat, $radius);
      $query = $wpdb->prepare("SELECT DISTINCT id, ( 3959 * acos( cos( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) as distance FROM {$wpdb->prefix}re_lat_lng as T
      WHERE
      ( 3959 * acos( cos( radians(%s) )
                    * cos( radians( lat ) )
                    * cos( radians( lng )
                    - radians(%s) )
                    + sin( radians(%s) )
                    * sin( radians( lat ) ) ) ) <= %s ORDER BY distance ASC", $selected_posts);

    }
    $results = $wpdb->get_results($query, 'ARRAY_A');
    foreach ($results as $key => $single_post) {
      $geo_searched_posts[] = $single_post['id'];
    }
    return $geo_searched_posts;
  }

  public function get_all_restricted_metas(){
    $restricted_metas = get_option('_reactive_meta_restrictions', true);
    $restricted_metas = json_decode(stripslashes_deep($restricted_metas));
    $restricted_metas_array = $restricted_metas !='1' && $restricted_metas->restrict_metas !='' ? explode(',', $restricted_metas->restrict_metas) : [];
    return $restricted_metas_array;
  }

  public function get_rental_product_data(){
    global $wpdb;
    $query = $wpdb->prepare("SELECT post_id, meta_value FROM {$wpdb->postmeta}
      WHERE meta_key = %s", 'redq_block_dates_and_times');
    $results = $wpdb->get_results($query, 'ARRAY_A');
    $rental_boocked_date = [];
    foreach ($results as $key => $value) {
      $serialized_values = maybe_unserialize($value['meta_value']);
      if(isset($serialized_values) && is_array($serialized_values)){
        foreach ($serialized_values as $single_key => $single_value) {
          $rental_boocked_date[$value['post_id']][$single_key] = $single_value['only_block_dates'];
        }
      }
    }
    return $rental_boocked_date;
  }

  public function auto_complete_search($post_types, $text_to_search){
    global $wpdb;
    if (empty($post_types)) $post_types = array('post');
    $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $post_types[] = 'publish';
    $post_types[] = '%'.$text_to_search.'%';
    $post_types[] = '%'.$text_to_search.'%';
    $query = $wpdb->prepare("SELECT ID, post_title FROM {$wpdb->posts}
      WHERE post_type IN ($post_type_placeholder)
      AND post_status = %s
      AND (post_title LIKE %s
      OR post_content LIKE %s) LIMIT 10", $post_types);
    $result = $wpdb->get_results($query, 'ARRAY_A');
    return $result;
  }

  public function get_post_to_terms($post_types, $taxonomies, $wpml_lang='') {
    global $wpdb;
    $wpdb->get_results("SET SESSION group_concat_max_len = 999999999999999", 'ARRAY_A');
    if (empty($post_types)) $post_types = array('post');
    $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    if (empty($taxonomies)) $taxonomies = array('category');
    $taxonomy_placeholder = implode(', ', array_fill(0, count($taxonomies), '%s'));
    $post_types = array_merge($post_types, $taxonomies);
    $post_types[] = 'publish';
    $post_types[] = 'inherit';
    if($wpml_lang === '') {
      $query = $wpdb->prepare("SELECT GROUP_CONCAT({$wpdb->term_relationships}.term_taxonomy_id) as allTermsIds, GROUP_CONCAT(slug) as allTermSlugs, GROUP_CONCAT(name) as allTermNames, object_id, taxonomy FROM {$wpdb->posts}
        LEFT JOIN {$wpdb->term_relationships}
        ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id
        LEFT JOIN {$wpdb->term_taxonomy}
        ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
        LEFT JOIN {$wpdb->terms}
        ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
        WHERE {$wpdb->posts}.post_type IN ($post_type_placeholder)
        AND {$wpdb->term_taxonomy}.taxonomy IN ($taxonomy_placeholder)
        AND ({$wpdb->posts}.post_status = %s OR {$wpdb->posts}.post_status = %s)
        GROUP BY {$wpdb->term_relationships}.object_id, {$wpdb->term_taxonomy}.taxonomy", $post_types);
    }else {
      $post_types[] = '%tax_%';
      $post_types[] = $wpml_lang;
      $translations_table_name = $wpdb->prefix . 'icl_translations';
      $query = $wpdb->prepare("SELECT GROUP_CONCAT({$wpdb->term_relationships}.term_taxonomy_id) as allTermsIds, GROUP_CONCAT(slug) as allTermSlugs, GROUP_CONCAT(name) as allTermNames, object_id, taxonomy FROM {$wpdb->posts}
        LEFT JOIN {$wpdb->term_relationships}
        ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id
        LEFT JOIN {$wpdb->term_taxonomy}
        ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
        LEFT JOIN {$wpdb->terms}
        ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
        LEFT JOIN {$translations_table_name}
        ON {$translations_table_name}.element_id = {$wpdb->terms}.term_id
        WHERE {$wpdb->posts}.post_type IN ($post_type_placeholder)
        AND {$wpdb->term_taxonomy}.taxonomy IN ($taxonomy_placeholder)
        AND ({$wpdb->posts}.post_status = %s OR {$wpdb->posts}.post_status = %s)
        AND element_type LIKE %s
        AND language_code = %s
        GROUP BY {$wpdb->term_relationships}.object_id, {$wpdb->term_taxonomy}.taxonomy", $post_types);
    }
    $results = $wpdb->get_results($query, 'ARRAY_A');
    return $results;
  }

  public function process_sorting_data($post_types, $sorting_attributes){
    $graph = new Graph();
    $sorting_result = [];
    foreach ($sorting_attributes as $single_attribute_key => $single_attribute) {
      switch ($single_attribute) {
        case 'post_title':
        $sorting_result[$single_attribute] = $this->sorting_by_post_key($post_types, $single_attribute);
          break;
        case 'post_date':
        $sorting_result[$single_attribute] = $this->sorting_by_post_key($post_types, $single_attribute);
          break;
        case 'comment_count':
        $sorting_result[$single_attribute] = $this->sorting_by_post_key($post_types, $single_attribute);
          break;
        default :
          $sorting_result[$single_attribute] = $this->sort_by_meta_key_value($post_types, $single_attribute);
          break;
      }
    }
    return $sorting_result;
  }

  public function get_term_meta_to_term($term_meta_keys) {
    $term_meta_to_terms= $this->get_term_meta_to_term_data($term_meta_keys);
    foreach ($term_meta_to_terms as $key => $single_term_meta_object) {
      $termMetaData = maybe_unserialize($single_term_meta_object['id']);
      if(is_array($termMetaData)) {
        $term_meta_to_terms[$key]['id'] = $termMetaData[0]['id'];
        $term_meta_to_terms[$key]['value'] = $termMetaData[0]['value'];
        $term_meta_to_terms[$key]['imageUrl'] = $termMetaData[0]['url'];
      } else {
        if ($term_meta_to_terms[$key]['termMetaKey'] == 'thumbnail_id') {
          $term_meta_to_terms[$key]['imageUrl'] = wp_get_attachment_url($single_term_meta_object['id']);
        }else{
          $term_meta_to_terms[$key]['value'] = $single_term_meta_object['id'];
        }
      }
    }
    return $term_meta_to_terms;
  }

  public function get_attachment_dir_url() {
    $upload_dir_url_array = wp_get_upload_dir();

    if (false === $upload_dir_url_array['error']) {
      return $upload_dir_url_array['baseurl'];
    }else{
      return site_url().'/wp-content/uploads';
    }
  }

  public function get_permalink_structure($post_types) {
    global $wp_rewrite;
    $rewrite_rule = [];
    foreach ($wp_rewrite->extra_permastructs as $key => $single_rewrite_rule) {
      if(in_array($key, $post_types)){
        $rewrite_rule[$key] = $single_rewrite_rule['struct'];
      }
    }
    return $rewrite_rule;
  }

  // public function get_get_grid_all_data($post_types) {
  //   global $wpdb;
  //   if (empty($post_types)) $post_types = array('post');
  //   $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
  //   $post_types[] = '_wp_attached_file';
  //   $post_types[] = 'publish';
  //   $post_types[] = 'inherit';
  //   $query = $wpdb->prepare("SELECT DISTINCT postTable.ID, postTable.post_title, postTable.post_date, postTable.post_content, postTable.post_excerpt, postTable.post_name, postTable.post_type, CASE WHEN firstMetaTable.meta_key = '_thumbnail_id' THEN  SecondMetaTable.meta_value ELSE NULL END as thumbnailUrl
  //     FROM {$wpdb->posts} as postTable LEFT JOIN {$wpdb->postmeta} as firstMetaTable
  //     ON postTable.ID =firstMetaTable.post_id
  //     LEFT JOIN {$wpdb->postmeta} as SecondMetaTable
  //     ON firstMetaTable.meta_value = SecondMetaTable.post_id
  //     WHERE  postTable.post_type IN($post_type_placeholder)
  //     AND SecondMetaTable.meta_key = %s
  //     AND (postTable.post_status = %s OR postTable.post_status = %s)
  //     ", $post_types);
  //   $results = $wpdb->get_results($query, 'ARRAY_A');
  //   return $results;
  // }

  public function get_get_grid_all_data($post_types, $wpml_lang) {
    global $wpdb;
    $wpdb->get_results("SET SESSION group_concat_max_len = 999999999999999", 'ARRAY_A');
    if (empty($post_types)) $post_types = array('post');
    $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
    $post_types[] = 'publish';
    $post_types[] = 'inherit';
    if($wpml_lang == ''){
      $query = $wpdb->prepare("SELECT DISTINCT postTable.*,
      firstMetaTable.meta_value as thumbnailUrl, SecondMetaTable.meta_value as productGallery
      FROM {$wpdb->posts} as postTable LEFT JOIN {$wpdb->postmeta} as firstMetaTable
      ON postTable.ID = firstMetaTable.post_id and firstMetaTable.meta_key = '_thumbnail_id'
      LEFT JOIN {$wpdb->postmeta} as SecondMetaTable
      ON postTable.ID = SecondMetaTable.post_id and SecondMetaTable.meta_key = '_product_image_gallery'
      WHERE  postTable.post_type IN($post_type_placeholder)
      AND postTable.post_type NOT IN('inventory')
      AND (postTable.post_status = %s OR postTable.post_status = %s)
      ", $post_types);
    }else{
      $post_types[] = '%post_%';
      $post_types[] = $wpml_lang;
      $translations_table_name = $wpdb->prefix . 'icl_translations';
      $query = $wpdb->prepare("SELECT DISTINCT postTable.ID, postTable.post_title, postTable.post_date, postTable.post_content, postTable.post_excerpt, postTable.post_name, postTable.post_type,
      firstMetaTable.meta_value as thumbnailUrl, SecondMetaTable.meta_value as productGallery
      FROM {$wpdb->posts} as postTable LEFT JOIN {$wpdb->postmeta} as firstMetaTable
      ON postTable.ID = firstMetaTable.post_id and firstMetaTable.meta_key = '_thumbnail_id'
      LEFT JOIN {$wpdb->postmeta} as SecondMetaTable
      ON postTable.ID = SecondMetaTable.post_id and SecondMetaTable.meta_key = '_product_image_gallery'
      LEFT JOIN {$translations_table_name} as tbl_wpml
      ON tbl_wpml.element_id = postTable.ID
      WHERE  postTable.post_type IN($post_type_placeholder)
      AND postTable.post_type NOT IN('inventory')
      AND (postTable.post_status = %s OR postTable.post_status = %s)
      AND tbl_wpml.element_type LIKE %s
      AND tbl_wpml.language_code = %s"
      , $post_types);
    }

    $results = $wpdb->get_results($query, 'ARRAY_A');
    $attachments_urls = '';
    $allPosts = [];
    $all_media_attachments_urls = [];
    $attachmentDir = $this->get_attachment_dir_url();
    foreach ($results as $key => $singlePost) {
      $post_gallery_ids = $this->get_reactive_post_galleries($singlePost, false);
      $singlePost['postGallery'] = isset($post_gallery_ids) ? $post_gallery_ids : '';
      $singlePost['bookmark'] = do_shortcode('[wwc_bookmarks_button id="'.$singlePost['ID'].'"]');
      $singlePost['post_content'] = do_shortcode( $singlePost['post_content'] );
      $post_type_obj = get_post_type_object($singlePost['post_type']);
      $singlePost['post_type_name'] = $post_type_obj->labels->singular_name;
      if ($singlePost['post_type'] == 'attachment') {
        $singlePost['thumbnailUrl'] = $singlePost['ID'];
        $all_media_attachments_urls[$key]['ID'] = $singlePost['ID'];
        $media_url = str_replace($attachmentDir.'/', "", $singlePost['guid']);
        $all_media_attachments_urls[$key]['attachmentUrl'] = $media_url;
      }else{
        $attachments_urls .= $singlePost['thumbnailUrl'].',';
        $attachments_urls .= $singlePost['productGallery'].',';
      }
      $attachments_urls .= $post_gallery_ids;
      $allPosts[] = $singlePost;
    }
    $all_attachment_urls = [];
    // if(isset($media_attachments_urls)){
    //   $all_media_attachments_urls = array_unique(array_filter(explode(',', $media_attachments_urls)));
    // }
    $attachment_ids = array_unique(array_filter(explode(',', $attachments_urls)));
    if (!empty($attachment_ids)) {
      $attachment_placeholder = implode(', ', array_fill(0, count($attachment_ids), '%d'));
      $attachment_ids[] = '_wp_attached_file';
      $meta_query = $wpdb->prepare("SELECT post_id as ID, meta_value as attachmentUrl FROM {$wpdb->postmeta}
      WHERE post_id IN($attachment_placeholder)
      AND meta_key = %s", $attachment_ids);
      $all_attachment_urls = $wpdb->get_results($meta_query, 'ARRAY_A');
    }
    return [
      'allPosts' => $allPosts,
      'allAttachmentsUrl' => array_merge($all_attachment_urls, $all_media_attachments_urls)
    ];
  }
  // public function get_get_grid_all_data($post_types) {
  //   global $wpdb;
  //   if (empty($post_types)) $post_types = array('post');
  //   $post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
  //   $post_types[] = 'publish';
  //   $post_types[] = 'inherit';
  //   $query = $wpdb->prepare("SELECT DISTINCT postTable.ID, postTable.post_title, postTable.post_date, postTable.post_content, postTable.post_excerpt, postTable.post_name, postTable.post_type, CASE WHEN firstMetaTable.meta_key = '_thumbnail_id' THEN  SecondMetaTable.meta_value WHEN SecondMetaTable.meta_key != '_wp_attached_file' THEN NULL END as thumbnailUrl
  //     FROM {$wpdb->posts} as postTable LEFT JOIN {$wpdb->postmeta} as firstMetaTable
  //     ON postTable.ID =firstMetaTable.post_id and firstMetaTable.meta_key = '_thumbnail_id'
  //     LEFT JOIN {$wpdb->postmeta} as SecondMetaTable
  //     ON firstMetaTable.meta_value = SecondMetaTable.post_id and SecondMetaTable.meta_key = '_wp_attached_file'
  //     WHERE  postTable.post_type IN($post_type_placeholder)
  //     AND (postTable.post_status = %s OR postTable.post_status = %s)
  //     ", $post_types);
  //   $results = $wpdb->get_results($query, 'ARRAY_A');
  //   return $results;
  // }
  public function get_reactive_post_galleries( $post, $html = true ) {
    $post = (object) $post;
    if ( ! has_shortcode( $post->post_content, 'gallery' ) )
    return '';
    if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $post->post_content, $matches, PREG_SET_ORDER ) ) {
      $images_ids = '';
      foreach ( $matches as $shortcode ) {
        if ( 'gallery' === $shortcode[2] ) {
          $shortcode_attrs = shortcode_parse_atts( $shortcode[3] );
          if ( ! is_array( $shortcode_attrs ) ) {
            $shortcode_attrs = array();
          }
          if(isset($shortcode_attrs['ids'])){
            $images_ids .= $shortcode_attrs['ids']. ',';
          }
        }
      }
      if (!is_array($images_ids)) {
        return $images_ids;
      }
      return '';
    }
  }

  public function get_all_users()
  {
    //LEFT JOIN {$wpdb->usermeta}
    //ON {$wpdb->users}.ID = {$wpdb->usermeta}.user_id
    global $wpdb;
    $query = $wpdb->prepare("SELECT ID, user_login as username, display_name, user_email, user_url, user_nicename  FROM {$wpdb->users}
     WHERE ID <> %s", null);
    $results = $wpdb->get_results($query, 'ARRAY_A');
    return $results;
  }
}
