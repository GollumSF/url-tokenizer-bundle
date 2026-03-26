<?php

namespace Test\GollumSF\UrlTokenizerBundle\Integration\Controller;

use GollumSF\UrlTokenizerBundle\GollumSFUrlTokenizerBundle;
use Nyholm\BundleTest\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractControllerTest extends TestCase {

	protected function getProjectPath(): string {
		return __DIR__ . '/../../ProjectTest';
	}

	/** @var KernelInterface */
	private $kernel;

	protected function setUp(): void {
		parent::setUp();
		$_ENV['SHELL_VERBOSITY'] = 1;
	}

	protected function getKernel(): KernelInterface {
		if (!$this->kernel) {
			$this->kernel = new TestKernel('test', true);
			$this->kernel->addTestBundle(GollumSFUrlTokenizerBundle::class);
			$this->kernel->setTestProjectDir(realpath(__DIR__ . '/../../..'));
			$this->kernel->addTestConfig($this->getProjectPath().'/Resources/config/config.yaml');
			$this->kernel->addTestRoutingFile($this->getProjectPath().'/Resources/config/routing.yaml');
			$this->kernel->boot();
		}
		return $this->kernel;
	}

	protected function getContainer(): ContainerInterface {
		return $this->getKernel()->getContainer();
	}

	protected function getClient(): AbstractBrowser {
		return $this->getContainer()->get('test.client');
	}

	protected function tearDown(): void {
		if ($this->kernel) {
			$this->kernel->shutdown();
			$this->kernel = null;
		}
		parent::tearDown();
	}
}
