<?php

 session_start();

  $GLOBALS['db'] = new PDO('mysql:dbname=ecommerce; host=localhost',"root",""); 

  function addProductCart($user_id,$product_id,$quantity)
  {

    $cmd = 'INSERT INTO cart ( user_id, product_id, quantity) ' .
    'VALUES ( :user_id, :product_id, :quantity)';
    $sql = $GLOBALS['db']->prepare($cmd);
    #$sql->bindValue(':cart_id', $cart_id);
    $sql->bindValue(':user_id', $user_id);
    $sql->bindValue(':product_id', $product_id);
     $sql->bindValue(':quantity', $quantity);
  
    $sql->execute();

    return $GLOBALS['db']->lastInsertId();
  
  }

  function deleteProduct($user_id)
  {

     try 
      {
      $db = new PDO('mysql:dbname=ecommerce; host=localhost'); 
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = $db->prepare('DELETE FROM cart WHERE user_id = :user_id');
        $sql->bindValue(':user_id', $user_id);

        $sql->execute();
        
        
      }
    
    catch(PDOException $e) 
    {
      echo $e->getMessage();
    }
  
  }

  $http_verb = strtolower($_SERVER['REQUEST_METHOD']);

  if ($http_verb == 'post')
  {
    try
    {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);
            
       
        $user_id = $_SESSION['userId'];
        $product_id = $json['product_id'];
        $quantity = $json['quantity'];
        

        if ( $_SESSION['userId'] != NULL)
        {
            $cid = addProductCart($user_id, $product_id, $quantity);
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
    else if($http_verb == 'delete')
    {

      try
      {
         $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);
            
       
        $user_id = $_SESSION['userId'];
        

        if ( $_SESSION['userId'] != NULL)
        {
            deleteProduct($user_id);
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