<?php

session_start();

  $GLOBALS['db'] = new PDO('mysql:dbname=ecommerce; host=localhost',"root",""); 

  function addProducts($description, $price,  $shipping_cost, $image)
  {

    $cmd = 'INSERT INTO product (description, price, shipping_cost,image) ' .
    'VALUES (:description, :price, :shipping_cost, :image)';
    $sql = $GLOBALS['db']->prepare($cmd);
    $sql->bindValue(':description', $description);

    $sql->bindValue(':price', $price);

    $sql->bindValue(':shipping_cost', $shipping_cost);

    $sql->bindValue(':image', $image);
    $sql->execute();

    return $GLOBALS['db']->lastInsertId();
  }

  $http_verb = strtolower($_SERVER['REQUEST_METHOD']);

  if ($http_verb == 'post')
  {
    try
    {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);
            
       
        $description = $json['description'];
        $price = $json['price'];
        $shipping_cost = $json['shipping_cost'];
        $image = $json['image'];
        

        if ( $_SESSION['userId'] != NULL)
        {
            $cid = addProducts($description, $price, $shipping_cost, $image);
            $myObj = new stdClass();
            $myObj->pAdded = $cid > 0;
            $myObj->message = $cid > 0 ? "" : "Something went wrong...";
            
            echo json_encode($myObj);
        }
        else
        {
            $myObj = new stdClass();
            $myObj->pAdded = false;
            $myObj->message = "Please login first..";
            echo json_encode($myObj);
        }
    }
    catch (Exception $e) 
    {
      $myObj = new stdClass();
      $myObj->pAdded = false;
      $myObj->message = "something went wrong, please try again later";
      echo json_encode($myObj);
    }
  }
?>