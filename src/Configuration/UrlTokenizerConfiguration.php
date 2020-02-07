<?php

namespace GollumSF\UrlTokenizerBundle\Configuration;

class UrlTokenizerConfiguration implements UrlTokenizerConfigurationInterface {

	/** @var string */
	private $secret;
	
	/** @var bool */
	private $defaultFullUrl;
	
	/** @var string */
	private $algo;

	/** @var string  */
	private $tokenQueryName;
	
	/** @var string  */
	private $tokenTimeQueryName;

	public function __construct(
		string $secret,
		bool $defaultFullUrl,
		string $algo,
		string $tokenQueryName,
		string $tokenTimeQueryName
	) {
		$this->secret = $secret;
		$this->defaultFullUrl = $defaultFullUrl;
		$this->algo = $algo;
		$this->tokenQueryName = $tokenQueryName;
		$this->tokenTimeQueryName = $tokenTimeQueryName;
	}

	public function getSecret(): string {
		return $this->secret;
	}
	
	public function getDefaultFullUrl(): bool {
		return $this->defaultFullUrl;
	}

	public function getAlgo(): string {
		return $this->algo;
	}

	public function getTokenQueryName(): string {
		return $this->tokenQueryName;
	}

	public function getTokenTimeQueryName(): string {
		return $this->tokenTimeQueryName;
	}
}