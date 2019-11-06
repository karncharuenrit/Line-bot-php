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
$message = $arrayJson['events'][0]['message']['text'];


if ($message == "c1553") {
    $image_url = "https://lh3.googleusercontent.com/YKckTcdBzSbTBzAjteTKVRGtvv584CjTq6-tlSt_PySW8yY5LhPwOJm0A_B5vXvPqFuoEpXDEvCH26lLhQ9d1K39vv0WBcrctZpfXgjg5BqqrUGd4iOT5_FtwbpkVqIBBKG9_456cQ8OQh2cIeuKQWHFgdqgb5awBhcqKPYC67CHtCLhUb5x8uK_S_G4DaF4dh60dxSQ5GsThwYvWriblqeGZv57dzgk2Lx3-zO6KSf7nrV8qpRuauQsuT2mOTWQgVhvm57UaSHzswXFw5U_XENdWCs3ADuX9eBnutdfKFEh2gkSR0TI_0cYu3aiWz-sPgi5QrvQwq3mjfovT7-jPGmzADPjTYBXuBjPLTFWQPjR1tQJ-oEQgqqI_TZmsj7YzlYUTFs3pQPCtLNnQUWKK4lM1A6G-P-mFgCB57XrDtclkjWp70Wky2nv5sdGdojVjzN483qC85C66sI9XTCihBOvXI5bBeiO1-DyzEmKIsSdLAs8MYbA6VWR_DqckvPYA1raiZ8Cy4hbdfl9zkdMraJcXBpBMfcsLe66BVQepa6UV6oxKsOE3WI6btq86NlF8FLI24AJnIq919sxyKCz5wtgAF1xPVwJ4trDmBBy4rrAUiHuNxanqeg9XLrsKqUNZ03gDxnTp76YwIn9AOWCywmYIhDqEEbCIlBzsU77UBltL6XjVtFk=w603-h211-no";
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "image";
    $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
    $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
    replyMsg($arrayHeader, $arrayPostData);
} elseif ($messaage == "c1521") {
    $image_url = "https://lh3.googleusercontent.com/YKckTcdBzSbTBzAjteTKVRGtvv584CjTq6-tlSt_PySW8yY5LhPwOJm0A_B5vXvPqFuoEpXDEvCH26lLhQ9d1K39vv0WBcrctZpfXgjg5BqqrUGd4iOT5_FtwbpkVqIBBKG9_456cQ8OQh2cIeuKQWHFgdqgb5awBhcqKPYC67CHtCLhUb5x8uK_S_G4DaF4dh60dxSQ5GsThwYvWriblqeGZv57dzgk2Lx3-zO6KSf7nrV8qpRuauQsuT2mOTWQgVhvm57UaSHzswXFw5U_XENdWCs3ADuX9eBnutdfKFEh2gkSR0TI_0cYu3aiWz-sPgi5QrvQwq3mjfovT7-jPGmzADPjTYBXuBjPLTFWQPjR1tQJ-oEQgqqI_TZmsj7YzlYUTFs3pQPCtLNnQUWKK4lM1A6G-P-mFgCB57XrDtclkjWp70Wky2nv5sdGdojVjzN483qC85C66sI9XTCihBOvXI5bBeiO1-DyzEmKIsSdLAs8MYbA6VWR_DqckvPYA1raiZ8Cy4hbdfl9zkdMraJcXBpBMfcsLe66BVQepa6UV6oxKsOE3WI6btq86NlF8FLI24AJnIq919sxyKCz5wtgAF1xPVwJ4trDmBBy4rrAUiHuNxanqeg9XLrsKqUNZ03gDxnTp76YwIn9AOWCywmYIhDqEEbCIlBzsU77UBltL6XjVtFk=w603-h211-no";
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "image";
    $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
    $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
    replyMsg($arrayHeader, $arrayPostData);
} elseif ($messaage == "c1532") {
    $image_url = "https://lh3.googleusercontent.com/YKckTcdBzSbTBzAjteTKVRGtvv584CjTq6-tlSt_PySW8yY5LhPwOJm0A_B5vXvPqFuoEpXDEvCH26lLhQ9d1K39vv0WBcrctZpfXgjg5BqqrUGd4iOT5_FtwbpkVqIBBKG9_456cQ8OQh2cIeuKQWHFgdqgb5awBhcqKPYC67CHtCLhUb5x8uK_S_G4DaF4dh60dxSQ5GsThwYvWriblqeGZv57dzgk2Lx3-zO6KSf7nrV8qpRuauQsuT2mOTWQgVhvm57UaSHzswXFw5U_XENdWCs3ADuX9eBnutdfKFEh2gkSR0TI_0cYu3aiWz-sPgi5QrvQwq3mjfovT7-jPGmzADPjTYBXuBjPLTFWQPjR1tQJ-oEQgqqI_TZmsj7YzlYUTFs3pQPCtLNnQUWKK4lM1A6G-P-mFgCB57XrDtclkjWp70Wky2nv5sdGdojVjzN483qC85C66sI9XTCihBOvXI5bBeiO1-DyzEmKIsSdLAs8MYbA6VWR_DqckvPYA1raiZ8Cy4hbdfl9zkdMraJcXBpBMfcsLe66BVQepa6UV6oxKsOE3WI6btq86NlF8FLI24AJnIq919sxyKCz5wtgAF1xPVwJ4trDmBBy4rrAUiHuNxanqeg9XLrsKqUNZ03gDxnTp76YwIn9AOWCywmYIhDqEEbCIlBzsU77UBltL6XjVtFk=w603-h211-no";
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "image";
    $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
    $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
    replyMsg($arrayHeader, $arrayPostData);
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
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
}
exit;
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