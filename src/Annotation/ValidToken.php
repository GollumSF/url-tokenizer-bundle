<?php

namespace GollumSF\UrlTokenizerBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class ValidToken implements ConfigurationInterface {
	
	const ALIAS_NAME = 'gsf_valid_token';
	
	/** @var int */
	private $lifeTime = null;
	
	/** @var bool */
	private $fullUrl = null;
	
	/** @var string */
	private $key = null;
	
	/**
	 * @param int $lifeTime
	 * @param bool $fullUrl
	 * @param string $key
	 */
	public function __construct(
		$lifeTime = null,
		$fullUrl = null,
		$key = null
	)
	{
		if (is_array($lifeTime)) {
			trigger_deprecation('gollumsf/url_tokenizer_bundle', '4.0', 'Use native php attributes for %s', __CLASS__);
			$this->lifeTime = isset($lifeTime['lifeTime']) ? $lifeTime['lifeTime'] : null;
			$this->fullUrl = isset($lifeTime['fullUrl']) ? $lifeTime['fullUrl'] : null;
			$this->key = isset($lifeTime['key']) ? $lifeTime['key'] : null;
			
			return;
		}
		$this->lifeTime = $lifeTime;
		$this->fullUrl = $fullUrl;
		$this->key = $key;
	}
	
	/////////////
	// Getters //
	/////////////

	public function isFullUrl(): ?bool {
		return $this->fullUrl;
	}

	public function getKey(): ?string {
		return $this->key;
	}

	public function getLifeTime(): ?int {
		return $this->lifeTime;
	}
	
	public function getAliasName() {
		return self::ALIAS_NAME;
	}

	public function allowArray() {
		return false;
	}
	
	/////////////
	// Setters //
	/////////////

	public function setFullUrl(?bool $fullUrl): self {
		$this->fullUrl = $fullUrl;
		return $this;
	}

	public function setKey(?string $key): self {
		$this->key = $key;
		return $this;
	}

	public function setLifeTime(?int $lifeTime): self {
		$this->lifeTime = $lifeTime;
		return $this;
	}
}
