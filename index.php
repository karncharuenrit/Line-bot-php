<?php

echo "i am bot";
require_once '/index.php';

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
} catch (\LINE\LINEBot\Exception\InvalidSignatureException $e) {
    error_log('parseEventRequest failed. InvalidSignatureException => ' . var_export($e, true));
} catch (\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
    error_log('parseEventRequest failed. UnknownEventTypeException => ' . var_export($e, true));
} catch (\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
    error_log('parseEventRequest failed. UnknownMessageTypeException => ' . var_export($e, true));
} catch (\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
    error_log('parseEventRequest failed. InvalidEventRequestException => ' . var_export($e, true));
}
