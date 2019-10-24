<?php

echo "i am bot A";
require_once '/Users/kan/Line-bot-php/index.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

$logger = new Logger('LineBot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV["hV49GKQw+K2jv0VCyJ2BT6tYiQm6dwweGBtDCW/TrudXBXzju8p0rojagOepJgAXaQ0Z0B2ZOQHHW4jMYWifptIb29Gew62KWD/8oMSN+eHFgyoZ9trsFeI06j2YId2mSxEcnypVdsUn0fz3GP5uIQdB04t89/1O/w1cDnyilFU="]);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV["0b524fb063d9b92c9c7debd29e5bbae0
"]]);

$signature = $_SERVER['HTTP_' . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
try {
  $events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
} catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
  error_log('parseEventRequest failed. InvalidSignatureException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
  error_log('parseEventRequest failed. UnknownEventTypeException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
  error_log('parseEventRequest failed. UnknownMessageTypeException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
  error_log('parseEventRequest failed. InvalidEventRequestException => '.var_export($e, true));
}
foreach ($events as $event) {
  // Postback Event
  if (($event instanceof \LINE\LINEBot\Event\PostbackEvent)) {
    $logger->info('Postback message has come');
    continue;
  }
  // Location Event
  if  ($event instanceof LINE\LINEBot\Event\MessageEvent\LocationMessage) {
    $logger->info("location -> ".$event->getLatitude().",".$event->getLongitude());
    continue;
  }
  
  // Message Event = TextMessage
  if (($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
    // get message text
    $messageText=strtolower(trim($event->getText()));
    
  }
}  
?>