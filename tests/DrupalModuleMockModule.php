<?php
/**
 * @file
 * Contains \DrupalModuleMockModule.
 */

require_once __DIR__ . '/../classes/DrupalModule.php';

/**
 * Mock class for testing DrupalModule class.
 */
class DrupalModuleMockModule extends DrupalModule {
  const FIRST_VAR = 'var1';
  const SECOND_VAR = 'var2';

  protected $variables = array(
    'var1' => 'var1_value',
    'var2' => 'var2_value',
  );

  /**
   * {@inheritdoc}
   */
  public static function modToClass($mod_name) {
    return parent::modToClass($mod_name);
  }

  /**
   * {@inheritdoc}
   */
  public function varGet($var_name) {
    return parent::varGet($var_name);
  }

}
