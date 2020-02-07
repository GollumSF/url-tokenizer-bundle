<?php
namespace GollumSF\UrlTokenizerBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ExpiredTokentHttpException extends BadRequestHttpException {
}