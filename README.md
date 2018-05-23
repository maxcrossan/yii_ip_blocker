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

Configure the component and preload it in protected/config/main.php

```php
    'preload'=>array('ip-blocker'),
    
    'components'=>array(
        'ip-blocker' => array(
            'class' => Crossan\IPBlocker::class,
            //Expression to validate on - should return true if validation is required
            'validateOn' => '!Yii::app()->user->isGuest',
            //{ip} will be replaced
            'blockedMessage' => "Access to this system is blocked from your IP: {ip}.",
            'whitelistedIPs' => array(
                ':1', '127.0.0.1', //Localhost
                '123.123.123.123', //Office
            )
        ),
    )
```