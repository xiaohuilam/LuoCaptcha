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

    const CAPTCHA_NAME = 'luo-captcha-response';

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
    protected $scriptLoaded = false;

    /**
     * HTTP Request
     *
     * @var \Xiaohuilam\LuoCaptcha\Utils\LuoCaptchaRequestInterface
     */
    protected $request;

    public function __construct($siteKey, $secret)
    {
        $this->setSecret($secret);
        $this->setSiteKey($siteKey);
    }

    public function display($name = null, array $attributes = [])
    {
        return '<div class="l-captcha" data-site-key="'.$this->getSiteKey().'"></div>';
    }

    public function script()
    {
        $script = '';
        if ( ! $this->scriptLoaded) {
            $script = '<script src="'.self::CLIENT_URL.'" async defer></script>';
            $this->scriptLoaded = true;
        }
        return $script;
    }

    public function verify($response, $clientIp = null)
    {
        if (empty($response)) 
            throw new LuoCaptchaException(LuoCaptchaException::ERR_MSG[LuoCaptchaException::BAD_RESPONSE], LuoCaptchaException::BAD_RESPONSE);

        $response = $this->sendVerifyRequest([
            'api_key'  => $this->secret,
            'response' => $response,
            'remoteip' => $clientIp
        ]);
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
        $resposne = $client->send('POST', self::VERIFY_URL, [
            'form_params' => $params
        ]);
        if($response->getStatusCode != 200)
            throw new LuoCaptchaException(LuoCaptchaException::ERR_MSG[LuoCaptchaException::API_SERVER_FAIL], LuoCaptchaException::API_SERVER_FAIL);

        $json = $response->getBody();
        $json = json_decode($json, true);
        return $json;
    }
}
