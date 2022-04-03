<?php
namespace Test\GollumSF\UrlTokenizerBundle\IntegrationPhp8\Controller;

use Test\GollumSF\UrlTokenizerBundle\Integration\Controller\ValidateControllerTest as ValidateControllerTestBase;

/**
 * @requires PHP 8.0.0
 */
class ValidateControllerTest extends ValidateControllerTestBase {
	protected function getProjectPath(): string {
		return __DIR__ . '/../../ProjectTestPhp8';
	}
}
