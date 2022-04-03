<?php
namespace Test\GollumSF\UrlTokenizerBundle\Integration\Controller;

use GollumSF\UrlTokenizerBundle\Exception\ExpiredTokentHttpException;
use GollumSF\UrlTokenizerBundle\Exception\InvalidTokentHttpException;

class ValidateControllerTest extends AbstractControllerTest {
	
	public function provideValidate() {
		return [
			[ '/generate', '/validate?' ],
			[ '/generate-fullurl', '/validate-fullurl?' ],
			[ '/generate-no-fullurl', '/validate-no-fullurl?' ],
			[ '/generate-key', '/validate-key?' ],
			[ '/generate-lifetime', '/validate-lifetime?' ],
		];
	}

	/**
	 * @dataProvider provideValidate
	 */
	public function testValidateSuccess($generate, $validate) {
		$client = $this->getClient();

		$client->request('GET', $generate);
		$response = $client->getResponse();
		$content = $response->getContent();
		
		$this->assertEquals($response->getStatusCode(), 200);
		$this->assertStringContainsString($validate, $content);

		$client->request('GET', $content);
		$response = $client->getResponse();
		
		$this->assertEquals($response->getStatusCode(), 200);
		$this->assertEquals($response->getContent(), 'good');
	}

	/**
	 * @dataProvider provideValidate
	 */
	public function testValidateKoToken($generate, $validate) {

		$client = $this->getClient();

		$client->request('GET', $generate);
		$response = $client->getResponse();
		$content = $response->getContent();

		$this->assertEquals($response->getStatusCode(), 200);
		$this->assertStringContainsString($validate, $content);

		$content .= '&add_param=value';
		$client->request('GET', $content);
		$response = $client->getResponse();
		
		$json = json_decode($response->getContent(), true);

		$this->assertIsArray($json);
		$this->assertEquals($json['class'], InvalidTokentHttpException::class);
		$this->assertEquals($response->getStatusCode(), 403);
	}


	public function testValidateKoLifetime() {

		$client = $this->getClient();

		$client->request('GET', '/generate-lifetime-ko');
		$response = $client->getResponse();
		$content = $response->getContent();

		$this->assertEquals($response->getStatusCode(), 200);
		$this->assertStringContainsString('/validate-lifetime-ko?', $content);

		sleep(1);
		
		$client->request('GET', $content);
		$response = $client->getResponse();

		$json = json_decode($response->getContent(), true);

		$this->assertIsArray($json);
		$this->assertEquals($json['class'], ExpiredTokentHttpException::class);
		$this->assertEquals($response->getStatusCode(), 403);
	}
	
}
