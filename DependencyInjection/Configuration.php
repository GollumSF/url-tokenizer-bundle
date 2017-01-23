<?php
namespace GollumSF\UrlTokenizerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class Configuration implements ConfigurationInterface {
	
	const DEFAULT_SECRET = 'Default_S3cret_Must_be_Ch4nge!!!';
	
	public function getConfigTreeBuilder() {
		
		$treeBuilder = new TreeBuilder();;
		$rootNode = $treeBuilder->root('gollum_sf_url_tokenizer');
		
		$rootNode
			->children()
			->scalarNode('secret')->defaultValue(self::DEFAULT_SECRET)->end()
			->end()
		;
		
		return $treeBuilder;
	}
}
