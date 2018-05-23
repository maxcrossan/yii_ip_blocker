IP Blocker Component for Yii
============================

This will block and logout any users with IP addresses not listed in the whitelist

## Installation

Run the Composer command to install the latest stable version of the IPBlocker

```bash
    php composer.phar require maxcrossan/yii_ip_blocker
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