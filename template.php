<?php
/**
 * @file
 * asu_webspark_bootstrap's primary theme functions and alterations.
 */

/**
 * Load Kalatheme dependencies.
 *
 * Implements template_preprocess_html().
 */
function asu_webspark_bootstrap_preprocess_html(&$variables) {
  // Add IE meta tag to force IE rendering mode
  $meta_ie_render_engine = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'content' =>  'IE=edge,chrome=1',
      'http-equiv' => 'X-UA-Compatible',
    )
  );
  drupal_add_html_head($meta_ie_render_engine, 'meta_ie_render_engine');

  // Add conditional stylesheets for IE
  drupal_add_css(path_to_theme() . '/css/ie9.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'IE 9', '!IE' => FALSE), 'preprocess' => FALSE));
  drupal_add_css(path_to_theme() . '/css/ie8.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'IE 8', '!IE' => FALSE), 'preprocess' => FALSE));

  // Load student CSS if this is a student template
  if (variable_get('asu_brand_is_student', 'default') == 'student') {
    drupal_add_css(drupal_get_path('theme', 'asu_webspark_bootstrap') . '/css/student/' .
      variable_get('asu_brand_is_student', 'default') .  '.css', array(
      'group' => CSS_THEME,
      'media' => 'screen',
      'weight' => '100',
      )

    );
    // Load menu CSS for student header
    if (variable_get('asu_brand_student_color', 'black') != 'black') {
      drupal_add_css(drupal_get_path('theme', 'asu_webspark_bootstrap') . '/css/student/menu/' .
        variable_get('asu_brand_student_color', 'black') .  '.css', array(
        'group' => CSS_THEME,
        'media' => 'screen',
        'weight' => '200',
        )
      );
    }
  }
}

/**
 * Implements hook_ctools_plugin_post_alter()
 */
function asu_webspark_bootstrap_ctools_plugin_post_alter(&$plugin, &$info) {
  if ($info['type'] == 'styles') {
    if ($plugin['name'] == 'kalacustomize') {
      $plugin['title'] = 'ASU Customize';
    }
  }
}

/**
 * Override or insert variables into the page template.
 *
 * Implements template_process_page().
 */
function asu_webspark_bootstrap_preprocess_page(&$variables) {
  $variables['asu_picture'] = '';
  $variables['asu_local_navicon'] = '';

  // Make sure default picture gets responsive panopoly stylingz
  if (theme_get_setting('default_picture', 'asu_webspark_bootstrap') && theme_get_setting('picture_path', 'asu_webspark_bootstrap')) {
    $image_style = module_exists('asu_cas') ? 'asu_header_image' : 'panopoly_image_full';
    $variables['asu_picture'] = theme('image_style', array(
      'style_name' => $image_style,
      'path' => theme_get_setting('picture_path', 'asu_webspark_bootstrap'),
    )
    );
  }

  // Parse sitename for color
  $variables['site_name_first'] = '';
  $variables['site_name_last'] = '';
  $middle = strrpos(substr($variables['site_name'], 0, floor(strlen($variables['site_name']) / 2)), ' ') + 1;
  $variables['site_name_first'] = substr($variables['site_name'], 0, $middle);  // "The Quick : Brown Fox "
  $variables['site_name_last'] = substr($variables['site_name'], $middle);  // "Jumped Over The Lazy / Dog"
}


/**
 * Override or insert variables into the page template.
 *
 * Implements template_process_page().
 */
function asu_webspark_bootstrap_preprocess_block(&$variables) {
  $block = $variables['block'];
  if ($block->delta == 'main-menu' && $block->module == 'system' && $block->status == 1 && $block->theme = 'asu_webspark_bootstrap') {
    // Get the entire main menu tree.
    $main_menu_tree = array();
    $main_menu_tree = menu_tree_all_data('main-menu', NULL, 2);
    // Add the rendered output to the $main_menu_expanded variable.
    //
    $main_menu_asu = menu_tree_output($main_menu_tree);
    $pri_attributes = array(
      'class' => array(
        'nav',
        'navbar-nav',
        'links',
        'clearfix',
      ),
    );
    $variables['content'] = theme('links__system_main_menu', array(
      'links' => $main_menu_asu,
      'attributes' => $pri_attributes,
      'heading' => array(
        'text' => t('Main menu'),
        'level' => 'h2',
        'class' => array('element-invisible'),
      ),
    ));
    $block->subject = '';
  }
}

/**
 * Implements hook_block_view_alter().
 *
 * We are using this to inject the bootstrap data-toggle/data-target attributes into the ASU
 * Header so that it can also activate the local menu.
 *
 */
function asu_webspark_bootstrap_block_view_alter(&$data, $block) {
  // Add the attributes if applicable
  if (($block->module == 'asu_brand') && ($block->delta == 'asu_brand_header')) {
    $data['content'] = str_replace('<a href="javascript:toggleASU();">', '<a href="javascript:toggleASU();" data-target=".navbar-collapse" data-toggle="collapse">', $data['content']);
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function asu_webspark_bootstrap_form_panels_edit_style_settings_form_alter(&$form, &$form_state) {
  // Add some extra ASU styles if extra styles are on
  if (isset($form['general_settings']['settings']['title'])) {
    $styles = array('title', 'content');
    foreach ($styles as $style) {
      $form['general_settings']['settings'][$style]['attributes']['#options'] += array(
        'featured-text' => 'ASU FEATURED TEXT',
      );
    }
  }
}

