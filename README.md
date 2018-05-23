IP Blocker Component for Yii
============================

Will block and logout any users not listed in a whitelist 

## Installation

Add the following to your composer file and run `php composer.phar update`

```json
    "require": {
        "maxcrossan/yii_ip_blocker": "dev-master"
    },
```

Configure the component in protected/config/main.php

```php
    'components'=>array(
        'ip-blocker' => array(
            'class' => Crossan\IPBlocker::class,
            'validateOn' => '!Yii::app()->user->isGuest', //Expression to validate on - should return true if validation is required
            'blockedMessage' => "Access to this system is blocked from your IP: {ip}.", //{ip} will be replaced
            'whitelistedIPs' => array(
                ':1', '127.0.0.1', //Localhost
                '123.123.123.123', //Office
            )
        ),
    )
```