<?php
namespace GollumSF\UrlTokenizerBundle\Tokenizer;

use GollumSF\UrlTokenizerBundle\Calendar\Calendar;
use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfigurationInterface;

/**
 * Tokeniser
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class Tokenizer implements TokenizerInterface {
	
	/**
	 * @var UrlTokenizerConfigurationInterface
	 */
	private $configuration;
	
	/** @var Calendar */
	private $calendar;
	
	public function __construct(
		UrlTokenizerConfigurationInterface $configuration,
		Calendar $calendar = null
	) {
		$this->configuration = $configuration;
		$this->calendar = $calendar ? $calendar : new Calendar();
	}
	
	/**
	 * Generate tokens from an URL
	 */
	public function generateToken(string $url, bool $fullUrl = null, string $key = NULL): string {
		$baseUrl = '';
		$fullUrl = $fullUrl === null ? $this->configuration->getDefaultFullUrl() : $fullUrl;
		if ($fullUrl === true) {
			$baseUrl = $this->getQueryParameters($url)['baseUrl'].' ';
		}
		return hash_hmac($this->configuration->getAlgo(), $baseUrl.$this->getSortedQuery($url), $key ? $key : $this->configuration->getSecret());
	}
	
	/**
	 * Generate an URL with its token from an URL without one
	 */
	public function generateUrl(string $url, bool $fullUrl = null, ?string $key = NULL): string {

		$tokenQueryName = $this->configuration->getTokenQueryName();
		$tokenTimeQueryName = $this->configuration->getTokenTimeQueryName();
		
		$separator = (strpos($url, '?') === false) ? '?' : '&';
		$url .= $separator.$tokenTimeQueryName.'='.$this->calendar->time();
		
		$token = $this->generateToken($url, $fullUrl, $key);
		
		return $url.'&'.$tokenQueryName.'='.rawurlencode($token);
	}
	
	/**
	 * Remove Tokens from URL
	 */
	public function removeToken(string $url): string {

		$tokenQueryName = $this->configuration->getTokenQueryName();
		
		$arParams = $this->getQueryParameters($url);
		$baseUrl = $arParams["baseUrl"];
		$listParams = $arParams["listParams"];
		
		$return = "";
		$first = true;
		foreach ($listParams as $key => $arParams) {
			$glu = "&";
			if ($first) {
				$glu = "?";
			}
			if ($arParams[0] !== $tokenQueryName) {
				$return .= $glu.rawurlencode($arParams[0])."=".rawurlencode($arParams[1]);
				$first = false;
			}
		}
		
		return $baseUrl.$return;
	}
	
	
	/**
	 * Retrieve token from an url
	 */
	public function getToken(string $url): ?string {
		$tokenQueryName = $this->configuration->getTokenQueryName();
		return $this->extractParameterFromUrl($url, $tokenQueryName);
	}

	/**
	 * Retrieve token time from an url
	 */
	public function getTokenTime(string $url): ?int {
		$tokenTimeQueryName = $this->configuration->getTokenTimeQueryName();
		$value = $this->extractParameterFromUrl($url, $tokenTimeQueryName);
		return $value ? (int)$value : null;
	}

	protected function extractParameterFromUrl(string $url, string $paramName): ?string {
		$arParams = $this->getQueryParameters($url);

		if(count($arParams) > 0) {
			$listParams = $arParams["listParams"];
		}

		if (isset($listParams) && count($listParams) > 0) {
			foreach ($listParams as $arrayParts) {
				if ($arrayParts[0] === $paramName) {
					return $arrayParts[1];
				}
			}
		}
		return NULL;
	}
	
	/**
	 * Sort query parameters and implode it
	 */
	protected function getSortedQuery(string $url): string {
		$params = $this->getQueryParameters($url)["listParams"];
		$keys = [];
		$out = [];
		foreach ($params as $param) {
			$keys[] = $param[0];
		}
		array_multisort($keys, SORT_ASC, $params);
		foreach ($params as $param) {
			$out[] = rawurlencode($param[0]).'='.rawurlencode($param[1]);
		}
		return implode("&", $out);
	}
	
	/**
	 * Get query parameters from url
	 */
	protected function getQueryParameters(string $url): array {
		$listParams  = [];
		$baseUrl     = $url;
		$queryParams = '';
		
		if (strpos($url, "?") !== false) {
			$baseUrl     = substr($url, 0, strpos($url, "?"));
			$queryParams = substr($url, strpos($url, "?")+1);
		}
		$list = explode("&", $queryParams);
		if ($list) {
			foreach ($list as $param) {
				if ($param) {
					$params = explode("=", $param);
					$params[0] = rawurldecode($params[0]);
					$params[1] = rawurldecode(array_key_exists(1, $params) ? $params[1] : '');
					$listParams[] = $params;
				}
			}
		}
		
		return [
			"baseUrl" => $baseUrl,
			"listParams" => $listParams
		];
	}
	
	
}