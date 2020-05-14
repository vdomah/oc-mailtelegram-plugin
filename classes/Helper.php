<?php namespace Vdomah\MailTelegram\Classes;

use October\Rain\Support\Traits\Singleton;
use Vdomah\MailTelegram\Models\Settings as MailTelegramSettings;
use Vdomah\MailTelegram\Classes\Telegram;

class Helper
{
    use Singleton;

    public function send($obMessage)
    {
        $obSettings = MailTelegramSettings::instance();
        $bDontSendIfInDebugMode = config('app.debug') == true && $obSettings->disabled_in_debug == true;

        if ($obSettings->disabled_sending || $bDontSendIfInDebugMode) {
            return;
        }

        if (!empty($obSettings->admins_to_send)) {
            foreach ($obMessage->getTo() as $sEmail=>$sName) {
                if (!in_array($sEmail, $obSettings->admins_to_send)) {
                    return;
                }
            }
        }
        $sHtml = $obMessage->getBody();

        $sText = $this->makeTextFromHTML($sHtml, $obSettings->strip_eols);

        $obTelegram = (new Telegram);

        if (is_array($obSettings->telegram_chat_ids)) {
            foreach ($obSettings->telegram_chat_ids as $telegram_chat_id) {
                $obTelegram->sendMessage([
                    'chat_id'    => $telegram_chat_id['chat_id'],
                    'text'       => $sText,
                    'parse_mode' => 'HTML',
                ]);
            }
        }
    }

    /**
     * Strip tags, spaces, ends of lines
     */
    public function makeTextFromHTML($sHtml, $bStripEOL = false)
    {
        $arResult = [];

        //Remove style tag with it's content
        $sRegex = '/<style[^>]*>[^<]*<[^>]*>/';
        $sHtml = preg_replace($sRegex, '', $sHtml);

        $sText = strip_tags($sHtml);

        if ($bStripEOL) {
            $sText = preg_replace('/\s+/', ' ', $sText);
        } else {
            $arText = explode(PHP_EOL, $sText);

            foreach($arText as $sRow) {
                $sRow = trim($sRow);
                if ($sRow != "") {
                    $arResult[] = $sRow;
                }
            }

            $sText = implode(PHP_EOL, $arResult);
        }

        return $sText;
    }
}