<?php

namespace App\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class EnglishCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "english";

    /**
     * @var string Command Description
     */
    protected $description = "get english resources";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        // This will send a message using `sendMessage` method behind the scenes to
        // the user/chat id who triggered this command.
        // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
        // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.

        // This will update the chat status to typing...
        error_log("handling english");

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $response = file_get_contents('http://api.islamhouse.com/v1/vVy4XOskhGrOe6kQ/main/sitecontent/en/showall/json');
        $response = json_decode($response);



        $keyboard = array();

        foreach($response as $section){
          array_push($keyboard, $section["block_name"]);
        }

        $kb = [
          'keyboard' => $keyboard,
          'resize_keyboard' => true,
          'one_time_keyboard' => true,
          'selective' => true
        ];

        $kb = json_encode($kb, true);

        $this->replyWithMessage([
          'text' => 'Choose section?',
          'reply_markup' => $kb,
        ]);

        // This will prepare a list of available commands and send the user.
        // First, Get an array of all registered commands
        // They'll be in 'command-name' => 'Command Handler Class' format.
    }
}