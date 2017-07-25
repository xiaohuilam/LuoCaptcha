<?php namespace \Luosimao\Captcha\Utils;

class LuoCaptchaException extends \Exception{
    const API_SERVER_FAIL = -500;
    const BAD_RESPONSE    = -20;
    const EMPTY_API_KEY   = -10;
    const EMPTY_RESPONSE  = -11;
    const ERROR_API_KEY   = -40;
    const ERR_MSG = [
        -500 => '验证码服务器挂了',
        -10  => 'API_KEY为空',
        -11  => '验证码为空',
        -20  => '验证码验证失败',
        -40  => 'API_KEY错误 请勿把使用SITE_KEY当做API_KEY使用',
    ];
    protected $data = [];

    public function __construct($message, $code, $data = null)
    {
        if($data != null) $this->data = $data;
        parent::__construct($message, $code);
    }

    public function getData()
    {
        return $this->data;
    }
}
