<?php namespace Luosimao\Captcha;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class     LuoCaptchaServiceProvider
 *
 * @package  Luosimao\Captcha;
 * @author   XIAOHUI.LAM <xiaohui.lam@gmail.com>
 */
class LuoCaptchaServiceProvider extends IlluminateServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'luo-captcha';

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerLuoCaptcha();
    }

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->registerFormMacros($this->app);
        $this->registerValidatorRules($this->app);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            \Luosimao\Captcha\Utils\Contracts\LuoCaptcha::class,
        ];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register NoCaptcha service.
     */
    private function registerLuoCaptcha()
    {
        $this->singleton(\Luosimao\Captcha\Utils\Contracts\LuoCaptcha::class, function($app) {
            /** @var  \Illuminate\Contracts\Config\Repository  $config */
            $config = $app['config'];

            return new LuoCaptcha(
                $config->get('no-captcha.sitekey'),
                $config->get('no-captcha.secret')
            );
        });
    }

    /**
     * Register Validator rules.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    private function registerValidatorRules($app)
    {
        $callback = function($attribute, $value) use ($app) {
            unset($attribute);
            $ip = $app['request']->getClientIp();
            return $app[\Luosimao\Captcha\Utils\Contracts\LuoCaptcha::class]->verify($value, $ip);
        };

        if ($app->bound('validator')) {
            $app['validator']->extend('captcha', $callback);
        } else {
            \Illuminate\Support\Facades\Validator::extend('captcha', $callback);
        }
    }

    /**
     * Register Form Macros.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    private function registerFormMacros($app)
    {
        if ($app->bound('form')) {
            $app['form']->macro('captcha', function($name = null, array $attributes = []) use ($app) {
                return $app[\Luosimao\Captcha\LuoCaptcha::class]->display($name, $attributes);
            });
        }
    }

    protected function singleton($abstract, $concrete = null)
    {
        $this->app->singleton($abstract, $concrete);
    }
}
