<?php namespace Vdomah\MailTelegram\Models;

use October\Rain\Database\Model;

class Settings extends Model
{
    public $implement = [
        'System.Behaviors.SettingsModel',
    ];

    public $settingsCode = 'mail_telegram_settings';
    public $settingsFields = 'fields.yaml';

    //public $jsonable = ['telegram_chat_ids'];

    public function initSettingsData()
    {
        $this->disabled_in_debug = false;
        $this->disabled_sending = false;
    }

    public function getAdminsToSendOptions()
    {
        return \Backend\Models\User::get()->pluck('email', 'email');
    }
}