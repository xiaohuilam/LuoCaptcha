<?php namespace Luosimao\Captcha\Utils\Contracts;

use \Luosimao\Captcha\Utils\LuoCaptchaRequestInterface;
use \Luosimao\Captcha\Utils\LuoCaptchaException;
use \GuzzleHttp\Client;

interface LuoCaptcha{
    public function display($name = null, array $attributes = []);
    public function script();
    public function verify($response, $clientIp = null);
}
