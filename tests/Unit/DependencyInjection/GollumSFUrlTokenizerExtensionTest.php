<?php
namespace Test\GollumSF\UrlTokenizerBundle\Unit\DependencyInjection;

use GollumSF\UrlTokenizerBundle\Checker\CheckerInterface;
use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfigurationInterface;
use GollumSF\UrlTokenizerBundle\DependencyInjection\GollumSFUrlTokenizerExtension;
use GollumSF\UrlTokenizerBundle\EventSubscriber\ValidTokenSubscriber;
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
		$this->assertContainerBuilderHasService(ValidTokenSubscriber::class);
	}

	public function providerLoadConfiguration() {
		return [
			[ 
				[],
				UrlTokenizerConfigurationInterface::DEFAULT_SECRET,
				UrlTokenizerConfigurationInterface::DEFAULT_DEFAULT_FULL_URL,
				UrlTokenizerConfigurationInterface::DEFAULT_ALGO,
				UrlTokenizerConfigurationInterface::DEFAULT_TOKEN_QUERY_NAME,
				UrlTokenizerConfigurationInterface::DEFAULT_TOKEN_TIME_QUERY_NAME
			],
			[ 
				[
					'secret'=> 'SECRET',
					'default_full_url' => true,
					'algo' => 'sha1', 
					'token_query_name' => 'TTT', 
					'token_time_query_name' => 'DDD'
				], 'SECRET', true, 'sha1', 'TTT', 'DDD'
			],
		];
	}

	/**
	 * @dataProvider providerLoadConfiguration
	 */
	public function testLoadConfiguration(
		$config,
		$secret,
		$defaultFullUrl,
		$algo,
		$tokenName,
		$tokenTimeName
	) {
		$this->load($config);
		
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(UrlTokenizerConfigurationInterface::class, 0, $secret);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(UrlTokenizerConfigurationInterface::class, 1, $defaultFullUrl);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(UrlTokenizerConfigurationInterface::class, 2, $algo);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(UrlTokenizerConfigurationInterface::class, 3, $tokenName);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(UrlTokenizerConfigurationInterface::class, 4, $tokenTimeName);
	}
}