<?php
/**
 * @file
 * Contains \DynamicClass.
 */

/**
 * Common processing methods for classifying Drupal things.
 */
trait DynamicClass {
  /**
   * Build the class name for an child class.
   *
   * All classes leveraging this system must follow this naming convention of
   * converting the machine name of the object from snake_case to CamelCase and
   * appending the base object type as defined in the parent class.
   *
   * @param string $machine_name
   *   Drupal's machine name for the object.
   *
   * @return mixed|string
   *   Equivalent class moduleName.
   */
  public static function buildClassName($machine_name) {
    $base = static::classBase();

    $machine_name = str_replace('_', ' ', $machine_name);
    $machine_name = ucwords(strtolower($machine_name));
    $machine_name = str_replace(' ', '', $machine_name);

    return "{$machine_name}{$base}";
  }

  /**
   * Load an module object from memory so it doesn't have to be reloaded.
   *
   * @param string $key
   *   Allow multiple instances of the same class by providing a key.
   *
   * @return static
   *   A module object.
   */
  public static function load($key = 'none') {
    static $instance;

    if (!isset($instance[$key])) {
      $instance[$key] = new static();
    }

    return $instance[$key];
  }

  /**
   * Derive a machine name from a class.
   */
  protected function setMachineName() {
    // Split the current class on Caps.
    $pieces = preg_split('/(?=[A-Z])/', get_called_class());

    // First piece will be empty and last piece should be the class base, so
    // throw those out.
    array_shift($pieces);
    array_pop($pieces);

    $this->machineName = strtolower(implode('_', $pieces));
  }

  /**
   * Get the machine name for this object.
   *
   * @return string
   *   The machine name.
   */
  public function getMachineName() {
    return $this->machineName;
  }

}

/**
 * Interface for creating abstract, dynamic classes.
 */
interface DynamicClassInterface {
  /**
   * Get the base class suffix to be used for new object types.
   *
   * Ex. For DrupalModule it is 'Module,' so subclasses should be names
   *   ModuleNameModule.
   *
   * @return string
   *   The object base name.
   */
  public static function classBase();
}
