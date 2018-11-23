IP Blocker Component for Yii
============================

This will block and logout any users with IP addresses not listed in the whitelist

## Installation

Run the Composer command to install the latest stable version of IPBlocker

```bash
    php composer.phar require maxcrossan/yii_ip_blocker
```

Configure the component and preload it in protected/config/main.php

```php
    'preload'=>array('ip-blocker'),
    
    'components'=>array(
        'ip-blocker' => array(
            'class' => Crossan\IPBlocker::class,
            //Expression to validate on (should return true if validation is required)
            'validateOn' => '!Yii::app()->user->isGuest',
            //{ip} will be replaced
            'blockedMessage' => "Access to this system is blocked from your IP: {ip}.",
            // Network ranges can be specified as:
            // 1. Wildcard format:     1.2.3.*
            // 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
            // 3. Start-End IP format: 1.2.3.0-1.2.3.255
            'whitelistedIPs' => array(
                ':1', '127.0.0.1', //Localhost
                '123.123.123.123', //Office
            ),
            'whitelistArraySupplier' => null, //Use this to grab IPs from a method somewhere in your codebase. Will override whitelistedIPs if set.
        ),
    )
```