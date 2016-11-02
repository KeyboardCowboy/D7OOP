<?php
/**
 * @file
 * Contains \DrupaloopExampleRemoveVowelsFilter.
 */

/**
 * Remove all vowels from text.
 */
class DrupaloopExampleDrupalCapFilter extends DrupalFilter implements DrupalModuleDependencyInterface {
  use DrupaloopExampleTrait;

  /**
   * {@inheritdoc}
   */
  public function info() {
    return array(
      'title' => t("Example: Capitalize Drupal!"),
      'description' => t("Capitalize all instances of Drupal."),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $filter, $format, $langcode, $cache, $cache_id) {
    return preg_replace('/\bdrupal\b/', 'Drupal', $text);
  }

}
