<?php
/**
 * @file
 * Object wrapper for organizing modules.
 */

/**
 * Class Module.
 */
class Module {
  // Module machine name.
  private $modName;

  // Path to the module.
  public $path;

  // Default directory registry for files.
  private $dirs = array(
    'admin' => 'admin',
    'inc' => 'includes',
    'classes' => 'classes',
    'tpl' => 'templates',
    'js' => 'js',
    'css' => 'css',
  );

  /**
   * Module constructor.
   *
   * @param string $mod_name
   *   The name of the module being instantiated.
   */
  public function __construct($mod_name) {
    $this->modName = $mod_name;
    $this->path = drupal_get_path('module', $mod_name);

    // @ToDo: Add ctools etc directories to the dir registry lookup.
  }

  /**
   * Fetch an module object from memory so it doesn't have to be reloaded.
   *
   * @param string $mod_name
   *   Module machine name.
   *
   * @return object
   *   A module object.
   */
  public static function fetch($mod_name = 'module') {
    // Attempt to load an existing module object from memory.
    $class_name = self::_modToClass($mod_name);
    $module = &drupal_static("ModuleClass:{$class_name}");

    // If we don't have a module object, create one.
    if (!$module && class_exists($class_name)) {
      $module = new $class_name();
    }

    return $module;
  }

  /**
   * Get the relative path for a specific file type.
   *
   * @param string $type
   *   The type of subdirectory to get.
   * @param bool $inc_module
   *   Include the module path.
   *
   * @return string
   *   The desired path if it exists.
   */
  public function getDir($type, $inc_module = FALSE) {
    $path = '';

    if ($inc_module && !empty($this->path)) {
      $path .= $this->path;
    }

    return isset($this->dirs[$type]) ? "{$path}/{$this->dirs[$type]}" : $path;
  }

  /**
   * Set the relative directory for a set of files.
   *
   * @param string $type
   *   Type of files.
   * @param string $val
   *   Directory name relative to the module root.
   */
  public function setDir($type, $val) {
    $this->dirs[$type] = $val;
  }

  /**
   * Get the full path to a module's file.
   *
   * @param string $filename
   *   A module's file name.
   * @param string $type
   *   An optional string describing the type of file, if it should not be
   *   gleaned from the file extension.
   *
   * @return string
   *   The path to the file.
   */
  public function getPath($filename, $type = NULL) {
    // If the type of file was not provided, try to get it from the file
    // extension.
    if (!$type && ($ext = $this->_getFileExt($filename))) {
      $type = $ext;
    }

    return $this->getDir($type, TRUE) . "/{$filename}";
  }

  /**
   * Attach a file or files to a render array.
   *
   * @param array $array
   *   A render array.
   * @param string|array $filename
   *   The name of the file to attach.
   */
  public function attachFile(array &$array, $filename) {
    if (is_array($filename)) {
      foreach ($filename as $_filename) {
        $this->_attachFile($array, $_filename);
      }
    }
    else {
      $this->_attachFile($array, $filename);
    }
  }

  /**
   * Helper function to attach a file to a render array.
   *
   * @param array $array
   *   A render array.
   * @param string $filename
   *   A file to attach.
   */
  private function _attachFile(array &$array, $filename) {
    $ext = self::_getFileExt($filename);
    $array['#attached'][$ext][] = $this->getPath($filename);
  }

  /**
   * Convert a module machine name to an appropriate class name.
   *
   * @param string $mod_name
   *   Module machine name.
   *
   * @return mixed|string
   *   Equivalent class name.
   */
  private static function _modToClass($mod_name) {
    $class_name = str_replace('_', ' ', $mod_name);
    $class_name = ucwords($class_name);
    $class_name = str_replace(' ', '', $class_name);

    return $class_name;
  }

  /**
   * Helper function to get a file's extension.
   *
   * @param string $filename
   *   A filename.
   *
   * @return string
   *   The file's extension.
   */
  private static function _getFileExt($filename) {
    return substr(strrchr($filename, "."), 1);
  }
}
