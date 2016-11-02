<?php
/**
 * @file
 * Contains \DynamicClass.
 */

/**
 * Common processing methods for classifying Drupal things.
 */
abstract class DynamicClass {
  // The machine name for the class (modulename_delta).
  protected $machineName;

  /**
   * DynamicClass constructor.
   */
  public function __construct() {
    $this->setMachineName();
  }

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
   *
   * @throws \DrupalOopMissingClassException
   *   If the class name constructed does not exist.
   */
  public static function getClass($machine_name) {
    $base = static::classBase();

    $machine_name = str_replace('_', ' ', $machine_name);
    $machine_name = ucwords(strtolower($machine_name));
    $machine_name = str_replace(' ', '', $machine_name);

    $class = "{$machine_name}{$base}";

    if (!class_exists($class)) {
      throw new DrupalOopMissingClassException(t("Unable to load class @name", array('@name' => $class)));
    }
    else {
      return $class;
    }
  }

  /**
   * Load an module object from memory so it doesn't have to be reloaded.
   *
   * @param string $machine_name
   *   Instantiate an object by passing its machine name into static::load().
   *
   * @return static
   *   A module object.
   */
  public static function load($machine_name = NULL) {
    static $instance;

    // Instantiate an object by its machine name.
    if (!empty($machine_name)) {
      try {
        $class = static::getClass($machine_name);
        return $class::load();
      }
      catch (DrupalOopMissingClassException $e) {
        watchdog_exception('drupaloop', $e);
      }
    }
    else {
      $instance = new static();
    }

    return $instance;
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

  /**
   * Derive a machine name from a class.
   *
   * Machine names are [ModuleName][Delta] (without the class base name).
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
