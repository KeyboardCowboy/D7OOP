<?php
/**
 * @file
 * Contains \DrupalOopExampleModule.
 */

/**
 * Custom functionality for the DrupalOOP module.
 */
class DrupaloopExampleModule extends DrupalModule implements DrupalModuleInterface {
  // Register local system variables.
  protected $variables = array(
    'demo_message' => "Drupaloop Example Module is enabled!",
    'welcome_block_greeting' => "Hello",
  );

}

/**
 * Class DrupaloopExampleTrait.
 *
 * Common functionality for DrupalOOP Example classes.
 */
trait DrupaloopExampleTrait {
  /**
   * {@inheritdoc}
   */
  public static function moduleName() {
    return 'drupaloop_example';
  }

}
