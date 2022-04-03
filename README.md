# GollumSF Url Tokenizer

[![Build Status](https://github.com/GollumSF/url-tokenizer-bundle/actions/workflows/symfony_5.4.yml/badge.svg?branch=master)](https://github.com/GollumSF/url-tokenizer-bundle/actions)
[![Coverage](https://coveralls.io/repos/github/GollumSF/url-tokenizer-bundle/badge.svg?branch=master)](https://coveralls.io/github/GollumSF/url-tokenizer-bundle)
[![License](https://poser.pugx.org/gollumsf/url-tokenizer-bundle/license)](https://packagist.org/packages/gollumsf/url-tokenizer-bundle)
[![Latest Stable Version](https://poser.pugx.org/gollumsf/url-tokenizer-bundle/v/stable)](https://packagist.org/packages/gollumsf/url-tokenizer-bundle)
[![Latest Unstable Version](https://poser.pugx.org/gollumsf/url-tokenizer-bundle/v/unstable)](https://packagist.org/packages/gollumsf/url-tokenizer-bundle)
[![Discord](https://img.shields.io/discord/671741944149573687?color=purple&label=discord)](https://discord.gg/xMBc5SQ)

## Installation:

```shell
composer require gollumsf/url-tokenizer-bundle
 ```

### config/bundles.php
```php
return [
    // [ ... ]
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    GollumSF\UrlTokenizerBundle\GollumSFUrlTokenizerBundle::class => ['all' => true],
];
```

### config.yml

```yaml
gollum_sf_url_tokenizer:
    secret: Default_S3cret_Must_be_Ch4nge!!! # Default secret key for token MUST BE CHANGE
    default_full_url: false'                 # (optional, default: false) By default tokenise full url or only parameter
    algo: 'sha256'                           # (optional, default: "sha256') Algo for hash token. (must be in list returned by hash_hmac_algos())
    token_query_name: "t"                    # (optional, default: "t") Query token param name for url tokenized
    token_time_query_name: "d"               # (optional, default: "d") Query token time param name for url tokenized
```

## Usage

## Tokenize URL

```php
<?php

use GollumSF\UrlTokenizerBundle\Tokenizer\TokenizerInterface;

public function (TokenizerInterface $tokenizer) { // Inject service
    
    $url = 'http://www.mydomain.com?param1=a';
    
    // $url1Tokenised => http://www.mydomain.com?param1=a&t=THE_TOKENd=1580775131 (tokenize only parameter)
    $url1Tokenised = $tokenizer->generateUrl($url);
    
    // $url1Tokenised => http://www.mydomain.com?param1=a&t=THE_TOKENd=1580775131 (tokenize full url)
    $url1Tokenised = $tokenizer->generateUrl($url, true);
    
    // $url1Tokenised => http://www.mydomain.com?param1=a&t=THE_TOKENd=1580775131 (use custom secret)
    $url1Tokenised = $tokenizer->generateUrl($url, false, 'CUSTOM SECRET');

}
```
## Check URL tokenized

```php
<?php

use GollumSF\UrlTokenizerBundle\Checker\CheckerInterface;

public function (CheckerInterface $checker) { // Inject service
    
    $urlWithToken = 'http://www.mydomain.com?param1=a&t=THE_TOKEN&d=1580775131';
    
    // $result => true or false
    $result = $checker->checkToken($urlWithToken);
    
    // $result => true or false (use full url)
    $result = $checker->checkToken($urlWithToken, true);
    
    // $result => true or false (use custom secret)
    $result = $checker->checkToken($urlWithToken, null, 'CUSTOM SECRET');

    // $result => true or false automatic use url from Master Request 
    $result = $checker->checkTokenMasterRequest();
    $result = $checker->checkTokenMasterRequest(true);
    $result = $checker->checkTokenMasterRequest(null, 'CUSTOM SECRET');

    // $result => is true if token is generate before 3600 second ago
    $result = $checker->checkTokenTime($urlWithToken, 3600);
    $result = $checker->checkTokenTimeMasterRequest(3600); // on Master Request

    // $result => result on valid Token and Time 
    $result = $checker->checkTokenAndTokenTime($urlWithToken, 3600);
    $result = $checker->checkTokenAndTokenTimeMasterRequest(3600); // on Master Request

}
```

## Use annotation

Use `ValidToken` annotation for restrain access at action.

```php
<?php

use GollumSF\UrlTokenizerBundle\Annotation\ValidToken;
use GollumSF\UrlTokenizerBundle\Tokenizer\TokenizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


class GenerateController extends AbstractController {

	/**
	 * @Route("/generate")
	 */
	public function generate(TokenizerInterface $tokenizer) {
		return new Response($tokenizer->generateUrl(
			$this->generateUrl('validate', [ 'param' => 'value' ], RouterInterface::ABSOLUTE_URL)
		));
	}

	/**
	 * @Route("/validate", name="validate")
	 * @ValidToken()
	 */
	public function validate(TokenizerInterface $tokenizer) {
		return new Response('good');
	}

```

### Options

 - **fullUrl**: (boolean, default = null) If null use condifuration value. 
 - **key**: (string, default = null) If null use condifuration value.
 - **lifeTime**: (integer, default = null) If null don't check time. Is lifetime of token before creation
