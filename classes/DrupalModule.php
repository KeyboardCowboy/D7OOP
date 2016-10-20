<?php
/**
 * @file
 * Contains \DrupalModule.
 */

/**
 * Base class for a Drupal module.
 */
class DrupalModule {
  // The Drupal module moduleName.  Should be set by each extending class.
  protected $moduleName;

  // Register variable names and their default values.
  protected $variables = array();

  /**
   * Module constructor.
   *
   * @param string $module_name
   *   A Drupal module machine name.
   */
  public function __construct($module_name) {
    $this->moduleName = $module_name;
  }

  /**
   * Load an module object from memory so it doesn't have to be reloaded.
   *
   * Module class names must follow the pattern of converting the module name
   * from snake_case to CamelCase and appending 'Module' to it.
   *
   * Ex. Module 'awesome_sauce' has the class 'AwesomeSauceModule'
   *
   * @param string $module_name
   *   The machine moduleName of the module.
   *
   * @return \DrupalModule|bool
   *   A module object or FALSE if we can't instantiate.
   *
   * @throws \DrupalOopMissingClassException
   *   If the module class cannot be found.
   */
  public static function load($module_name) {
    $module_class = DrupalModule::modToClass($module_name);

    // Attempt to load an existing module object from memory.
    $module = &drupal_static("DrupalOOP:{$module_class}");

    // If we don't have a module object, create one.
    if (!$module) {
      if (class_exists($module_class)) {
        $module = new $module_class($module_name);
      }
      else {
        $module = FALSE;
        $vars = array(
          '@class' => $module_class,
          '@mod' => $module_name,
        );

        throw new DrupalOopMissingClassException(t("Class @class not found for module @mod.", $vars));
      }
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

    if (empty($path)) {
      $path = drupal_get_path('module', $this->moduleName);
    }

    return $path;
  }

  /**
   * Get a local system variable for this module.
   *
   * @param string $var_name
   *   The local name of the variable to get.
   *
   * @return mixed
   *   The value of the system var.
   *
   * @see $this->variables
   * @see variable_get()
   */
  protected function varGet($var_name) {
    $default = isset($this->variables[$var_name]) ? $this->variables[$var_name] : NULL;

    return variable_get($this->getSystemVarName($var_name), $default);
  }

  /**
   * Set a local system variable for this module.
   *
   * @param string $var_name
   *   The module variable name.
   * @param mixed $value
   *   The value to set.
   *
   * @see $this->variables
   * @see variable_set()
   */
  public function varSet($var_name, $value) {
    variable_set($this->getSystemVarName($var_name), $value);
  }

  /**
   * Delete a local system variable.
   *
   * @param string $var_name
   *   A local var name.
   *
   * @see $this->variables
   * @see variable_del()
   */
  public function varDel($var_name) {
    variable_del($this->getSystemVarName($var_name));
  }

  /**
   * Delete all registered local variables from the system.
   *
   * @see hook_uninstall()
   */
  public function deleteAllVars() {
    foreach (array_keys($this->variables) as $var_name) {
      $this->varDel($var_name);
    }
  }

  /**
   * Get a full, prefixed variable name.
   *
   * @param string $var_name
   *   The variable to load.
   *
   * @return string
   *   The full, prefixed variable name.
   */
  protected function getSystemVarName($var_name) {
    return "{$this->moduleName}_{$var_name}";
  }

  /**
   * Convert a module machine moduleName to an appropriate class moduleName.
   *
   * @param string $mod_name
   *   Module machine moduleName.
   *
   * @return mixed|string
   *   Equivalent class moduleName.
   */
  protected static function modToClass($mod_name) {
    $class_name = str_replace('_', ' ', $mod_name);
    $class_name = ucwords($class_name);
    $class_name = str_replace(' ', '', $class_name);

    return "{$class_name}Module";
  }

  /**
   * Throw a watchdog exception.
   *
   * @param \Exception $e
   *   The exception to report.
   */
  protected function watchdogException(Exception $e) {
    watchdog_exception('drupaloop', $e);
  }

}

/**
 * Exception handler for missing classes.
 */
class DrupalOopMissingClassException extends Exception {}
