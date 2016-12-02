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
abstract class DrupalModule extends DynamicClass implements DynamicClassInterface {
  // Register variable names and their default values.
  protected $variables = array();

  // Allow for custom directory mapping.
  protected static $dirMap = array();

  /**
   * {@inheritdoc}
   */
  final public static function classBase() {
    return 'Module';
  }

  /**
   * Get the path to the module file.
   *
   * @param string $filename
   *   An optional filename to get the path for.
   *
   * @return string
   *   The module path.
   */
  public function path($filename = '') {
    static $path;

    // Get the path to this module.
    if (empty($path)) {
      $path = drupal_get_path('module', $this->machineName);
    }

    // Lookup the relative path to the provided filename.
    if (!empty($filename)) {
      $ext = $this->getFileExtension($filename);
      $ext_path = $this->getExtPath($ext);
      $return_path = "{$path}/{$ext_path}/{$filename}";
    }
    else {
      $return_path = $path;
    }

    return $return_path;
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
   * Load a block by its delta.
   *
   * A block's machine name is [ModuleName]_[Delta].  A module's machine name is
   * the name of the module.
   *
   * @param string $delta
   *   The delta of the block to get.
   *
   * @return DrupalBlock
   *   The DrupalBlock object.
   */
  public function loadBlock($delta) {
    $block_machine_name = "{$this->machineName}_{$delta}";
    return DrupalBlock::load($block_machine_name);
  }

  /**
   * Load a filter by it's machine_name.
   *
   * @param string $machine_name
   *   The machine_name of the filter to get.
   *
   * @return DrupalFilter
   *   The DrupalFilter object.
   */
  public function loadFilter($machine_name) {
    return DrupalFilter::load($machine_name);
  }

  /**
   * Attach a file to a render array.
   *
   * @param array $build
   *   The render array to attach to.
   * @param string $filename
   *   The name of the file within its like directory, including the extension.
   * @param array $options
   *   Options for the file attachment array.
   */
  public function attachFile(array &$build, $filename, array $options = array()) {
    // Determine whether this is a local or external file.
    $type = stripos($filename, 'http') === 0 ? 'external' : 'file';

    // Determine extension.
    $ext = $this->getFileExtension($filename);

    // Attach the file to the render array.
    $build['#attached'][$ext][] = array(
        'data' => $type === 'external' ? $filename : "{$this->path()}/{$ext}/{$filename}",
        'type' => $type,
      ) + $options;
  }

  /**
   * Attach JS settings to a render array.
   *
   * @param array $build
   *   The render array to attach to.
   * @param array $settings
   *   The settings.
   */
  public function attachJsSettings(array &$build, array $settings) {
    $build['#attached']['js'][] = array(
      'type' => 'setting',
      'data' => array(
        $this->getMachineName() => $settings,
      ),
    );
  }

  /**
   * Attach a custom library to a render array.
   *
   * @param array $build
   *   The render array to attach to.
   * @param string $library_name
   *   The library name as defined in hook_library().
   */
  public function attachLibrary(array &$build, $library_name) {
    $build['#attached']['library'][] = array(
      $this->getMachineName(),
      $library_name,
    );
  }

  /**
   * Attach an external library via the Libraries API.
   *
   * @param array $build
   *   The render array to attach to.
   * @param string $name
   *   The name of the library.
   */
  public function attachLibraryApi(array &$build, $name) {
    $build['#attached']['libraries_load'][] = array($name);
  }

  /**
   * Wrapper around watchdog function.
   *
   * @see watchdog()
   */
  public function watchdog($message, $variables = array(), $severity = WATCHDOG_NOTICE, $link = NULL) {
    watchdog(get_called_class(), $message, $variables, $severity, $link);
  }

  /**
   * Throw a watchdog exception.
   *
   * @param \Exception $e
   *   The exception to report.
   */
  public function watchdogException(Exception $e) {
    watchdog_exception(get_called_class(), $e);
  }

  /**
   * Get the directory for a given file extension.
   *
   * @param string $ext
   *   A file extension.
   *
   * @return string
   *   The directory where a file of this extension should be stored.
   */
  protected function getExtPath($ext) {
    return isset(static::$dirMap[$ext]) ? static::$dirMap[$ext] : $ext;
  }

  /**
   * Get the extension of a file.
   *
   * @param string $filename
   *   A full filename.
   *
   * @return string
   *   The file extension.
   */
  protected function getFileExtension($filename) {
    $info = new SplFileInfo($filename);
    return $info->getExtension();
  }

}

/**
 * Interface ModuleDependencyInterface.
 *
 * Required methods for classes that are owned by modules.
 */
interface ModuleDependencyInterface {
  /**
   * Register an object to its module.
   *
   * @return string
   *   The name of the module that owns the class.
   */
  public static function moduleName();

  /**
   * Set the module that owns an object.
   *
   * @param \DrupalModule $module
   *   A DrupalModule object or derivative.
   */
  public function setModule(DrupalModule $module);

  /**
   * Get the Drupal module object that owns this object.
   *
   * @return DrupalModule
   *   The Drupal module object that owns this object.
   */
  public function module();
}

/**
 * Custom functionality for classes that depend on modules to exits.
 */
trait ModuleDependency {
  // The Drupal module object that owns this class.
  private $module;

  /**
   * Override the default loader.
   *
   * This
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
      // Instantiate this object.
      if (!isset($instance)) {
        $instance = new static();
        $instance->setModule(DrupalModule::load(static::moduleName()));
      }
    }

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function setModule(DrupalModule $module) {
    $this->module = $module;
  }

  /**
   * {@inheritdoc}
   */
  public function module() {
    return $this->module;
  }

}

/**
 * Exception handler for missing classes.
 */
class DrupalOopMissingClassException extends Exception {}
