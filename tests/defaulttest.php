<?php

class DefaultTest extends Phpr_UFOTestCase {

	public function testConfig() {
		$this->assertInstanceOf('Phpr_Config', Phpr::$config);
		$this->assertNotNull(Phpr::$config->get('TIMEZONE', null));
		$this->assertNotNull(Phpr::$config->get('DB_CONNECTION', null));
	}

	public function testSecurity() {
		$this->assertInstanceOf('Core_Security', Phpr::$security);
		$this->assertNull(Phpr::$security->getUser());
		$this->assertNull(Phpr::$security->getUserId());
	}

	public function testDefaultController() {
		$this->assertTrue(Phpr::$classLoader->load(Phpr_Response::controllerApplication));
	}

	public function testWritableDirs() {
		$this->assertTrue(is_writable(PATH_APP . "/temp"));
		$this->assertTrue(is_writable(PATH_APP . "/uploaded"));
	}

	/**
	 * @depends testConfig
	 */
	public function testWritableDirsExtended() {
		if (Phpr::$config->get('SITE_ENVIRONMENT') != 'production') {
			$this->assertTrue(is_writable(PATH_APP . "/controllers"));
			$this->assertTrue(is_writable(PATH_APP . "/init"));
			$this->assertTrue(is_writable(PATH_APP . "/views"));
		} else {
			$this->markTestSkipped();
		}
	}
}