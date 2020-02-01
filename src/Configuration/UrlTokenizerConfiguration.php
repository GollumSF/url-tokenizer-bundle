<?php

namespace GollumSF\UrlTokenizerBundle\Configuration;

class UrlTokenizerConfiguration implements UrlTokenizerConfigurationInterface {

	/** @var string */
	private $secret;

	public function __construct(
		string $secret
	) {
		$this->secret = $secret;
	}
	
	public function getSecret(): string {
		return $this->secret;
	}
}