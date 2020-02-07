<?php

namespace GollumSF\UrlTokenizerBundle\Configuration;

interface UrlTokenizerConfigurationInterface {

	public const DEFAULT_SECRET = 'Default_S3cret_Must_be_Ch4nge!!!';
	public const DEFAULT_DEFAULT_FULL_URL = false;
	public const DEFAULT_ALGO = 'sha256';
	public const DEFAULT_TOKEN_QUERY_NAME = 't';
	public const DEFAULT_TOKEN_TIME_QUERY_NAME = 'd';

	public function getSecret(): string;
	public function getDefaultFullUrl(): bool;
	public function getAlgo(): string;
	public function getTokenQueryName(): string;
	public function getTokenTimeQueryName(): string;
}