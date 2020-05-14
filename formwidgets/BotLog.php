<?php namespace Vdomah\MailTelegram\FormWidgets;

use Exception;
use Backend\Classes\FormWidgetBase;

/**
 * Widget to create set of translatable fields grouped into language tabs.
 *
 * @package vdomah\mailtelegram
 * @author Art Gek
 */
class BotLog extends FormWidgetBase
{
    /**
     * @var array Form field configuration
     */
    public $form;

    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'botlog';

    /**
     * @var array Collection of form widgets.
     */
    protected $formWidgets = [];

    public $tabs = [];

    public $previewMode = false;

    public $viewPathBackend;

    public function init()
    {
        $this->fillFromConfig([
            'form',
        ]);

        $this->bindToController();

        $this->viewPathWidget = 'vdomah/mailtelegram/formwidgets/botlog/partials/';
    }

    public function render()
    {
        $this->prepareVars();
        return $this->makePartial($this->viewPathWidget . 'default');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $arUpdates = [];
        $arLogs = [];

        try {
            if ($this->model->telegram_token) {
                $sResp = file_get_contents('https://api.telegram.org/bot' . $this->model->telegram_token . '/getUpdates');

                if (isset($sResp))
                    $arResp = json_decode($sResp);

                if (isset($arResp)) {
                    foreach ($arResp->result as $arUpdate) {
                        if (!property_exists($arUpdate, 'message') || $arUpdate->message->from->is_bot) {
                            $arUpdates[] = $arUpdate;
                        } else {
                            if ($arUpdate->message->chat) {
                                $arLogs = [
                                    'User or chat ID' => (property_exists($arUpdate->message->chat, 'id') ? $arUpdate->message->chat->id : ''),
                                    'First name' => (property_exists($arUpdate->message->chat, 'first_name') ? $arUpdate->message->chat->first_name : ''),
                                    'Last name' => (property_exists($arUpdate->message->chat, 'last_name') ? $arUpdate->message->chat->last_name : ''),
                                    'Username' => (property_exists($arUpdate->message->chat, 'username') ? $arUpdate->message->chat->username : ''),
                                    'Date' => date('F j, Y H:i:s', $arUpdate->message->date),
                                ];
                            } else {
                                $arUpdates[] = $arUpdate->message->chat;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $arUpdates[] = $e->getMessage();
        }

        $this->vars['model'] = $this->model;
        $this->vars['updates_log'] = $arLogs;
        $this->vars['updates_arr'] = $arUpdates;
    }
}