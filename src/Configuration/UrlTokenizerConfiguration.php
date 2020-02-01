<?php

namespace GollumSF\UrlTokenizerBundle\Configuration;

class UrlTokenizerConfiguration implements UrlTokenizerConfigurationInterface {

	/** @var string */
	private $secret;
	
	/** @var string */
	private $algo;

	public function __construct(
		string $secret,
		string $algo
	) {
		$this->secret = $secret;
		$this->algo = $algo;
	}

	public function getSecret(): string {
		return $this->secret;
	}

	public function getAlgo(): string {
		return $this->algo;
	}
}