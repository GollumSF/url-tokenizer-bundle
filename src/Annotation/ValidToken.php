<?php

namespace GollumSF\UrlTokenizerBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class ValidToken extends ConfigurationAnnotation {
	
	const ALIAS_NAME = 'gsf_valid_token';
	
	/** @var bool */
	private $fullUrl = null;

	/** @var string */
	private $key = null;

	/** @var int */
	private $lifeTime = null;
	
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