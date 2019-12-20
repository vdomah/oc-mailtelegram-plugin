<?php namespace Vdomah\MailTelegram;

use Event;
use Vdomah\MailTelegram\Classes\Telegram;
use Vdomah\MailTelegram\Models\Settings as MailTelegramSettings;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'Mail Telegram',
            'description' => 'Send site mail to your Telegram via bot',
            'author'      => 'Art Gek',
            'icon'        => 'icon-envelope-o',
            'homepage'    => 'https://github.com/vdomah/oc-mailtelegram-plugin',
        ];
    }

    public function boot()
    {
        Event::listen('mailer.send', function ($obMailerInstance, $sView, $obMessage) {
            $obSettings = MailTelegramSettings::instance();

            if ($obSettings->disabled_sending) {
                return;
            }

            if (config('app.debug') == true && $obSettings->disabled_in_debug == true) {
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

            $sText = $this->clearHTML($sText);

            $sText = preg_replace('/\s+/', ' ', strip_tags($sHtml));

            (new Telegram)->sendMessage([
                'chat_id'    => MailTelegramSettings::instance()->telegram_chat_id,
                'text'       => $sText,
                'parse_mode' => 'HTML',
            ]);
        });
    }

    /**
     * Убираем пробелы, переносы строк
     */
    private function clearHTML($sHtml)
    {
        $result = [];

        $sRegex = '/<style[^>]*>[^<]*<[^>]*>/';
        $sHtml = preg_replace($sRegex, '', $sHtml);

        $sText = strip_tags($sHtml);

        $arText = explode("\n", $sText);

        foreach($arText as $sRow)
        {
          $sRow = trim ($sRow);
          if ($sRow != "") {
            $result[] = $sRow;
          }
        }

        return implode("\n", $result);
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'vdomah.mailtelegram::lang.settings.settings_label',
                'description' => 'vdomah.mailtelegram::lang.settings.settings_desc',
                'category'    => SettingsManager::CATEGORY_NOTIFICATIONS,
                'icon'        => 'icon-envelope-o',
                'class'       => 'Vdomah\MailTelegram\Models\Settings',
                'order'       => 500,
            ],
        ];
    }
}
