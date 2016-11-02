<?php
/**
 * @file
 * Contains \DrupaloopExampleWelcomeBlock.php.
 */

/**
 * Create a standard welcome block.
 */
class DrupaloopExampleWelcomeBlock extends DrupalBlock {
  /**
   * {@inheritdoc}
   */
  public static function moduleName() {
    return 'drupaloop_example';
  }

  /**
   * {@inheritdoc}
   */
  public function info() {
    return array(
      'info' => t("Welcome Block"),
      'cache' => DRUPAL_CACHE_PER_USER,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function subject() {
    return t("Welcome to Drupal!");
  }

  /**
   * {@inheritdoc}
   *
   * Display a custom greeting for the logged-in user.
   */
  public function content() {
    $greeting = $this->getBlockVar('greeting');

    return t("!greeting !name!", array('!greeting' => $greeting, '!name' => format_username($GLOBALS['user'])));
  }

  /**
   * {@inheritdoc}
   *
   * Collect a custom salutation.
   */
  public function form($form = array()) {
    $form['greeting'] = array(
      '#type' => 'textfield',
      '#title' => t("Greeting"),
      '#default_value' => $this->getBlockVar('greeting'),
    );

    return parent::form($form);
  }

}
