<?php namespace Vdomah\MailTelegram;

use Event;
use Vdomah\MailTelegram\Classes\Helper;
use Vdomah\MailTelegram\FormWidgets\BotLog;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;

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
        Event::listen('mailer.send', function ($obMailerInstance, $sView, $obMessage) {
            Helper::instance()->send($obMessage);
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
}
