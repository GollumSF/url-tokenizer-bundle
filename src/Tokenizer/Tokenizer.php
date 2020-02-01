<?php
namespace GollumSF\UrlTokenizerBundle\Tokenizer;

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
	
	/**
	 * Tokeniser constructor.
	 * @param string $keyPrivate
	 */
	public function __construct(UrlTokenizerConfigurationInterface $configuration) {
		$this->configuration = $configuration;
	}
	
	/**
	 * Generate tokens from an URL
	 *
	 * @param string $url
	 * @param boolean $fullmatch (optional)
	 * @param string $key (optional)
	 * @return mixed string
	 */
	public function generateToken(string $url, bool $fullmatch = false, string $key = NULL): string {
		$baseUrl = '';
		if ($fullmatch === true) {
			$baseUrl = $this->getQueryParameters($url)['baseUrl'].' ';
		}
		return hash_hmac("sha1", $baseUrl.$this->getSortedQuery($url), $key ? $key : $this->configuration->getSecret());
	}
	
	/**
	 * Generate an URL with its token from an URL without one
	 *
	 * @param string $url
	 * @param boolean $fullmatch (optional)
	 * @param string $key (optional)
	 * @return string
	 */
	public function generateUrl(string $url, bool $fullmatch = false, ?string $key = NULL): string {
		
		$token = $this->generateToken($url, $fullmatch, $key);
		$separator = (strpos($url, '?') === false) ? '?' : '&';
		
		return $url.$separator."t=".urlencode($token);
	}
	
	/**
	 * Remove Tokens from URL
	 */
	public function removeToken(string $url): string {
		
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
			if ($arParams[0] != "t") {
				$return .= $glu.urlencode($arParams[0])."=".urlencode($arParams[1]);
				$first = false;
			}
		}
		
		return $baseUrl.$return;
	}
	
	
	/**
	 * Retrieve token from an url
	 */
	public function getToken(string $url): ?string {
		
		$arParams = $this->getQueryParameters($url);
		
		if(count($arParams) > 0) {
			$listParams = $arParams["listParams"];
		}
		
		if (isset($listParams) && count($listParams) > 0) {
			foreach ($listParams as $arrayParts) {
				if ($arrayParts[0] == 't') {
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
			$out[] = urlencode($param[0]).'='.urlencode($param[1]);
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
					$params[0] = urldecode($params[0]);
					$params[1] = urldecode(array_key_exists(1, $params) ? $params[1] : '');
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