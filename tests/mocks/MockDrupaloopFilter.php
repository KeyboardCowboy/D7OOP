<?php
/**
 * @file
 * Contains \MockDrupaloopFilter.
 */

require_once __DIR__ . '/../../classes/DrupalFilter.php';

/**
 * Mock filter for testing.
 */
class MockDrupaloopFilter extends DrupalFilter {
  public function info() {
    return array(
      'title' => 'Test Filter',
      'description' => 'Test description.',
      'prepare callback' => '_test_prepare',
      'process callback' => '_test_process',
      'settings callback' => '_test_settings',
      'tips callback' => '_test_tips',
      'default settings' => array(
        'test_setting' => 1,
      ),
    );
  }

  public function prepare($text, $filter, $format, $langcode, $cache, $cache_id) {
    // Strip out punctuation.
    return preg_replace('/[^a-z0-9 ]/i', '', $text);
  }

  public function process($text, $filter, $format, $langcode, $cache, $cache_id) {
    // Uppercase everything.
    return strtoupper($text);
  }

  public function tips($filter, $format, $long) {
    if ($long) {
      return 'This is a much longer tip.';
    }
    else {
      return 'Short tip.';
    }
  }

  public function settingsForm($form, &$form_state, $filter, $format, $defaults, $filters) {
    $elements = parent::settingsForm($form, $form_state, $filter, $format, $defaults, $filters);

    $elements['test_field'] = array(
      '#type' => 'textfield',
    );

    return $elements;
  }

}
