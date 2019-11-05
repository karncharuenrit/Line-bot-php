<?php

require_once './vendor/autoload.php';
/*Return HTTP Request 200*/
//ทำการ Return Response Status 200 กลับไปให้ LINE ก่อน เพื่อตรวจสอบว่า LINE Webhook สามารถเชื่อมมายัง Server เราได้

use Kreait\Firebase\Factory;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;


$factory = (new Factory)
    ->withServiceAccount('./secret/readsid-5a802-d428a33cbfdc.json')
    // The following line is optional if the project id in your credentials file
    // is identical to the subdomain of your Firebase project. If you need it,
    // make sure to replace the URL with the URL of your project.
    ->withDatabaseUri('https://readsid-5a802.firebaseio.com/');

$database = $factory->createDatabase();

// die(print_r($database));
$httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
$bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));


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



if (sizeof($request_array['events']) > 0) {

    foreach ($request_array['events'] as $event) {
        $reply_message = '';
        $reply_token = $event['replyToken'];
        $datas = [];

        if ($event['type'] == 'message') {
            if ($event['message']['type'] == 'text') {
                $text = $event['message']['text'];
                $reply_message = '' . $text . '';
            } else

                $reply_message = '' . $event['message']['type'] . '';
        } else
            $reply_message = '' . $event['type'] . '';




        if (strlen($reply_message) > 0) {
            //$reply_message = iconv("tis-620","utf-8",$reply_message);

            $data = [
                'replyToken' => $reply_token,
                'messages' => [['type' => 'text', 'text' => $reply_message]]
            ];



            $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);

            $send_result = send_reply_message($API_URL, $POST_HEADER, $post_body);
            echo "Result: " . $send_result . "\r\n";
        }
    }
}

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
//--------------------------------------------------imagebuilder------------------------------------------------

// แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array

$events = json_decode($content, true);
if (!is_null($events)) {
    // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
    $replyToken = $events['events'][0]['replyToken'];
    $typeMessage = $events['events'][0]['message']['type'];
    $userMessage = $events['events'][0]['message']['text'];
    $userMessage = strtolower($userMessage);
    switch ($typeMessage) {
        case 'text':
            switch ($userMessage) {
                case "t":
                    $textReplyMessage = "Bot ตอบกลับคุณเป็นข้อความ";
                    $replyData = new TextMessageBuilder($textReplyMessage);
                    break;
                case "i":
                    $picFullSize = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower';
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower/240';
                    $replyData = new ImageMessageBuilder($picFullSize, $picThumbnail);
                    break;
                case "v":
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/sampleimage/240';
                    $videoUrl = "https://www.mywebsite.com/simplevideo.mp4";
                    $replyData = new VideoMessageBuilder($videoUrl, $picThumbnail);
                    break;
                case "a":
                    $audioUrl = "https://www.mywebsite.com/simpleaudio.mp3";
                    $replyData = new AudioMessageBuilder($audioUrl, 27000);
                    break;
                case "l":
                    $placeName = "ที่ตั้งร้าน";
                    $placeAddress = "แขวง พลับพลา เขต วังทองหลาง กรุงเทพมหานคร ประเทศไทย";
                    $latitude = 13.780401863217657;
                    $longitude = 100.61141967773438;
                    $replyData = new LocationMessageBuilder($placeName, $placeAddress, $latitude, $longitude);
                    break;
                case "s":
                    $stickerID = 22;
                    $packageID = 2;
                    $replyData = new StickerMessageBuilder($packageID, $stickerID);
                    break;
                case "im":
                    $imageMapUrl = 'https://www.mywebsite.com/imgsrc/photos/w/sampleimagemap';
                    $replyData = new ImagemapMessageBuilder(
                        $imageMapUrl,
                        'This is Title',
                        new BaseSizeBuilder(699, 1040),
                        array(
                            new ImagemapMessageActionBuilder(
                                'test image map',
                                new AreaBuilder(0, 0, 520, 699)
                            ),
                            new ImagemapUriActionBuilder(
                                'http://www.ninenik.com',
                                new AreaBuilder(520, 0, 520, 699)
                            )
                        )
                    );
                    break;
                case "tm":
                    $replyData = new TemplateMessageBuilder(
                        'Confirm Template',
                        new ConfirmTemplateBuilder(
                            'Confirm template builder',
                            array(
                                new MessageTemplateActionBuilder(
                                    'Yes',
                                    'Text Yes'
                                ),
                                new MessageTemplateActionBuilder(
                                    'No',
                                    'Text NO'
                                )
                            )
                        )
                    );
                    break;
                default:
                    $textReplyMessage = " คุณไม่ได้พิมพ์ ค่า ตามที่กำหนด";
                    $replyData = new TextMessageBuilder($textReplyMessage);
                    break;
            }
            break;
        default:
            $textReplyMessage = json_encode($events);
            $replyData = new TextMessageBuilder($textReplyMessage);
            break;
    }
}
$textMessageBuilder = new TextMessageBuilder(json_encode($events));
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


        <!-- //test2 -->