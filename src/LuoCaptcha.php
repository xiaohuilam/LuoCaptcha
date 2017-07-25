<?php namespace Luosimao\Captcha;

use \Luosimao\Captcha\Utils\LuoCaptchaRequestInterface;
use \Luosimao\Captcha\Utils\LuoCaptchaException;
use \GuzzleHttp\Client;

class LuoCaptcha{

    /* -----------------------------------------------------------------------------------------------
     | 常量
     | ---------------------------------------------------------------------------------------------- */

    const CLIENT_URL   = 'https://captcha.luosimao.com/static/dist/api.js';

    const VERIFY_URL   = 'https://captcha.luosimao.com/api/site_verify';

    const CAPTCHA_NAME = 'luotest_response';

    /* -----------------------------------------------------------------------------------------------
     | 属性
     | ---------------------------------------------------------------------------------------------- */

    /**
     * 站点key
     *
     * @var string
	 */
    private $siteKey;

    /**
     * 私钥key
     *
     * @var string
	 */
    private $secret;


	/**
	 * 是否加载过js
	 *
	 * @var boolean
	 */
    protected static $scriptLoaded = false;

    /**
     * HTTP Request
     *
     * @var \Xiaohuilam\LuoCaptcha\Utils\LuoCaptchaRequestInterface
     */
    protected $request;

    public function __construct($siteKey = null, $secret = null)
    {
        if(!$siteKey) $siteKey = getenv('NOCAPTCHA_SITEKEY');
        if(!$secret) $secret = getenv('NOCAPTCHA_SECRET');

        $this->setSecret($secret);
        $this->setSiteKey($siteKey);
    }

    public function display($name = null, array $attributes = [])
    {
        return '<div class="l-captcha" data-site-key="'.$this->getSiteKey().'" data-width="100%;"></div>';
    }

    public static function script()
    {
        $script = '';
        if ( ! self::$scriptLoaded) {
            $script = '<script src="'.self::CLIENT_URL.'" async defer></script>';
            self::$scriptLoaded = true;
        }
        return $script;
    }

    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes CAPTCHA
     * test using a PSR-7 ServerRequest object.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     *
     * @return bool
     */
    public function verifyRequest(ServerRequestInterface $request)
    {
        $ip_header = getenv('REQUEST_IP_HEADER', 'REMOTE_ADDR');
        $body   = $request->getParsedBody();
        $server = $request->getServerParams();
        $response = isset($body[self::CAPTCHA_NAME]) ? $body[self::CAPTCHA_NAME] : '';
        $remoteIp = isset($server[$ip_header]) ? $server[$ip_header] : null;

        return $this->verify($response, $remoteIp);
    }

    public function verify($response, $clientIp = null)
    {
        if (!$response)
            $response = $this->request->input[self::CAPTCHA_NAME];

        if (!$response) 
            throw new LuoCaptchaException(LuoCaptchaException::ERR_MSG[LuoCaptchaException::BAD_RESPONSE], LuoCaptchaException::BAD_RESPONSE);

        $response = $this->sendVerifyRequest([
            'api_key'  => $this->secret,
            'response' => $response,
            'remoteip' => $clientIp
        ]);
        if($response['error'] != 0)
            throw new LuoCaptchaException(LuoCaptchaException::ERR_MSG[$response['error']], $response['error']);

        return (isset($response['error']) && (0 === $response['error'])) === true;
    }

    /**
     * Set the secret key.
     *
     * @param  string  $secret
     * @return self
     */
    protected function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    protected function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set Site key.
     *
     * @param  string  $siteKey
     * @return self
     */
    protected function setSiteKey($siteKey)
    {
        $this->siteKey = $siteKey;
        return $this;
    }

    protected function getSiteKey()
    {
        return $this->siteKey;
    }

    protected function setRequest(LuoCaptchaRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * 发送验证请求
     */
    protected function sendVerifyRequest($params)
    {
        $client   = new Client();
        $response = $client->request('POST', self::VERIFY_URL, [
            'form_params' => $params,
            'proxy' => 'http://127.0.0.1:8888'
        ]);
        if($response->getStatusCode() != 200)
            throw new LuoCaptchaException(LuoCaptchaException::ERR_MSG[LuoCaptchaException::API_SERVER_FAIL], LuoCaptchaException::API_SERVER_FAIL);

        $json = $response->getBody();
        $json = json_decode($json, true);

        return $json;
    }
}
