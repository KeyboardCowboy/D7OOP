<?php
/**
 * @file
 * Contains \DrupalFilterTest.
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/mocks/MockDrupaloopFilter.php';

/**
 * Test DrupalFilter base class functionality.
 */
class DrupalFilterTest extends TestCase {
  // Define some dummy data.
  private $filter = array(
    'name' => 'mock_drupaloop',
    'settings' => array(),
  );
  private $format = array();
  private $langcode = 'en';
  private $cache = TRUE;
  private $cache_id = 'cacheid';


  /**
   * Test that the info array is being properly constructed.
   */
  public function testInfo() {
    $filters = array();

    MockDrupaloopFilter::load()->addFilter($filters);

    $this->assertArrayHasKey('mock_drupaloop', $filters, 'Filter machine name is not parsing properly.');

    $this->assertEquals('Test Filter', $filters['mock_drupaloop']['title'], 'Filter info not merging properly.');
    $this->assertEquals('Test description.', $filters['mock_drupaloop']['description'], 'Filter info not merging properly.');

    // Callbacks should not be overridden.
    $this->assertEquals('_drupaloop_filter_prepare', $filters['mock_drupaloop']['prepare callback'], 'Filter info not merging properly.');
    $this->assertEquals('_drupaloop_filter_process', $filters['mock_drupaloop']['process callback'], 'Filter info not merging properly.');
    $this->assertEquals('_drupaloop_filter_settings', $filters['mock_drupaloop']['settings callback'], 'Filter info not merging properly.');
    $this->assertEquals('_drupaloop_filter_tips', $filters['mock_drupaloop']['tips callback'], 'Filter info not merging properly.');

    $this->assertEquals(0, $filters['mock_drupaloop']['weight'], 'Filter info not merging properly.');
    $this->assertEquals(1, $filters['mock_drupaloop']['default settings']['test_setting'], 'Filter info not merging properly.');
    $this->assertTrue($filters['mock_drupaloop']['cache'], 'Filter info not merging properly.');
  }

  /**
   * Test the prepare and process callback mapping.
   */
  public function testPrepareAndProcess() {
    $text = 'Here is a test string, I made it myself.';

    $filtered_text = _drupaloop_filter_prepare($text, (object) $this->filter, (object) $this->format, $this->langcode, $this->cache, $this->cache_id);
    $this->assertEquals('Here is a test string I made it myself', $filtered_text);

    $filtered_text = _drupaloop_filter_process($filtered_text, (object) $this->filter, (object) $this->format, $this->langcode, $this->cache, $this->cache_id);
    $this->assertEquals('HERE IS A TEST STRING I MADE IT MYSELF', $filtered_text);
  }

  /**
   * Test the tips callback mapping.
   */
  public function testTips() {
    $tips = _drupaloop_filter_tips((object) $this->filter, (object) $this->format, FALSE);
    $this->assertEquals('Short tip.', $tips);

    $tips = _drupaloop_filter_tips((object) $this->filter, (object) $this->format, TRUE);
    $this->assertEquals('This is a much longer tip.', $tips);
  }

  /**
   * Test the settings form callback mapping.
   */
  public function testSettingsForm() {
    $form = array();
    $form_state = array();
    $defaults = array();
    $filters = array();

    $form = _drupaloop_filter_settings($form, $form_state, (object) $this->filter, (object) $this->format, $defaults, $filters);

    $this->assertEquals(1, count($form));
    $this->assertArrayHasKey('test_field', $form);
  }

}
