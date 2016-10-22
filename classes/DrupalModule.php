<?php
/**
 * @file
 * Contains \DrupalModule.
 */

require_once __DIR__ . '/DynamicClass.php';

/**
 * Base class for a Drupal module.
 *
 * Module class names must follow the pattern of converting the module name
 * from snake_case to CamelCase and appending 'Module' to it.
 *
 * Ex. Module 'awesome_sauce' has the class 'AwesomeSauceModule'
 */
abstract class DrupalModule implements DynamicClassInterface {
  use DynamicClass;

  // The Drupal machine name of the object.
  protected $machineName;

  // Register variable names and their default values.
  protected $variables = array();

  /**
   * Module constructor.
   */
  public function __construct() {
    $this->setMachineName();
  }

  /**
   * {@inheritdoc}
   */
  final public static function classBase() {
    return 'Module';
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
      $path = drupal_get_path('module', $this->machineName);
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
  public function varGet($var_name) {
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
    return "{$this->machineName}_{$var_name}";
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
