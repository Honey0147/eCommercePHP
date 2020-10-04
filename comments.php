<?php

 session_start();

  $GLOBALS['db'] = new PDO('mysql:dbname=ecommerce; host=localhost',"root",""); 
  $GLOBALS['db']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 $myObj = new stdClass();

  function addComments($uid, $pid, $ratings, $image, $comment_text)
  {

    
    $sql = $GLOBALS['db']->prepare('INSERT INTO comments (uid, pid, ratings ,image, comment_text)'.'VALUES (:uid, :pid, :ratings, :image, :comment_text)');
 
    $sql->bindValue(':uid', $uid);
    $sql->bindValue(':pid', $pid);
    $sql->bindValue(':ratings', $ratings);
     $sql->bindValue(':image', $image);
     $sql->bindValue(':comment_text', $comment_text);
  
    $sql->execute();

    return $GLOBALS['db']->lastInsertId();
   
  }

  function ifAddedProduct($uid)
  {
    $cmd =  'SELECT product_id FROM cart WHERE user_id = :user_id';
    $sql = $GLOBALS['db']->prepare($cmd);
    $sql->bindValue(':user_id', $uid);

    $sql->execute();
 
 $result = $sql->fetch(PDO::FETCH_NUM);
    return $result[0];
  }

  function deleteComments($uid)
  {

     try 
      {
      $db = new PDO('mysql:dbname=ecommerce; host=localhost'); 
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = $db->prepare('DELETE FROM comments WHERE uid = :uid');
        $sql->bindValue(':uid', $uid);

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
            
       // $cid = $json['cid'];
        $uid = $_SESSION['userId'];
        $pid = ifAddedProduct($_SESSION['userId']);
        $ratings = $json['ratings'];
        $image = $json['image'];
        $comment_text = $json['comment_text'];


        if ( $_SESSION['userId'] != NULL)
        {
         
          echo $_SESSION['userId'];

          if(ifAddedProduct($_SESSION['userId']))
          {

            $comment_id = addComments($uid, $pid, $ratings, $image, $comment_text);

            echo $comment_id;

            $myObj->cAdded = $comment_id > 0;
            $myObj->cAdded = true;
            $myObj->message = $comment_id > 0 ? "" : "Something went wrong...";
            $myObj->product_id = $pid;
            echo json_encode($myObj);
          }
          else
          {
           
            $myObj->cAdded = false;
            $myObj->message = "No product added to cart..";
            echo json_encode($myObj);
          }
            
        }
        else
        {
            $myObj = new stdClass();
            $myObj->cAdded = false;
            $myObj->message = "Please login first..";
            echo json_encode($myObj);
        }
    }
    catch (Exception $e) 
    {
      $myObj = new stdClass();
      $myObj->cAdded = false;
      $myObj->message = "something went wrong, please try again later";
      echo json_encode($myObj);
    }
  }
  else if($http_verb == 'delete')
    {

      try
      {
         $delete = trim(file_get_contents("php://input"));
        $json = json_decode($delete, true);
            
       
        $user_id = $_SESSION['userId'];
        

        if ( $_SESSION['userId'] != NULL)
        {
            deleteComments($user_id);
        }
        else
        {
            $myObj = new stdClass();
            $myObj->cDeleted = false;
            $myObj->message = "Please login first..";
            echo json_encode($myObj);
        }
      }
      catch (Exception $e) 
      {
      $myObj = new stdClass();
      $myObj->cDeleted = false;
      $myObj->message = "something went wrong, please try again later";
      echo json_encode($myObj);
      }
     
    }
?>