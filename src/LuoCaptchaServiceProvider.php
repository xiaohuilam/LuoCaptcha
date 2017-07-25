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
abstract class LuoCaptchaServiceProvider extends IlluminateServiceProvider
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
        parent::register();

        $this->registerConfig();
        $this->registerNoCaptcha();
    }

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        parent::boot();

        $this->registerFormMacros($this->app);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Contracts\NoCaptcha::class,
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
        $this->singleton(Contracts\NoCaptcha::class, function($app) {
            /** @var  \Illuminate\Contracts\Config\Repository  $config */
            $config = $app['config'];

            return new LuoCaptcha(
                $config->get('no-captcha.secret'),
                $config->get('no-captcha.sitekey')
            );
        });
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
                return $app[\Luosimao\Captcha\Utils\Contracts\LuoCaptcha::class]->display($name, $attributes);
            });
        }
    }
}
