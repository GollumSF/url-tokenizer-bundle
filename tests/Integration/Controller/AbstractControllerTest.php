<?php

namespace Test\GollumSF\UrlTokenizerBundle\Integration\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use GollumSF\UrlTokenizerBundle\GollumSFUrlTokenizerBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractControllerTest extends BaseBundleTestCase {
	
	protected $projectPath = __DIR__ . '/../../ProjectTest';
	
	/** @var KernelInterface */
	private $kernel;

	protected function getBundleClass() {
		return GollumSFUrlTokenizerBundle::class;
	}

	protected function setUp(): void {
		parent::setUp();
		$_ENV['SHELL_VERBOSITY'] = 1;
		// Make all services public
		$this->addCompilerPass(new PublicServicePass('|GollumSF*|'));
	}

	protected function getKernel(): KernelInterface {
		if (!$this->kernel) {
			// Create a new Kernel
			$this->kernel = $this->createKernel();
	
			// Add some other bundles we depend on
			$this->kernel->addBundle(\Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class);
	
			$this->kernel->addCompilerPasses([ new PublicServicePass('|GollumSF*|') ]);
			
			// Add some configuration
			$this->kernel->addConfigFile($this->projectPath.'/Resources/config/config.yaml');
	
			// Boot the kernel.
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
}
