<?php
/**
 * @file
 * Contains \DrupalBlock.
 */

require_once __DIR__ . '/DynamicClass.php';

/**
 * Objectify Drupal blocks.
 *
 * Child classes will follow the following pattern: [ModuleName][Delta]Block
 *
 * @see drupaloop_example_block_info()
 */
abstract class DrupalBlock extends DynamicClass implements DynamicClassInterface, ModuleDependencyInterface {
  use ModuleDependency;

  // Default block settings.
  private static $defaultInfo = array(
    'info' => "You Forgot to Label Your Block",
  );

  /**
   * {@inheritdoc}
   */
  final public static function classBase() {
    return 'Block';
  }

  /**
   * Add the block to the registry.
   *
   * @param array $blocks
   *   The filters to be returned in hook_filter_info().
   */
  public function addBlock(array &$blocks) {
    $blocks[$this->getDelta()] = $this->info() + self::$defaultInfo;
  }

  /**
   * Get the delta for a block.
   *
   * This is done by stripping the module_name off the machine_name.
   *
   * @return string
   *   The block delta.
   */
  public function getDelta() {
    $machine_name = $this->getMachineName();
    $module_name = $this->module()->getMachineName();

    if (strpos($machine_name, $module_name) === 0) {
      return substr($machine_name, strlen($module_name) + 1);
    }
    else {
      return $machine_name;
    }
  }

  /**
   * Define the custom block info.
   *
   * Default values will fill in any omitted info.
   *
   * @return array
   *   The block info array.
   */
  abstract public function info();

  /**
   * Render the block output.
   *
   * @return string|array
   *   A render array or valid HTML string for the block.
   */
  abstract public function content();

  /**
   * Get the block title.
   *
   * @return string
   *   The block title.
   */
  public function subject() {
    return '';
  }

  /**
   * Get the block array as requested by hook_block_view().
   *
   * @return array
   *   The block view array.
   */
  public function view() {
    $block['subject'] = $this->subject();
    $block['content'] = $this->content();

    return $block;
  }

  /**
   * Create a block configuration form.
   *
   * @return array
   *   A form array.
   */
  public function form($block_form = array()) {
    $form = array();

    // Wrap the custom fields in a #tree so they can be automatically saved.
    if (!empty($block_form)) {
      $key = $this->formVariableKey();
      $form[$key] = $block_form;
      $form[$key]['#tree'] = TRUE;
    }

    return $form;
  }

  /**
   * Save values from the block configuration form.
   *
   * @param $values
   *   The values from the custom fields in the block configuration form.
   */
  public function save($values) {
    $key = $this->formVariableKey();

    // Automatically save the field values if grouped by key.
    if (isset($values[$key]) && is_array($values[$key])) {
      foreach ($values[$key] as $field => $value) {
        $var = $this->formFieldVar($field);
        $this->module()->varSet($var, $value);
      }
    }
  }

  /**
   * Get the value of a block config var.
   *
   * @param string $var
   *   The simple var name.  No prepended module name or delta.
   *
   * @return mixed
   *   The value of the block config var.
   */
  protected function getBlockVar($var) {
    return $this->module()->varGet($this->formFieldVar($var));
  }

  /**
   * Create a unique key that we can use to nest custom form values.
   *
   * @return string
   *   A unique module-delta key.
   */
  protected function formVariableKey() {
    return strtolower($this->getMachineName() . '_' . static::classBase());
  }

  /**
   * Prepare a block config var to be stored by the module as a system var.
   *
   * @param string $field
   *   The name of the config var field.
   *
   * @return string
   *   The prepared block var name.
   */
  protected function formFieldVar($field) {
    return strtolower($this->getDelta() . '_' . static::classBase() . '_' . $field);
  }

}
