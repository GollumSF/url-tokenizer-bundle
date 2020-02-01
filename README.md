# GollumSF Url Tokenizer

[![Build Status](https://travis-ci.com/GollumSF/url-tokenizer-bundle.svg?branch=master)](https://travis-ci.com/GollumSF/url-tokenizer-bundle)
[![Coverage](https://coveralls.io/repos/github/GollumSF/url-tokenizer-bundle/badge.svg?branch=master)](https://coveralls.io/github/GollumSF/url-tokenizer-bundle)
[![License](https://poser.pugx.org/gollumsf/url-tokenizer-bundle/license)](https://packagist.org/packages/gollumsf/url-tokenizer-bundle)
[![Latest Stable Version](https://poser.pugx.org/gollumsf/url-tokenizer-bundle/v/stable)](https://packagist.org/packages/gollumsf/url-tokenizer-bundle)
[![Latest Unstable Version](https://poser.pugx.org/gollumsf/url-tokenizer-bundle/v/unstable)](https://packagist.org/packages/gollumsf/url-tokenizer-bundle)
[![Discord](https://img.shields.io/discord/671741944149573687?color=purple&label=discord)](https://discord.gg/xMBc5SQ)

## Installation:

```shell
composer require gollumsf/url-tokenizer-bundle
 ```


### AppKernel.php
```php
class AppKernel extends Kernel {
	
	public function registerBundles() {
		
		$bundles = [
			
			// [...] //
			
			new GollumSF\UrlTokenizerBundle\GollumSFUrlTokenizerBundle(),
			
			// [...] // 
		}
	}
}
```

### config.yml

```yml
gollum_sf_url_tokenizer:
    secret: Default_S3cret_Must_be_Ch4nge!!!  # Default secret key for token
```

## Usage

## Tokenize URL

```php
$tokenizer = $container->get('gsf_url_tokenizer.tokenizer');
$url = 'http://www.mydomain.com?param1=a';

$url1Tokenised = tokenizer->generateUrl($url); // $url1Tokenised => http://www.mydomain.com?param1=a&t=THE_TOKEN

```

## Check URL tokenized

```php
$checker = $container->get('gsf_url_tokenizer.ckecker');
$urlWithToken = http://www.mydomain.com?param1=a&t=THE_TOKEN';

$result = checker->checkToken($urlWithToken); // $result => true or false

```
