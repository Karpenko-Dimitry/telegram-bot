<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetWebhookRequest;
use App\Models\TelegramUser;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramController extends Controller
{
    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * @throws TelegramSDKException
     */
    public function webhook()
    {
        $response = $this->telegram->getWebhookUpdate();

        $chat = $response->getChat();
        $message = $response->getMessage();
        $id = $chat->get('id');
        $text = $message->get('text');

        TelegramUser::makeNew($message->toArray());
        $this->telegram->sendMessage([
            'chat_id' => $id,
            'text' => "Я начинаю оживать. Мне приятно что Вы написали: $text",
        ]);
    }

    /**
     * @throws TelegramSDKException
     */
    public function setWebhook(SetWebhookRequest $request)
    {
        if ($request->get('pass') === config('telegram.bots.mybot.webhook_secret')) {
            return $this->telegram->setWebhook([
                'url' => 'https://kdv.s-host.net/api/telegram/webhook',
                'secret_token' => config('telegram.bots.mybot.webhook_secret'),
            ]);
        } else {
            abort(404);
        }
    }
}
