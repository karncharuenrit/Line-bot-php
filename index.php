<?php
/*Return HTTP Request 200*/
//ทำการ Return Response Status 200 กลับไปให้ LINE ก่อน เพื่อตรวจสอบว่า LINE Webhook สามารถเชื่อมมายัง Server เราได้
http_response_code(200);

file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND); //เราใช้เพื่ออ่านข้อมูล Data ที่ LINE ส่งเข้ามาซึ่งเราจะได้ข้อมูลในลักษณะของ Json แบบนี้
echo "IPJastel@center";

$datas = file_get_contents('php://input'); //$datas : เราสร้างตัวแปรนี้ขึ้นมาเพื่อไว้สำหรับเก็บ Datas ที่เราได้รับมาจาก LINE
$deCode = json_decode($datas, true); //$decode : เก็บค่า Array หลังจาก Decode แล้วโดยใช้คำสั่ง json_decode

$replyToken = $deCode['events'][0]['replyToken']; //เก็บข้อมูล replytoken ซึ่ง replytoken นี้เอาไว้สำหรับใช้ในการตอบข้อความแบบ reply (ตอบกลับทันทีหลังจากที่มีการส่ง Datas จาก LINE เข้ามา)

function getFormatTextMessage($text)
{
    $datas = [];
    $datas['type'] = 'text';
    $datas['text'] = $text;
    return $datas;
}


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


$messages = [];
$messages['replyToken'] = $replyToken;
$messages['messages'][0] = getFormatTextMessage('Hi..');
$encodeJson = json_encode($messages);
$LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
$LINEDatas['token'] = "hV49GKQw+K2jv0VCyJ2BT6tYiQm6dwweGBtDCW/TrudXBXzju8p0rojagOepJgAXaQ0Z0B2ZOQHHW4jMYWifptIb29Gew62KWD/8oMSN+eHFgyoZ9trsFeI06j2YId2mSxEcnypVdsUn0fz3GP5uIQdB04t89/1O/w1cDnyilFU=";
$results = sentMessage($encodeJson, $LINEDatas);


