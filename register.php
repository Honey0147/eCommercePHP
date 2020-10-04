<?php

$GLOBALS['db'] = new PDO('mysql:dbname=ecommerce; host=localhost', "root", "");



function isUnknownEmail($email)
{
  $isUnknown = false;

  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $sql = $GLOBALS['db']->prepare('SELECT count(*) FROM user WHERE email = :email');
    $sql->bindValue(':email', $email);
    $sql->execute();
    $result = $sql->fetch(PDO::FETCH_NUM);

    $isUnknown = ($result[0] == 0);
  }

  return $isUnknown;
}

function addUserToDB($email, $password,  $shipping_address)
{

  $cmd = 'INSERT INTO user (email, password, shipping_address) ' .
    'VALUES (:email, :password, :shipping_address)';
  $sql = $GLOBALS['db']->prepare($cmd);
  $sql->bindValue(':email', $email);

  $sql->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));


  $sql->bindValue(':shipping_address', $shipping_address);
  $sql->execute();

  return $GLOBALS['db']->lastInsertId();
}

$http_verb = strtolower($_SERVER['REQUEST_METHOD']);

if ($http_verb == 'post') {
  try {
    $post = trim(file_get_contents("php://input"));
    $json = json_decode($post, true);

    $email = $json['email'];
    $password = $json['password'];


    $shipping_address = $json['shipping_address'];


    if (isUnknownEmail($email)) {
      $uid = addUserToDB($email, $password, $shipping_address);
      echo $uid;
      $myObj = new stdClass();
      $myObj->isLoggedIn = $uid > 0;
      $myObj->message = $uid > 0 ? "" : "something went wrong. Please try again.";

      $_SESSION['uid'] = $uid;

      echo json_encode($myObj);
    } else {
      $myObj = new stdClass();
      $myObj->isLoggedIn = false;
      $myObj->message = "email already has account. Try to log in instead";
      echo json_encode($myObj);
    }
  } catch (Exception $e) {
    $myObj = new stdClass();
    $myObj->isLoggedIn = false;
    $myObj->message = "something went wrong, please try again later";
    echo json_encode($myObj);
  }
}
