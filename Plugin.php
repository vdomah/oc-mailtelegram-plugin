<?php namespace Vdomah\MailTelegram;

use Event;
use Vdomah\MailTelegram\Classes\Helper;
use Vdomah\MailTelegram\FormWidgets\BotLog;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Vdomah\MailTelegram\Models\Settings as MailTelegramSettings;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'vdomah.mailtelegram::lang.plugin.name',
            'description' => 'vdomah.mailtelegram::lang.plugin.description',
            'author'      => 'Art Gek',
            'icon'        => 'icon-envelope-o',
            'homepage'    => 'https://github.com/vdomah/oc-mailtelegram-plugin',
        ];
    }

    public function boot()
    {
        Event::listen('mailer.prepareSend', function ($obMailerInstance, $sView, $obMessage) {
            Helper::instance()->send($obMessage);

            if (MailTelegramSettings::get('prevent_mail_sending', false)) {
                return false;
            }
        });
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'vdomah.mailtelegram::lang.settings.label',
                'description' => 'vdomah.mailtelegram::lang.settings.description',
                'category'    => SettingsManager::CATEGORY_NOTIFICATIONS,
                'icon'        => 'icon-envelope-o',
                'class'       => 'Vdomah\MailTelegram\Models\Settings',
                'order'       => 500,
                'permissions' => ['vdomah.mailtelegram.access_settings'],
            ],
        ];
    }

    public function registerFormWidgets()
    {
        return [
            BotLog::class => [
                'label' => 'Bot Log',
                'code'  => 'botlog'
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'vdomah.mailtelegram.access_settings' => [
                'tab' => 'vdomah.mailtelegram::lang.plugin.name',
                'label' => 'vdomah.mailtelegram::lang.permissions.access_settings'
            ],
        ];
    }
}
