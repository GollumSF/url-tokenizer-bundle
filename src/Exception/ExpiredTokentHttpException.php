<?php
namespace GollumSF\UrlTokenizerBundle\Exception;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExpiredTokentHttpException extends AccessDeniedHttpException {
}