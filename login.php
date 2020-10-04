<?php
  session_start();

  function validateUser($email, $password)
  {
    $userId = 0;

    try 
    {
      $db = new PDO('mysql:dbname=ecommerce; host=localhost'); 
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
      
      if(filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password)> 0)
      {
        $sql = $db->prepare('SELECT * FROM user WHERE email = :email');
        $sql->bindValue(':email', $email);

        $sql->execute();
        
        if($user = $sql->fetch(PDO::FETCH_ASSOC))
        {
         
         
          
          if(password_verify($password, $user['password']))

            $userId = $user['uid'];
        }
      }
    } 
    catch(PDOException $e) 
    {
      echo $e->getMessage();
    }

    return $userId;
  }

  try
  {
    $http_verb = strtolower($_SERVER['REQUEST_METHOD']);

    if ($http_verb == 'post')
    {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);         
            
        $email = $json['email']; 
        $password = ($json['password']);


        echo $password;
        echo $email;
        

        $userId = validateUser($email, $password);

       
        $myObj = new stdClass();
        $myObj->isLoggedIn = $userId > 0;
        $myObj->message = $userId > 0 ? "" : "email/password does not match";
        
        $_SESSION['userId'] = $userId;

        echo json_encode($myObj);
    }
    else if ($http_verb == 'get')
    {
        
        $myObj = new stdClass();
        $myObj->isLoggedIn = $_SESSION['isLoggedIn'];
        echo json_encode($myObj);
        }
    else if ($http_verb == 'delete')
    {
        
        $_SESSION['isLoggedIn'] = false;

        $myObj = new stdClass();
        $myObj->isLoggedIn = false;
        echo json_encode($myObj);
    }
  }
  catch (Exception $e) 
  {
    $myObj = new stdClass();
    $myObj->isLoggedIn = false;
    $myObj->message = "something went wrong, please try again later";
    echo json_encode($myObj);
  }
?>
