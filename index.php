<?php

require_once './vendor/autoload.php';
/*Return HTTP Request 200*/
//ทำการ Return Response Status 200 กลับไปให้ LINE ก่อน เพื่อตรวจสอบว่า LINE Webhook สามารถเชื่อมมายัง Server เราได้

use Kreait\Firebase\Factory;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;

include("./flex.php");

$factory = (new Factory)
  ->withServiceAccount('./secret/readsid-5a802-d428a33cbfdc.json')
  // The following line is optional if the project id in your credentials file
  // is identical to the subdomain of your Firebase project. If you need it,
  // make sure to replace the URL with the URL of your project.
  ->withDatabaseUri('https://readsid-5a802.firebaseio.com/');

$database = $factory->createDatabase();

// die(print_r($database));



http_response_code(200);

file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND); //เราใช้เพื่ออ่านข้อมูล Data ที่ LINE ส่งเข้ามาซึ่งเราจะได้ข้อมูลในลักษณะของ Json แบบนี้


$datas = file_get_contents('php://input'); //$datas : เราสร้างตัวแปรนี้ขึ้นมาเพื่อไว้สำหรับเก็บ Datas ที่เราได้รับมาจาก LINE
$deCode = json_decode($datas, true); //$decode : เก็บค่า Array หลังจาก Decode แล้วโดยใช้คำสั่ง json_decode

$replyToken = $deCode['events'][0]['replyToken']; //เก็บข้อมูล replytoken ซึ่ง replytoken นี้เอาไว้สำหรับใช้ในการตอบข้อความแบบ reply (ตอบกลับทันทีหลังจากที่มีการส่ง Datas จาก LINE เข้ามา)

$accessToken = "hV49GKQw+K2jv0VCyJ2BT6tYiQm6dwweGBtDCW/TrudXBXzju8p0rojagOepJgAXaQ0Z0B2ZOQHHW4jMYWifptIb29Gew62KWD/8oMSN+eHFgyoZ9trsFeI06j2YId2mSxEcnypVdsUn0fz3GP5uIQdB04t89/1O/w1cDnyilFU="; //copy ข้อความ Channel access token ตอนที่ตั้งค่า
$content = file_get_contents('php://input');
$arrayJson = json_decode($content, true);
$arrayHeader = array();
$arrayHeader[] = "Content-Type: application/json";
$arrayHeader[] = "Authorization: Bearer {$accessToken}";


function getFormatTextMessage($text)
{

  $datas = [];
  $datas['type'] = 'text';
  $datas['text'] = $text;
  echo $text;
  return $datas;
}

// function pushMsg($arrayHeader, $arrayPostData)
// {
//     $strUrl = "https://api.line.me/v2/bot/message/push";
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $strUrl);
//     curl_setopt($ch, CURLOPT_HEADER, false);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     $result = curl_exec($ch);
//     curl_close($ch);
// }
// exit;
// $jsonFlex = 

function sentMessage($encodeJson, $datas)
{
  $datasReturn = [];
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $datas['url'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $encodeJson,
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer " . $datas['token'],
      "cache-control: no-cache",
      "content-type: application/json; charset=UTF-8",

    ),


  ));
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);
  if ($err) {
    $datasReturn['result'] = 'E';
    $datasReturn['message'] = $err;
  } else {
    if ($response == "{}") {
      $datasReturn['result'] = 'S';
      $datasReturn['message'] = 'Success';
    } else {
      $datasReturn['result'] = 'E';
      $datasReturn['message'] = $response;
    }
  }
  return $datasReturn;
}
//-------------------------------------replymessages------------------------------------------------------------------------------

$API_URL = 'https://api.line.me/v2/bot/message/reply';
$ACCESS_TOKEN = 'hV49GKQw+K2jv0VCyJ2BT6tYiQm6dwweGBtDCW/TrudXBXzju8p0rojagOepJgAXaQ0Z0B2ZOQHHW4jMYWifptIb29Gew62KWD/8oMSN+eHFgyoZ9trsFeI06j2YId2mSxEcnypVdsUn0fz3GP5uIQdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น
$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array
$message = strtolower($arrayJson['events'][0]['message']['text']);




if ($message == "c1553") {
  $image_url = "https://firebasestorage.googleapis.com/v0/b/readsid-5a802.appspot.com/o/c1553.png?alt=media&token=e38663ba-8a34-4daf-b333-5d4952397cd8";
  $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
  $arrayPostData['messages'][0]['type'] = "image";
  $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
  $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
  replyMsg($arrayHeader, $arrayPostData);
} else if ($message == "c1112") {
  $image_url = "https://firebasestorage.googleapis.com/v0/b/readsid-5a802.appspot.com/o/CYBER_WORLD_C1112.png?alt=media&token=a3c5187b-fb19-4b24-9d5f-c343df6dfbf7";
  $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
  $arrayPostData['messages'][0]['type'] = "image";
  $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
  $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
  replyMsg($arrayHeader, $arrayPostData);
} else if ($message == "c1478") {
  $image_url = "https://firebasestorage.googleapis.com/v0/b/readsid-5a802.appspot.com/o/c1478.png?alt=media&token=adf52fd6-0d58-44ad-abaf-aadb742b69f4";
  $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
  $arrayPostData['messages'][0]['type'] = "image";
  $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
  $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
  replyMsg($arrayHeader, $arrayPostData);
} else if ($message == "c1096") {
  $image_url = "https://firebasestorage.googleapis.com/v0/b/readsid-5a802.appspot.com/o/c1096_1.png?alt=media&token=89517fc8-3798-42cb-86d4-cadf40dcecdb";
  $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
  $arrayPostData['messages'][0]['type'] = "image";
  $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
  $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
  $image_url = "https://firebasestorage.googleapis.com/v0/b/readsid-5a802.appspot.com/o/c1096_2.png?alt=media&token=a1972fc6-1039-4e9f-b9e4-29eed05d89b0";
  $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
  $arrayPostData['messages'][1]['type'] = "image";
  $arrayPostData['messages'][1]['originalContentUrl'] = $image_url;
  $arrayPostData['messages'][1]['previewImageUrl'] = $image_url;
  replyMsg($arrayHeader, $arrayPostData);
} else {
  $data =  [
    'replytoken' => $replyToken,
    'messages' => $$jsonFlex
  ];
  $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
  $arrayPostData['messages'][0]['type'] = "text";
  $arrayPostData['messages'][0]['text'] = "กรุณาระบุ CID ให้ถูกต้อง";
  replyMsg($arrayHeader, $arrayPostData, $data);
}



// //if (sizeof($request_array['events']) > 0) {

//     foreach ($request_array['events'] as $event) {
//         $reply_message = '';
//         $reply_token = $event['replyToken'];
//         $datas = [];

//         if ($event['type'] == 'message') {
//             if ($event['message']['type'] == 'text') {
//                 $text = $event['message']['text'];
//                 $reply_message = '' . $text . '';
//             } else

//                 $reply_message = '' . $event['message']['type'] . '';
//         } else
//             $reply_message = '' . $event['type'] . '';




//         if (strlen($reply_message) > 0) {
//             //$reply_message = iconv("tis-620","utf-8",$reply_message);

//             $data = [
//                 'replyToken' => $reply_token,
//                 'messages' => [['type' => 'text', 'text' => $reply_message]]
//             ];

//             $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);

//             $send_result = send_reply_message($API_URL, $POST_HEADER, $post_body);
//             echo "Result: " . $send_result . "\r\n";
//         }
//     }
// }

function send_reply_message($url, $post_header, $post_body)
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $result = curl_exec($ch);
  curl_close($ch);

  return $result;
}
//--------------------------------------------------imagebuilder-----------------------------------------------

function replyMsg($arrayHeader, $arrayPostData)
{
  $strUrl = "https://api.line.me/v2/bot/message/reply";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $strUrl);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_DNS_LOCAL_IP4, false);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $result = curl_exec($ch);
  curl_close($ch);
}
exit;

$response = $bot->replyMessage($replyToken, $replyData);
if ($response->isSucceeded()) {
  echo 'Succeeded!';
  return;
}



?>

    <!-- // $message = $arrayJson['events'][0]['message']['text']; //รับข้อความจากผู้ใช้
// $id = $arrayJson['events'][0]['source']['userId']; //recive id form user 
$reply_messages = '';
$messages = [];
$messages['replyToken'] = $replyToken;
$userID = $events['events'][0]['source']['userId'];
$sourceType = $events['events'][0]['source']['type'];
//$text = $messaage['mmessage']['type']['text'];
$text = $event['message']['text'];
$reply_message = '('.$text.')';
$messages['messages'][0] = getFormatTextMessage($reply_message);
$encodeJson = json_encode($messages);
$LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
$LINEDatas['token'] = "hV49GKQw+K2jv0VCyJ2BT6tYiQm6dwweGBtDCW/TrudXBXzju8p0rojagOepJgAXaQ0Z0B2ZOQHHW4jMYWifptIb29Gew62KWD/8oMSN+eHFgyoZ9trsFeI06j2YId2mSxEcnypVdsUn0fz3GP5uIQdB04t89/1O/w1cDnyilFU=";
$results = sentMessage($encodeJson, $LINEDatas);; -->