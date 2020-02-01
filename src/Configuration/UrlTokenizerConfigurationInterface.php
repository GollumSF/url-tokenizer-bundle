<?php

namespace GollumSF\UrlTokenizerBundle\Configuration;

interface UrlTokenizerConfigurationInterface {

	public const DEFAULT_SECRET = 'Default_S3cret_Must_be_Ch4nge!!!';

	public function getSecret(): string;
}