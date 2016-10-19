<?php
/**
 * @file
 * Contains \DrupalModule.
 */

/**
 * Base class for a Drupal module.
 */
class DrupalModule {
  // The Drupal module name.  Should be set by each extending class.
  protected $name;

  /**
   * Module constructor.
   */
  public function __construct() {}

  /**
   * Fetch an module object from memory so it doesn't have to be reloaded.
   *
   * @param string $module_class
   *   The class name for a module.
   *
   * @return object
   *   A module object.
   */
  public static function load($module_class) {
    // Attempt to load an existing module object from memory.
    $module = &drupal_static("ModuleClass:{$module_class}");

    // If we don't have a module object, create one.
    if (!$module) {
      $module = class_exists($module_class) ? new $module_class() : FALSE;
    }

    return $module;
  }


  /**
   * Get the path to the module file.
   *
   * @return string
   *   The module path.
   */
  public function path() {
    static $path;

    if (empty($path) && !empty($this->name)) {
      $path = drupal_get_path('module', $this->name);
    }

    return $path;
  }

}
