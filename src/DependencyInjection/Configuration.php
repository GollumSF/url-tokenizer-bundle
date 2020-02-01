<?php
namespace GollumSF\UrlTokenizerBundle\DependencyInjection;

use GollumSF\UrlTokenizer\Configuration\UrlTokenizerConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class Configuration implements ConfigurationInterface {

	public function getConfigTreeBuilder() {
		
		$treeBuilder = new TreeBuilder();;
		$rootNode = $treeBuilder->root('gollum_sf_url_tokenizer');
		
		$rootNode
			->children()
			->scalarNode('secret')->defaultValue(UrlTokenizerConfigurationInterface::DEFAULT_SECRET)->end()
			->end()
		;
		
		return $treeBuilder;
	}
}
