<?php

namespace Crossan;

use CApplicationComponent;
use CHttpException;
use Yii;

/**
 * Class IPBlocker
 * Will block and logout any users not listed in the whitelist
 * @author Max Crossan
 */
class IPBlocker extends CApplicationComponent
{

    public $validateOn = '!Yii::app()->user->isGuest';
    public $whitelistedIPs = array(':1', '127.0.0.1');
    public $blockedMessage = 'Access from {ip} is blocked.';

    private $_checkIP = true;
    private $_ipAddress;

    /**
     * @throws CHttpException
     */
    public function init()
    {
        $this->_ipAddress = Yii::app()->request->userHostAddress;
        $this->_checkIP = $this->evaluateExpression($this->validateOn);

        if($this->_checkIP && !$this->_canAccess()) {
            yii::app()->user->logout();
            throw new CHttpException(403, str_replace('{ip}', $this->_ipAddress, $this->blockedMessage));
        }
    }

    /**
     * @return bool
     */
    private function _canAccess(){
        if(!count($this->whitelistedIPs))
            return true;
        else if(in_array($this->_ipAddress, $this->whitelistedIPs))
            return true;

        foreach($this->whitelistedIPs as $whitelisted){
            if($this->ip_in_range($whitelisted))
                return true;
        }

        return false;
    }


    /**
     * @param $range
     * @return bool
     *
     * Network ranges can be specified as:
     * 1. Wildcard format:     1.2.3.*
     * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
     * 3. Start-End IP format: 1.2.3.0-1.2.3.255
     *
     * Modified from @Source https://stackoverflow.com/questions/11121817/how-to-check-an-ip-address-is-within-a-range-of-two-ips-in-php
     */
    private function ip_in_range($range) {
        $ip = $this->_ipAddress;
        if (strpos($range, '/') !== false) {
            // $range is in IP/NETMASK format
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                // $netmask is a 255.255.0.0 format
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
            } else {
                // $netmask is a CIDR size block
                // fix the range argument
                $x = explode('.', $range);
                while(count($x)<4) $x[] = '0';
                list($a,$b,$c,$d) = $x;
                $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);

                # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
                #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

                # Strategy 2 - Use math to create it
                $wildcard_dec = pow(2, (32-$netmask)) - 1;
                $netmask_dec = ~ $wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else {
            // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
            if (strpos($range, '*') !==false) { // a.b.*.* format
                // Just convert to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }

            if (strpos($range, '-')!==false) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float)sprintf("%u",ip2long($lower));
                $upper_dec = (float)sprintf("%u",ip2long($upper));
                $ip_dec = (float)sprintf("%u",ip2long($ip));
                return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
            }

            return false;
        }

    }

}