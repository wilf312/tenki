<?php


// Composer読み込み
require_once '../vendor/autoload.php';

// URL取得
$siteUrl = 'http://weather.yahoo.co.jp/weather/jp/expo/';

// HTML取得
$client = new Goutte\Client();
$crawler = $client->request('GET', $siteUrl);



$target = '#arealst,#arealst2';

// 見出しの取得
$head =  $crawler->filter($target)->filter("tr.heading")->first()->filter('th')->each(function ($nodeData) {
    return $nodeData->text();
});

// 地方ごとのデータ作成
$body =  $crawler->filter($target)->filter("tr")->each(function ($nodeData) {

    // 見出し部分のみスキップ
    $classData = $nodeData->attr('class');
    if ( $classData === 'heading' ) {
        return;
    }

    // 地方、情報の取得
    return $nodeData->filter('th,td')->each(function( $tip ) {
        return $tip->text();
    });
});



// JSONにする情報取得
$output = [];

// 見出しの追加
$head[0] = '地名';
array_push($output, $head);


// 地方別のデータ作成
foreach($body as $key => $val){

    if ( $val === NULL ) continue;
    array_push($output, $val);
}


// ヘッダー情報の送信
header("Content-Type: application/json; charset=utf-8");

// JSON作成
echo json_encode($output);


