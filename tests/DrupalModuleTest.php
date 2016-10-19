<?php
/**
 * @file
 * Contains \DrupalModuleTest.
 */

use PHPUnit\Framework\TestCase;

/**
 * Test the base DrupalModule class.
 */
class DrupalModuleTest extends TestCase {
  /**
   * Test that we are returning proper paths.
   */
  public function testPath() {
    $system_module = DrupalModule::load('StubSystemModule');
    $node_module = DrupalModule::load('StubNodeModule');
    $fake_module = DrupalModule::load('StubFakeModule');

    $this->assertEquals('modules/system', $system_module->path(), 'Invalid path for System module.');
    $this->assertEquals('modules/node', $node_module->path(), 'Invalid path for Node module.');
    $this->assertNull($fake_module->path(), 'Invalid path for unnamed module.');
  }

}

/**
 * Stub class to simulate the system module.
 */
class StubSystemModule extends DrupalModule {
  protected $name = 'system';
}

/**
 * Stub class to simulate the node module.
 */
class StubNodeModule extends DrupalModule {
  protected $name = 'node';
}

/**
 * Stub class to simulate an unnamed module.
 */
class StubFakeModule extends DrupalModule {}
