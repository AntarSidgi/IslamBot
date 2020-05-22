<?php

namespace App\Handlers;
use Illuminate\Support\Facades\Log;
use Telegram;

class Handler{

  public static $BASE_URL = 'https://api.islamhouse.com/v3/vVy4XOskhGrOe6kQ/main/';

  public static function handleMessage($arg){
    if($arg['message']['text'] == 'english'){
            $chat_id = $arg['message']['chat']['id'];
            Telegram::sendChatAction(['chat_id' => $chat_id ,'action' => 'typing']);

            $response = file_get_contents(Handler::$BASE_URL.'sitecontent/en/showall/json');
            $response = json_decode($response, true);



            $keyboard = [];

            foreach($response as $section){
              array_push($keyboard, [['text' => $section["block_name"], 'callback_data' => $section["block_name"]]]);
            }

            $inline_keyboard = json_encode([
                'inline_keyboard'=>$keyboard
            ]);

            Telegram::sendMessage([
            'chat_id'=>$chat_id,
            'text' => 'Choose section?',
            'reply_markup' => $inline_keyboard,
            ]);
        }
  }

  public static function handleCallBack($arg){
    error_log("handling callback");
    $callback = $arg['callback_query']['data'];
    $chat_id = $arg['callback_query']['message']['chat']['id'];
    
    switch ($callback) {
      case 'showall':
        Handler::sendMessages(Handler::$BASE_URL.'showall/en/showall/1/3/json', $chat_id, 'showall');
        break;
      case 'videos':
        Handler::sendMessages(Handler::$BASE_URL.'videos/en/showall/1/3/json', $chat_id, 'videos');
        break;
      case 'books':
        Handler::sendMessages(Handler::$BASE_URL.'books/en/showall/1/3/json', $chat_id, 'books');
        break;
      case 'articles':
        Handler::sendMessages(Handler::$BASE_URL.'articles/en/showall/1/3/json', $chat_id, 'articles');
        break;
      case 'audios':
        Handler::sendMessages(Handler::$BASE_URL.'audios/en/showall/1/3/json', $chat_id, 'audios');
        break;
      case 'fatwa':
        Handler::sendMessages(Handler::$BASE_URL.'fatwa/en/showall/1/3/json', $chat_id, 'fatwa');
        break;
      case 'favorites':
        Handler::sendMessages(Handler::$BASE_URL.'favorites/en/showall/1/3/json', $chat_id, 'favorites');
        break;
      case 'quran':
        Handler::sendMessages(Handler::$BASE_URL.'quran/en/showall/1/3/json', $chat_id, 'quran');
        break;
      case 'programsv':
        Handler::sendMessages(Handler::$BASE_URL.'programsv/en/showall/1/3/json', $chat_id, 'cards');
        break;
      case 'cards':
        Handler::sendMessages(Handler::$BASE_URL.'cards/en/showall/1/3/json', $chat_id, 'cards');
        break;
      case 'poster':
        Handler::sendMessages(Handler::$BASE_URL.'poster/en/showall/1/3/json', $chat_id, 'poster');
        break;
      case 'apps':
        Handler::sendMessages(Handler::$BASE_URL.'apps/en/showall/1/3/json', $chat_id, 'apps');
        break;
      default:
        error_log($callback);
        $cb = json_decode($callback, true);
	$url = Handler::$BASE_URL.$cb['section'].'/en/showall/'.$cb['to'].'/3/json';
	$message_id =$arg['callback_query']['message']["message_id"];
	error_log($message_id);
	error_log(json_encode($arg));
	Handler::editMessage($chat_id, $message_id);
	
	
        Handler::sendMessages($url, $chat_id, $cb['section']);
        break;
    }
    return;
  }

  public static function editMessage($chat_id, $message_id){    
	  $url = 'https://api.telegram.org/bot923486134:AAFdGgmm5zv3iDJC5rGvj_okOALT_HcLTUA/editMessageText';  
	    $data = array('chat_id'=>$chat_id, 'message_id'=>$message_id, 'text'=>'--------', 'reply_markup'=>'');
	  $options = array(
		  'http' => array(
			  'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			  'method'  => 'POST',
			  'content' => http_build_query($data)
		  ));
	  $context = stream_context_create($options);
	  $response = file_get_contents($url, false, $context);
	  error_log($response);
  }


  public static function sendMessages($url, $chat_id, $section){
    Telegram::sendChatAction(['chat_id' => $chat_id ,'action' => 'typing']);
        $response = file_get_contents($url);
        $response = json_decode($response, true);
        foreach($response['data'] as $resp){
          $photo = $resp['image'];
          foreach($resp['attachments'] as $video){
            // error_log('sending data'.$video['url']);

            Telegram::sendMessage([
              'chat_id' => $chat_id,
              'text' => '<b>'."\xF0\x9F\x94\xB0".' section: </b>'.$section."\n".'<b>'."\xF0\x9F\x93\x9C".' description: </b>'.$video['description']."\n".'<b>'."\xF0\x9F\x94\x97".'url:</b>'.'<a href="'. $video['url'].'">download file</a>' . "\n" .'<b>'."\xF0\x9F\x94\x98".'size: </b>'.$video['size'],
              'parse_mode' => 'HTML',
            ]);

            
            if(!next($response['data']) && !next($resp['attachments'])){
              if(!is_null($response['links']['next'])){
                error_log($response['links']['next']);
                $inline_keyboard = json_encode([
                  'inline_keyboard'=>[
                    [
                      ['text'=>'next', 'callback_data'=>json_encode(
                        [ 
                          'section' => $section,
                          'to' => $response['links']['current_page'] + 1,
                      ])
                    ]
                  ]
                ]]);

                // if(is_null($photo)){
                //   Telegram::sendMessage([
                //     'chat_id' => $chat_id,
                //     'text' => '<b>description:</b>'.$video['description']."\n".'<b>url:</b>'. $video['url'] . "\n" .'<b>size:</b>'.$video['size'],
                //     'parse_mode' => 'HTML',
                //     'reply_markup' => $inline_keyboard,
                //   ]);
                // }else{
                //   Telegram::sendPhoto([
                //   'chat_id' => $chat_id,
                //   'photo' => $photo,
                //   'caption' => '<b>description:</b>'.$video['description']."\n".'<b>url:</b>'. $video['url'] . "\n" .'<b>size:</b>'.$video['size'],
                //   'parse_mode' => 'HTML',
                //   'reply_markup' => $inline_keyboard,
                // ]);
                // }
                // Telegram::sendMessage([
                //   'chat_id' => $chat_id,
                //   'text' => '<b>section: </b>'."\n".$section.'<b>description: </b>'.$video['description']."\n".'<b>url:</b>'. $video['url'] . "\n" .'<b>size: </b>'.$video['size'],
                //   'parse_mode' => 'HTML',
                //   'reply_markup' => $inline_keyboard,
                // ]);
                Telegram::sendMessage([
                  'chat_id'=>$chat_id,
                  'text' => 'page '.$response['links']['current_page'].' of '.$response['links']['pages_number'],
                  'reply_markup' => $inline_keyboard,
                  ]);
              }
            break;
            }

            // if(is_null($photo)){
            //   Telegram::sendMessage([
            //     'chat_id' => $chat_id,
            //     'text' => '<b>description:</b>'.$video['description']."\n".'<b>url:</b>'. $video['url'] . "\n" .'<b>size:</b>'.$video['size'],
            //     'parse_mode' => 'HTML',
            //   ]);
            // }else{
            //   Telegram::sendPhoto([
            //   'chat_id' => $chat_id,
            //   'photo' => $photo,
            //   'caption' => '<b>description:</b>'.$video['description']."\n".'<b>url:</b>'. $video['url'] . "\n" .'<b>size:</b>'.$video['size'],
            //   'parse_mode' => 'HTML',
            // ]);
            // }
          }
        }

        // if(!is_null($response['links']['next'])){
        //   error_log($response['links']['next']);
        //   sleep(10);
        //   $inline_keyboard = json_encode([
        //     'inline_keyboard'=>[
        //       [
        //         ['text'=>'next', 'callback_data'=>json_encode(
        //           [ 
        //             'section' => 'video',
        //             'to' => $response['links']['current_page'] + 1,
        //         ],)

        //       ]
        //     ]
        //   ]]);
        // }
  }

}
