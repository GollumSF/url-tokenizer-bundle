<?php

namespace GollumSF\UrlTokenizerBundle\Annotation;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class ValidToken {

	const ALIAS_NAME = 'gsf_valid_token';

	private ?int $lifeTime;
	private ?bool $fullUrl;
	private ?string $key;

	public function __construct(
		?int $lifeTime = null,
		?bool $fullUrl = null,
		?string $key = null
	) {
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
}
