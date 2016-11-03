<?php
/**
 * @file
 * Contains \DrupalOopExampleModule.
 */

/**
 * Custom functionality for the DrupalOOP module.
 */
class DrupaloopExampleModule extends DrupalModule {
  // Register local system variables.
  protected $variables = array(
    'demo_message' => "Drupaloop Example Module is enabled!",
    'welcome_block_greeting' => "Hello",
  );

  public function customModuleMethod() {}

}

/**
 * Class DrupaloopExampleTrait.
 *
 * Recommended implementation for modules that implement multiple Drupaloop
 * objects.
 */
trait DrupaloopExampleTrait {
  /**
   * {@inheritdoc}
   */
  public static function moduleName() {
    return 'drupaloop_example';
  }

  /**
   * {@inheritdoc}
   *
   * Overriding this method to add our own return annotation for IDE tooltip
   * purposes.  If you don't use an IDE with autocomplete and tooltips, you
   * don't need this extra method.
   *
   * @return \DrupaloopExampleModule
   */
  public function module() {
    return parent::module();
  }

}
