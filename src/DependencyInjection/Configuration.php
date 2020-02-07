<?php
namespace GollumSF\UrlTokenizerBundle\DependencyInjection;

use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class Configuration implements ConfigurationInterface {

	public function getConfigTreeBuilder() {

		$treeBuilder = new TreeBuilder('gollum_sf_url_tokenizer');

		$treeBuilder->getRootNode()->children()
			->scalarNode('secret')->defaultValue(UrlTokenizerConfigurationInterface::DEFAULT_SECRET)->end()
			->scalarNode('default_full_url')->defaultValue(UrlTokenizerConfigurationInterface::DEFAULT_DEFAULT_FULL_URL)->end()
			->enumNode('algo')->values(hash_hmac_algos())->defaultValue(UrlTokenizerConfigurationInterface::DEFAULT_ALGO)->end()
			->scalarNode('token_query_name')->defaultValue(UrlTokenizerConfigurationInterface::DEFAULT_TOKEN_QUERY_NAME)->end()
			->scalarNode('token_time_query_name')->defaultValue(UrlTokenizerConfigurationInterface::DEFAULT_TOKEN_TIME_QUERY_NAME)->end()
		->end();
		
		return $treeBuilder;
	}
}
