<?php

require_once "Telegram.php";
require_once "user.php";

$telegram = new Telegram('7622481588:AAGG-mbk3qUpaUCzMdPb_yUrZDjmpCrt81E');

$data = $telegram->getData();
$message = $data['message'];

$text = $message['text'];
$chat_id = $message['chat']['id'];

$ADMIN_ID = 1488911821;

$productTypes = ['Mahsulot 1', 'Mahsulot 2', 'Mahsulot 3'];
$pageStep = ['products', 'info', 'orders'];

if ($text == "/start") {
  start();
} else {
  switch (getPage($chat_id)) {
    case 'main':
      if ($text == "Mahsulotlar") {
        products();
      } elseif ($text == 'Buyurtmalarim') {
        myOrders();
      } elseif ($text == 'Info') {
        info();
      } else {
        defaultFunc();
      }
      break;
    case 'products':
      if (in_array($text, $productTypes)) {
        productOrder($chat_id, $text);
        askContact();
      } elseif ($text == 'Ortga') {
        home();
      } else {
        defaultFunc();
      }
      break;
    case 'phone':
      if ($message['contact']['phone_number'] != " ") {
        setPhone($chat_id, $message['contact']['phone_number']);
        delivery();
      } elseif ($text == 'Ortga') {
        home();
      } else {
          setPhone($chat_id, $text);
          delivery();
      }
      break;
    case 'delivery':
      if ($text == 'Yetkazib berish') {
        location();
      } elseif ($text == "O'zim borib olaman") {
        showReady();
      } elseif ($text == 'Ortga') {
        askContact();
      } else {
        defaultFunc();
      }
      break;
    case 'location':
      if ($message['location']['latitude'] != "") {
        setLocation($chat_id, $message['location']['latitude'] . " - " . $message['location']['longitude']);
        showReady();
      } elseif ($text == "Lokatsiya jo'nata olmayman") {
        showReady();
      } elseif ($text == 'Ortga') {
        delivery();
      } else {
        defaultFunc();
      }
      break;
    case 'ready':
      if ($text == 'Boshqa buyurtma qilish') {
        products();
      } else {
        defaultFunc();
      }
      break;
    case 'info':
      if ($text == 'Ortga') {
        home();
      } else {
        defaultFunc();
      }
      break;
    case 'orders':
      if ($text == 'Ortga') {
        home();
      } else {
        defaultFunc();
      }
      break;
  }
}



function start()
{
  global $telegram, $chat_id;

  setPage($chat_id, 'main');

  $option = [
    [$telegram->buildKeyboardButton("Mahsulotlar"), $telegram->buildKeyboardButton("Buyurtmalarim")],
    [$telegram->buildKeyboardButton("Info")],
  ];

  $keyboard = $telegram->buildKeyBoard($option, $onetima = true, $resize = true);


  $msg = "Salom hurmatli {$telegram->FirstName()} botimizga hush kelibsiz.";
  $content = array('chat_id' => $chat_id, 'text' => $msg);
  $telegram->sendMessage($content);

  $content = array('chat_id' => $chat_id, "reply_markup" => $keyboard, 'text' => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit, dignissimos.");
  $telegram->sendMessage($content);
}

function home()
{
  global $telegram, $chat_id;
  setPage($chat_id, 'main');

  $option = [
    [$telegram->buildKeyboardButton("Mahsulotlar"), $telegram->buildKeyboardButton("Buyurtmalarim")],
    [$telegram->buildKeyboardButton("Info")],
  ];

  $keyboard = $telegram->buildKeyBoard($option, $onetima = true, $resize = true);

  $content = array('chat_id' => $chat_id, "reply_markup" => $keyboard, 'text' => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit, dignissimos.");
  $telegram->sendMessage($content);
}

function products()
{
  global $telegram, $chat_id;
  setPage($chat_id, 'products');


  $option = [
    [$telegram->buildKeyboardButton("Mahsulot 1")],
    [$telegram->buildKeyboardButton("Mahsulot 2")],
    [$telegram->buildKeyboardButton("Mahsulot 3")],
    [$telegram->buildKeyboardButton("Ortga")],
  ];
  $keyboard = $telegram->buildKeyBoard($option, $onetima = true, $resize = true);

  $content = array('chat_id' => $chat_id, "reply_markup" => $keyboard, 'text' => "Kerakli mahsulotni tanlang.");
  $telegram->sendMessage($content);
}

function myOrders()
{
  global $telegram, $chat_id;

  setPage($chat_id, 'orders');

  $option = [
    [$telegram->buildKeyboardButton("Ortga")],
  ];
  $keyboard = $telegram->buildKeyBoard($option, $onetima = true, $resize = true);

  $mahsulot = getOrder($chat_id);
  $phone = getPhone($chat_id);
  $delivery = getDelivery($chat_id);
  $location = getLocation($chat_id);

  $msg = "Sizning buyurtmangiz:\n<b>Mahsulot:</b> <i>{$mahsulot}</i>\n<b>Telefon:</b> <i>{$phone}</i>\n<b>Yetkazish turi:</b> <i>{$delivery}</i>\n<b>Manzil:</b> <i>{$location}</i>";
  $content = array('chat_id' => $chat_id, "reply_markup" => $keyboard, 'text' => $msg, 'parse_mode' => "html");
  $telegram->sendMessage($content);
}

function defaultFunc()
{
  global $telegram, $chat_id;

  $msg = "Kerakli kamandani tanlamadingiz.";
  $content = array('chat_id' => $chat_id, 'text' => $msg, 'parse_mode' => "html");
  $telegram->sendMessage($content);
}

function info()
{
  global $telegram, $chat_id;
  $option = [
    [$telegram->buildKeyboardButton("Ortga")],
  ];
  $keyboard = $telegram->buildKeyBoard($option, $onetima = true, $resize = true);

  setPage($chat_id, 'info');

  $msg = "Mening haqimda qiziqqaningiz uchun rahmat!\n\nBatafsil bilish uchun ushbu saytga kiring. \n<a href='https://me.ioqil.uz'>Sayt</a>: me.ioqil.uz";
  $content = array('chat_id' => $chat_id, "reply_markup" => $keyboard, 'text' => $msg, 'parse_mode' => "html");
  $telegram->sendMessage($content);
}

function askContact()
{
  global $telegram, $chat_id, $text;

  setPage($chat_id, 'phone');

  $option = [
    [$telegram->buildKeyboardButton("Raqamni jo'natish", true)],
    [$telegram->buildKeyboardButton("Ortga")],
  ];
  $keyboard = $telegram->buildKeyBoard($option, $onetima = true, $resize = true);

  $content = array('chat_id' => $chat_id, "reply_markup" => $keyboard, 'text' => "Buyurtma tanlandi. \nSiz {$text}ni tanladingiz. \nTelefon raqamingizni yuboring!");
  $telegram->sendMessage($content);
}

function delivery()
{
  global $telegram, $chat_id;

  setPage($chat_id, 'delivery');

  $option = [
    [$telegram->buildKeyboardButton("Yetkazib berish")],
    [$telegram->buildKeyboardButton("O'zim borib olaman")],
    [$telegram->buildKeyboardButton("Ortga")],
  ];
  $keyboard = $telegram->buildKeyBoard($option, $onetima = true, $resize = true);

  $content = array('chat_id' => $chat_id, "reply_markup" => $keyboard, 'text' => "Yetkazib berish turini tanlang.");
  $telegram->sendMessage($content);
}

function location()
{
  global $telegram, $chat_id;

  setPage($chat_id, 'location');

  $option = [
    [$telegram->buildKeyboardButton("Lokatsiya yuborish", false, true)],
    [$telegram->buildKeyboardButton("Lokatsiya jo'nata olmayman")],
    [$telegram->buildKeyboardButton("Ortga")],
  ];
  $keyboard = $telegram->buildKeyBoard($option, $onetima = true, $resize = true);

  $content = array('chat_id' => $chat_id, "reply_markup" => $keyboard, 'text' => "Lokatsiya jo'nating.");
  $telegram->sendMessage($content);
}

function showReady()
{
  global $telegram, $chat_id, $ADMIN_ID;

  setPage($chat_id, 'ready');

  $option = [
    [$telegram->buildKeyboardButton("Boshqa buyurtma qilish")],
  ];
  $keyboard = $telegram->buildKeyBoard($option, $onetima = true, $resize = true);

  $content = array('chat_id' => $chat_id, "reply_markup" => $keyboard, 'text' => "Buyurtmangiz uchun rahmat!");
  $telegram->sendMessage($content);

  $mahsulot = getOrder($chat_id);
  $phone = getPhone($chat_id);
  $delivery = getDelivery($chat_id);
  $location = getLocation($chat_id);
  $location = explode(" - ", $location);

  $step = !empty($phone) ? "\n<b>Telefon:</b> <i>{$phone}</i>" : null;
  $step1 = !empty($delivery) ? "\n<b>Yetkazish turi:</b> <i>{$delivery}</i>" : null;

  $text = "Yangi buyurtma!\nBuyurtmachi ismi: {$telegram->FirstName()}\nid:($chat_id)\n<b>Mahsulot:</b> <i>{$mahsulot}</i>$step$step1";

  $content = array('chat_id' => $ADMIN_ID, "reply_markup" => $keyboard, 'text' => $text, "parse_mode" => 'html');
  $telegram->sendMessage($content);

  $content = array('chat_id' => $ADMIN_ID, 'latitude' => $location[0], 'longitude' => $location[1]);
  $telegram->sendLocation($content);

}

function sendJSON()
{
  global $telegram;
  $data = $telegram->getData();
  $telegram->sendMessage(
    [
      'chat_id' => $telegram->ChatID(),
      'text' => json_encode($data, JSON_PRETTY_PRINT)
    ]
  );
}

function testDB()
{
  global $conn;

  $sql = "SELECT * FROM districts";
  $statement = $conn->prepare($sql);
  $statement->execute();
  return $statement->fetchAll(PDO::FETCH_ASSOC);
}


