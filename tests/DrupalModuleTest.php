<?php
/**
 * @file
 * Contains \DrupalModuleTest.
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/DrupalModuleMockModule.php';

/**
 * Test the base DrupalModule class.
 */
class DrupalModuleTest extends TestCase {
  private $module;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->module = DrupalModule::load('drupal_module_mock');
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown() {
    $this->module->deleteAllVars();
    $this->module = NULL;
  }

  /**
   * Test for bad module class names.
   */
  public function testBadClass() {
    $this->expectException('DrupalOopMissingClassException');
    DrupalModule::load('non_existent');
  }

  /**
   * Test module to class name conversion.
   */
  public function testModToClass() {
    $this->assertEquals('SimpleModule', DrupalModule::modToClass('simple'));
    $this->assertEquals('OneUnderscoreModule', DrupalModule::modToClass('one_underscore'));
    $this->assertEquals('BadCamelCasingModule', DrupalModule::modToClass('bad_camelCasing'));
    $this->assertEquals('WhySpacesModule', DrupalModule::modToClass('why spaces'));

    // Don't try to accommodate every case.  Make the developer use the right
    // format.
    $this->assertEquals('Or-hyphensModule', DrupalModule::modToClass('or-hyphens'));
  }

  /**
   * Test that we are returning proper paths.
   */
  public function testPath() {
    $system_module = DrupalModule::load('system');
    $node_module = DrupalModule::load('node');

    $this->assertEquals('modules/system', $system_module->path(), 'Invalid path for System module.');
    $this->assertEquals('modules/node', $node_module->path(), 'Invalid path for Node module.');
  }

  /**
   * Test variable system.
   */
  public function testVars() {
    // Check the default value.
    $this->assertEquals('var1_value', $this->module->varGet(DrupalModuleMockModule::FIRST_VAR), 'Failed to load default value for registered var.');

    // Set a new value.
    $this->module->varSet(DrupalModuleMockModule::FIRST_VAR, 'new value');
    $this->module->varSet(DrupalModuleMockModule::SECOND_VAR, 'new value 2');
    $this->assertEquals('new value', $this->module->varGet(DrupalModuleMockModule::FIRST_VAR), 'Failed to set var properly.');
    $this->assertEquals('new value 2', $this->module->varGet(DrupalModuleMockModule::SECOND_VAR), 'Failed to set var properly.');

    // Delete one var.
    $this->module->varDel(DrupalModuleMockModule::FIRST_VAR);
    $this->assertEquals('var1_value', $this->module->varGet(DrupalModuleMockModule::FIRST_VAR), 'Failed to delete var properly.');
    $this->assertEquals('new value 2', $this->module->varGet(DrupalModuleMockModule::SECOND_VAR), 'Failed to delete var properly.');

    // Delete all vars.
    $this->module->varSet(DrupalModuleMockModule::FIRST_VAR, 'new value');
    $this->assertEquals('new value', $this->module->varGet(DrupalModuleMockModule::FIRST_VAR), 'Failed to set var properly.');

    $this->module->deleteAllVars();
    $this->assertEquals('var1_value', $this->module->varGet(DrupalModuleMockModule::FIRST_VAR), 'Failed to delete all vars properly.');
    $this->assertEquals('var2_value', $this->module->varGet(DrupalModuleMockModule::SECOND_VAR), 'Failed to delete all vars properly.');
  }

}

/**
 * Stub class to simulate the system module.
 */
class SystemModule extends DrupalModule {}


/**
 * Stub class to simulate the node module.
 */
class NodeModule extends DrupalModule {}
