<?php
namespace GollumSF\UrlTokenizerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * GollumSFUrlTokeniserExtension
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class GollumSFUrlTokenizerExtension extends Extension {
	
	public function load(array $configs, ContainerBuilder $container) {
		
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);
		
		$this->applyConfigToParameter($container, 'gsf_url_tokenizer.configurations', $config);
		
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . "/../Resources/config"));
		$loader->load("services.yml");
	}
	
	protected function applyConfigToParameter(ContainerBuilder $container, $prefix, $config) {
		if (is_array($config)) {
			foreach ($config as $key => $value) {
				$this->applyConfigToParameter($container, $prefix.'.'.$key, $value);
			}
		} else {
			$container->setParameter($prefix, $config);
		}
	}
}