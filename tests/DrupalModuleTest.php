<?php
/**
 * @file
 * Contains \DrupalModuleTest.
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/mocks/DrupalModuleMockModule.php';

/**
 * Test the base DrupalModule class.
 */
class DrupalModuleTest extends TestCase {
  /**
   * @var \DrupalModuleMockModule
   */
  private $module;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->module = DrupalModuleMockModule::load();
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown() {
    $this->module->deleteAllVars();
    $this->module = NULL;
  }

  /**
   * Ensure machine names are being properly extracted.
   */
  public function testGetMachineName() {
    // Make sure they are derived properly.
    $this->assertEquals('system', SystemModule::load()->getMachineName(), 'Machine name is not being derived properly.');
    $this->assertEquals('field_ui', FieldUiModule::load()->getMachineName(), 'Machine name is not being derived properly.');
    $this->assertEquals('awesome_sauce', AwesomeSauceMod::load()->getMachineName(), 'Machine name is not being derived properly.');
  }

  /**
   * Ensure class names are being properly constructed from machine names.
   */
  public function testBuildClassName() {
    $class = DrupalModule::getClass('system');
    if (!class_exists($class)) {
      $this->fail("Class name improperly formed.");
    }

    $class = DrupalModule::getClass('field_ui');
    if (!class_exists($class)) {
      $this->fail("Class name improperly formed.");
    }

    $this->expectException('DrupalOopMissingClassException');
    DrupalModule::getClass('awesome_sauce');
  }

  /**
   * Test that we are returning proper paths.
   */
  public function testPath() {
    $system_module = SystemModule::load();
    $node_module = FieldUiModule::load();

    $this->assertEquals('modules/system', $system_module->path(), 'Invalid path for System module.');
    $this->assertEquals('modules/field_ui', $node_module->path(), 'Invalid path for Field UI module.');
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

  /**
   * Test getting data from the info file.
   */
  public function testGetName() {
    static::assertEquals('Drupal Mock Module', $this->module->name());
  }

}

/**
 * Stub class to simulate the system module.
 */
class SystemModule extends DrupalModule {}


/**
 * Stub class to simulate the Field UI module.
 */
class FieldUiModule extends DrupalModule {}

/**
 * Stub class to simulate a poorly named class.
 */
class AwesomeSauceMod extends DrupalModule {}
