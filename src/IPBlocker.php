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
        else return false;
    }

}