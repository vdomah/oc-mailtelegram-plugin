<?php namespace Vdomah\MailTelegram\Classes;

use Exception;

abstract class Gateway
{
    public $sName;

    protected $sHost;
    protected $sPort;
    protected $sApiUrl;

    protected $sProtoPart;
    protected $sPortPart;

    protected $sHandle;
    protected $sBotToken;

    public function __construct($sToken, $arOptions = [])
    {
        if ($this->sName == null) {
            throw new Exception('Gateway name is empty!');
        }

        $this->sHandle = curl_init();
        $this->sHost = $sHost = $arOptions['host'];
        $this->sPort = $sPort = $arOptions['port'];
        $this->sBotToken = $sToken;

        $this->sProtoPart = ($sPort == 443 ? 'https' : 'http');
        $this->sPortPart = ($sPort == 443 || $sPort == 80) ? '' : ':' . $sPort;
    }

    /**
     * Sending request to bot
     * @param $sMethod
     * @param array $arParams
     * @return mixed
     */
    abstract protected function request($sMethod, $arParams = []);

    /**
     * Sending message to bot
     * @param array $arParams
     * @return mixed
     */
    abstract public function sendMessage($arParams = []);
}