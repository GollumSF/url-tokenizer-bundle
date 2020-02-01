<?php
namespace Test\GollumSF\RestDocBundle\DependencyInjection;

use GollumSF\UrlTokenizerBundle\Checker\CheckerInterface;
use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfigurationInterface;
use GollumSF\UrlTokenizerBundle\DependencyInjection\GollumSFUrlTokenizerExtension;
use GollumSF\UrlTokenizerBundle\Tokenizer\TokenizerInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class GollumSFUrlTokenizerExtensionTest extends AbstractExtensionTestCase {

	protected function getContainerExtensions(): array {
		return [
			new GollumSFUrlTokenizerExtension()
		];
	}
	
	public function testLoad() {
		$this->load();
		
		$this->assertContainerBuilderHasService(TokenizerInterface::class);
		$this->assertContainerBuilderHasService(CheckerInterface::class);
		$this->assertContainerBuilderHasService(UrlTokenizerConfigurationInterface::class);
	}

	public function providerLoadConfiguration() {
		return [
			[ [], UrlTokenizerConfigurationInterface::DEFAULT_SECRET, UrlTokenizerConfigurationInterface::DEFAULT_ALGO ],
			[ [ 'secret'=> 'SECRET', 'algo' => 'sha1' ], 'SECRET', 'sha1' ],
		];
	}

	/**
	 * @dataProvider providerLoadConfiguration
	 */
	public function testLoadConfiguration(
		$config,
		$secret,
		$algo
	) {
		$this->load($config);
		
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(UrlTokenizerConfigurationInterface::class, 0, $secret);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(UrlTokenizerConfigurationInterface::class, 1, $algo);
	}
}