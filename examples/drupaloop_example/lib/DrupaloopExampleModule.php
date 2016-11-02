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

}
