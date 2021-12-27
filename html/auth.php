<?php
    session_start();
 ?>
<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
 <head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="style.css">
  <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet" 
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
  <title>Вход</title>
 </head>
 <body>
  <?php
   include 'mysqldb.php';
   include 'redirect.php';
   
   $db = new MySQLDB("/usr/local/etc/db_config");
   $login_hint = '';
   $password_hint = '';
   
   $safePost = filter_input_array(INPUT_POST, [
    "login" => FILTER_SANITIZE_EMAIL,
    "password" => FILTER_SANITIZE_STRING,
    "type" => FILTER_DEFAULT
   ]);
   
   if (empty($safePost["login"])){
       $login_hint = 'Введите логин';
   }
   else if (empty($safePost["password"])){
       $password_hint = 'Введите пароль';
   }
   else {
        if ($safePost['type'] == 'user'){
            $result=$db->selectFirst('user', '*', 
                    "email = '".$safePost['login'].
                    "' OR phone_number = '".$safePost['login']."';");
            if($result){
                $password_hash = $result['password'];
                $auth = password_verify($safePost['password'], $password_hash);
                if($auth){
                    $_SESSION['userid'] = $result['id'];
                    redirect("http://site.local/index.php");
                }
                else{
                    $password_hint = 'Неверный логин или пароль';
                }
            }
            else{
                $password_hint = 'Неверный логин или пароль';
            }
        }
        elseif ($safePost['type'] == 'employee'){
            $result=$db->selectFirst('employee', '*', 
                    "login = '" . $safePost['login'] . "'");
            if($result){
                $password_hash = $result['password'];
                $auth = password_verify($safePost['password'], $password_hash);
                echo 'checking';
                if($auth){
                    $_SESSION['employeeid'] = $result['id'];
                    redirect("http://site.local/employee/index.php");
                }
                else{
                    $password_hint = 'Неверный логин или пароль';
                }
            }
            else{
                $password_hint = 'Неверный логин или пароль';
            }
        }
        
    }
   
  ?>
  <div class="center_auth_frame">
   <div class="tabs">
    <div class="tab">
      <input type="radio" id="tab1" name="tab-group" checked>
      <label for="tab1" class="tab-title">Вход для пользователей</label> 
      <section class="tab-content">
      <form name="login" method="POST" action="auth.php">
       <p><input type="text" class="auth_field"
                 name="login" placeholder="Логин (телефон или e-mail)"></p>
       <?php
        if($login_hint){
            print('<p class="form_hint">'.$login_hint.'</p>');
        }
       ?>
       <p><input type="password" class="auth_field" 
                 name="password" placeholder="Пароль"></p>
       <?php
        if($password_hint){
            print('<p class="form_hint">'.$password_hint.'</p>');
        }
       ?> 
       <p>
        <input type="hidden" name="type" value="user">
        <input type="submit" name="send" class="btn btn-primary" value="Войти">
        <a href="/register.php" class="register_link">Регистрация</a>
       </p>
      </form>
     </section>
    </div> 
    <div class="tab">
     <input type="radio" id="tab2" name="tab-group">
     <label for="tab2" class="tab-title">Вход для сотрудников</label> 
     <section class="tab-content">
      <form name="login" method="POST" action="auth.php">
       <p><input type="text" class="auth_field"
                 name="login" placeholder="Логин"></p>
       <?php
        if($login_hint){
            print('<p class="form_hint">'.$login_hint.'</p>');
        }
       ?>
       <p><input type="password" class="auth_field" 
                 name="password" placeholder="Пароль"></p>
       <?php
        if($password_hint){
            print('<p class="form_hint">'.$password_hint.'</p>');
        }
       ?>
       <p>
        <input type="hidden" name="type" value="employee">
        <input type="submit" name="send" class="btn btn-primary" value="Войти">
       </p>
      </form>
     </section>
    </div>
   </div>
  </div>
 </body>
</html>
