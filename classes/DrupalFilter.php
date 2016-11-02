<?php
/**
 * @file
 * Contains \DrupalInputFilter.
 */

require_once __DIR__ . '/DynamicClass.php';

/**
 * Objectifies the input filter system.
 *
 * Individual filters can extend this class to implement their own filters.
 */
abstract class DrupalFilter extends DynamicClass implements DynamicClassInterface {
  use ModuleDependency;

  // Default filter info that will be inherited by subclasses.
  private static $defaultInfo = array(
    'title' => 'Filter Title',
    'description' => 'Filter description.',
    'default settings' => array(),
    'cache' => TRUE,
    'weight' => 0,
  );

  // Our custom mapping callbacks.  Don't let these be overridden.
  private static $callbacks = array(
    'prepare callback' => '_drupaloop_filter_prepare',
    'process callback' => '_drupaloop_filter_process',
    'settings callback' => '_drupaloop_filter_settings',
    'tips callback' => '_drupaloop_filter_tips',
  );

  /**
   * {@inheritdoc}
   */
  final public static function classBase() {
    return 'Filter';
  }

  /**
   * Add the filter to the registry.
   *
   * @param array $filters
   *   The filters to be returned in hook_filter_info().
   */
  public function addFilter(array &$filters) {
    $filters[$this->getMachineName()] = self::$callbacks + $this->info() + self::$defaultInfo;
  }

  /**
   * Settings form.
   *
   * Do not return the $form variable.  This contains the entire format settings
   * form.  Instead, return the $elements form which should contain just your
   * filter form fields.
   */
  public function settingsForm($form, &$form_state, $filter, $format, $defaults, $filters) {
    $filter->settings += $defaults;
    $elements = array();

    return $elements;
  }

  /**
   * Prepare callback.
   *
   * @return string
   *   The prepared text.
   */
  public function prepare($text, $filter, $format, $langcode, $cache, $cache_id) {
    return $text;
  }

  /**
   * Tips callback.
   *
   * @return string
   *   The Tips text.
   */
  public function tips($filter, $format, $long) {
    return '';
  }

  /**
   * Process callback.
   *
   * @return string
   *   The processed text.
   */
  abstract public function process($text, $filter, $format, $langcode, $cache, $cache_id);

  /**
   * Define the custom filter info.
   *
   * Default values will fill in any omitted info.
   *
   * @return array
   *   The filter info array.
   */
  abstract public function info();

}

/**
 * Filter Prepare callback.
 */
function _drupaloop_filter_prepare($text, $filter, $format, $langcode, $cache, $cache_id) {
  return DrupalFilter::load($filter->name)->prepare($text, $filter, $format, $langcode, $cache, $cache_id);
}

/**
 * Filter process callback.
 */
function _drupaloop_filter_process($text, $filter, $format, $langcode, $cache, $cache_id) {
  return DrupalFilter::load($filter->name)->process($text, $filter, $format, $langcode, $cache, $cache_id);
}

/**
 * Filter settings callback.
 */
function _drupaloop_filter_settings($form, &$form_state, $filter, $format, $defaults, $filters) {
  return DrupalFilter::load($filter->name)->settingsForm($form, $form_state, $filter, $format, $defaults, $filters);
}

/**
 * Filter tips callback.
 */
function _drupaloop_filter_tips($filter, $format, $long) {
  return DrupalFilter::load($filter->name)->tips($filter, $format, $long);
}
