<?php namespace Vdomah\MailTelegram\Classes;

use Vdomah\MailTelegram\Models\Settings as MailTelegramSettings;

class Telegram extends Gateway
{
    public function __construct($arOptions = [])
    {
        if (isset($arOptions['telegram_token'])) {
            $sToken = $arOptions['telegram_token'];
        } else {
            $sToken = MailTelegramSettings::get('telegram_token');
        }

        $this->sName = 'telegram';

        $arOptions += array(
            'host' => 'api.telegram.org',
            'port' => 443,
        );

        parent::__construct($sToken, $arOptions);

        $this->sApiUrl = "{$this->sProtoPart}://{$this->sHost}{$this->sPortPart}/bot{$sToken}";
    }

    public function request($sMethod, $arParams = [])
    {
        $sUrl = $this->sApiUrl . '/' . $sMethod;
        $sQuery = http_build_query($arParams);

        curl_setopt($this->sHandle, CURLOPT_POST, true);
        curl_setopt($this->sHandle, CURLOPT_POSTFIELDS, $sQuery);
        curl_setopt($this->sHandle, CURLOPT_URL, $sUrl);
        curl_setopt($this->sHandle, CURLOPT_RETURNTRANSFER, true);

        $sResponse = curl_exec($this->sHandle);
        $arResponse = json_decode($sResponse, true);

        return $arResponse;
    }

    public function sendMessage($arParams = [])
    {
        return $this->request('sendMessage', [
            'chat_id'      => $arParams['chat_id'],
            'text'         => $arParams['text'],
            'parse_mode'   => $arParams['parse_mode'] ?? null,
            'reply_markup' => $arParams['reply_markup'] ?? null,
        ]);
    }
}