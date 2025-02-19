<?php

function setPage($chatId, $data)
{
  file_put_contents('users/' . $chatId . '-page.txt', $data);
}

function getPage($chatId)
{
  return file_get_contents('users/' . $chatId . '-page.txt');
}

function productOrder($chatId, $data)
{
  file_put_contents('users/' . $chatId . '-order.txt', $data);
}

function getOrder($chatId)
{
  return file_get_contents('users/' . $chatId . '-order.txt');
}

function setPhone($chatId, $data)
{
  file_put_contents('users/' . $chatId . '-phone.txt', $data);
}

function getPhone($chatId)
{
  return file_get_contents('users/' . $chatId . '-phone.txt');
}

function setDelivery($chatId, $data)
{
  file_put_contents('users/' . $chatId . '-delivery.txt', $data);
}

function getDelivery($chatId)
{
  return file_get_contents('users/' . $chatId . '-delivery.txt');
}

function setLocation($chatId, $data)
{
  file_put_contents('users/' . $chatId . '-location.txt', $data);
}

function getLocation($chatId)
{
  return file_get_contents('users/' . $chatId . '-location.txt');
}