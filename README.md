# LARAVEL-LUOSIMAO
The ultimate captcha for chinese end users, luosimao. This package brings you a fast method to install the captcha function into your project.


# HOW TO GET LUOSIMAO CAPTCHA
https://luosimao.com/service/captcha


# INSTALL
```
composer require xiaohuilam/luo-captcha
```
```
vim .env
#NOCAPTCHA_SITEKEY={site key}
#NOCAPTCHA_SECRET={api key}
```
```
vim config/app.php
#add bellow into providers
Luosimao\Captcha\LuoCaptchaServiceProvider::class,

#add bellow into aliases:
'Captcha'     => Luosimao\Captcha\LuoCaptcha::class,
```

# DEMO

![11.png](https://i.loli.net/2017/08/01/598044b2eb541.png
)
![12.png](https://i.loli.net/2017/08/01/598044b30ebd3.png)

https://api.wallet.casa/login

