<?php

namespace GollumSF\UrlTokenizerBundle\Configuration;

interface UrlTokenizerConfigurationInterface {

	public const DEFAULT_SECRET = 'Default_S3cret_Must_be_Ch4nge!!!';
	public const DEFAULT_ALGO = 'sha256';

	public function getSecret(): string;
	public function getAlgo(): string;
}