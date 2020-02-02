<?php
namespace GollumSF\UrlTokenizerBundle\Reflection;

interface ControllerActionExtractorInterface {
	public function extractFromString($controllerAction): ?ControllerAction;
}