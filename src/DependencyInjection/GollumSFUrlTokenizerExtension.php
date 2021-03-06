<?php
namespace GollumSF\UrlTokenizerBundle\DependencyInjection;

use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfiguration;
use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class GollumSFUrlTokenizerExtension extends Extension {
	
	public function load(array $configs, ContainerBuilder $container) {
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . "/../Resources/config"));
		$loader->load("services.yml");
		$config = $this->processConfiguration(new Configuration(), $configs);

		$container
			->register(UrlTokenizerConfigurationInterface::class, UrlTokenizerConfiguration::class)
			->addArgument($config['secret'])
			->addArgument($config['default_full_url'])
			->addArgument($config['algo'])
			->addArgument($config['token_query_name'])
			->addArgument($config['token_time_query_name'])
		;
	}
	
}