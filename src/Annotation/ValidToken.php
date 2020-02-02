<?php

namespace GollumSF\UrlTokenizerBundle\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class ValidToken {
	/** @var bool */
	public $fullMatch = false;
	
	/** @var string */
	public $key = null;
}