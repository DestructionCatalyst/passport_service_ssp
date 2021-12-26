<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Регистрация</title>
  </head>
  <body>
    <?php
        $hostname = "172.20.0.2";    // database hostname (from docker inspect)
        $usernamedb = "root";    // database username
        $passworddb = "root";        // database password
        $dbName = "passport_service";        // database name
        include 'mysqldb.php';
        include 'redirect.php';

        $db = new MySQLDB("/usr/local/etc/db_config");
        
        function validateDate($date, $format = 'Y-m-d'){
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) === $date;
        }
        
        $hint = '';

        $safePost = filter_input_array(INPUT_POST, [
         "last_name" => FILTER_SANITIZE_STRING,
         "first_name" => FILTER_SANITIZE_STRING,
         "patronym" => FILTER_SANITIZE_STRING,
         "email" => FILTER_VALIDATE_EMAIL,
         "phone" => FILTER_SANITIZE_STRING,
         "sex" => FILTER_SANITIZE_STRING,
         "birth_date" => FILTER_SANITIZE_STRING,
         "birth_place" => FILTER_SANITIZE_STRING,
         "password" => FILTER_DEFAULT,
         "repeat_password" => FILTER_DEFAULT   
        ]);
        
        if ($safePost and $safePost["last_name"] and $safePost["first_name"] and 
                $safePost["birth_date"] and $safePost["email"] and 
                $safePost["phone"] and $safePost["sex"] and 
                $safePost["birth_place"] and $safePost["password"] and 
                $safePost["repeat_password"])
        {
            $checks_passed = true;
            preg_match("/[А-ЯЁ][а-яё]+/", $safePost["last_name"], $matches);
            if (!$matches){
                $checks_passed = false;
                $hint = $hint."Фамилия должна начинаться с заглавной буквы. "
                        . "Для ввода используйте кириллицу.</br>";
            }
            preg_match("/[А-ЯЁ][а-яё]+/", $safePost["first_name"], $matches);
            if (!$matches){
                $checks_passed = false;
                $hint = $hint."Имя должно начинаться с заглавной буквы. "
                        . "Для ввода используйте кириллицу.</br>";
            }
            preg_match("/[А-ЯЁ][а-яё]+/", $safePost["patronym"], $matches);
            if ($safePost["patronym"] and (!$matches)){
                $checks_passed = false;
                $hint = $hint."Отчество должно начинаться с заглавной буквы. "
                        . "Для ввода используйте кириллицу."
                        . "Если у Вас нет отчества, оставьте поле пустым.</br>";
            }
            if (!filter_var($safePost["email"], FILTER_VALIDATE_EMAIL)) {
                $checks_passed = false;
                $hint = $hint."Введите действительный e-mail адрес</br>";
            }
            preg_match("/\+\d{11}/", $safePost["phone"], $matches);
            if (!$matches){
                $checks_passed = false;
                $hint = $hint."Введите действительный номер телефона</br>";
            }
            if($safePost["sex"] != 'М' and $safePost["sex"] != 'Ж'){
                $checks_passed = false;
                $hint = $hint."Пожалуйста, укажите свой пол</br>";
            }
            if(!validateDate($safePost["birth_date"], "Y-m-d")){
                $checks_passed = false;
                $hint = $hint."Укажите корректную дату рождения</br>";
            }
            if (!preg_match("/[^A-Za-z]+/",$safePost["birth_place"]))
            {
                $checks_passed = false;
                $hint = $hint."Укажите место рождения на русском языке</br>";
            }
            $birth_date = new DateTime($safePost["birth_date"]);
            $eightteen_date = clone $birth_date;
            $eightteen_date->add(new DateInterval('P18Y'));
            if (new DateTime() < $eightteen_date){
                $checks_passed = false;
                $hint = $hint."Если Вам не исполнилось 18 лет, "
                        . "Вы не сможете воспользоваться услугами данного "
                        . "сервиса. Вы можете получить заграничный паспорт"
                        . "лично явившись в УМВД с одним из родителей.</br>";
            }
            if (strlen($safePost["password"]) < 8) {
                $checks_passed = false;
                $hint = $hint."Пароль должен содержать минимум 8 символов.</br>";
            }
            if(!preg_match("#[0-9]+#",$safePost["password"])) {
                $checks_passed = false;
                $hint = $hint."Пароль должен содержать хотя бы 1 цифру</br>";
            }
            if(!preg_match("#[A-ZА-ЯЁ]+#",$safePost["password"])) {
                $checks_passed = false;
                $hint = $hint."Пароль должен содержать хотя бы одну"
                        . " заглавную букву</br>";
            }
            if(!preg_match("#[a-zа-яё]+#",$safePost["password"])) {
                $checks_passed = false;
                $hint = $hint."Пароль должен содержать хотя бы одну"
                        . " строчную букву</br>";
            }
            if(!preg_match("#[^A-ZА-ЯЁa-zа-яё0-9]+#",$safePost["password"])) {
                $checks_passed = false;
                $hint = $hint."Пароль должен содержать хотя бы один"
                        . " специальный символ</br>";
            }
            if($safePost["password"] != $safePost["repeat_password"]){
                $checks_passed = false;
                $hint = $hint."Введенные пароли не совпадают</br>";
            }
            
            if($checks_passed){
                $db->insert('user', 
                        array("first_name", "last_name", "patronym", "email",
                            "phone_number", "sex", "birth_date", "birth_place",
                            "password"), 
                        array(array(
                            $safePost["first_name"],
                            $safePost["last_name"],
                            $safePost["patronym"],
                            $safePost["email"],
                            $safePost["phone"],
                            $safePost["sex"],
                            $safePost["birth_date"],
                            $safePost["birth_place"],
                            password_hash($safePost["password"],
                                    PASSWORD_DEFAULT)
                        )));
                        redirect('index.php');
            }
        }
        else {
            $hint = "Заполните все обязательные поля";
        }
        
        
    ?>
    <div class="central_column">
      <h1 class="central_header">Регистрация, шаг 1</h1>
      <form name="register" method="POST" action="register.php">
        <?php
        if($hint){
            print('<p class="form_hint">'.$hint.'</p>');
        }
        ?>
        <p><input type="text" class="auth_field" required="true" maxlength="64"
                  pattern="^[А-ЯЁ][а-яё]+$"
                  name="last_name" placeholder="Фамилия"></p>
        <p><input type="text" class="auth_field" required="true" maxlength="64"
                  pattern="^[А-ЯЁ][а-яё]+$"
                  name="first_name" placeholder="Имя"></p>
        <p><input type="text" class="auth_field" maxlength="64"
                  pattern="(^[А-ЯЁ][а-яё]+$|^$)"
                  name="patronym" placeholder="Отчество (при наличии)"></p>
        <p><input type="email" class="auth_field" required="true" maxlength="255"
                  name="email" placeholder="Адрес электронной почты"></p>
        <p><input type="tel" class="auth_field" required="true" maxlength="12"
                  pattern="^\+\d{11}$"
                  name="phone" placeholder="Номер мобильного телефона"></p>
        <p><span class="hint_text">Пол</span></br>
          <input type="radio" id="sex_m" name="sex" value="М">
          <label class="hint_text" for="sex_m">М</label>
          <input type="radio" id="sex_f" name="sex" value="Ж">
          <label class="hint_text" for="sex_f">Ж</label></p>
        <p><span class="hint_text">Дата рождения</span></br>
          <input type="date" class="auth_field" required="true"
                  name="birth_date" placeholder="Дата рождения"></p>
        <p><input type="text" class="auth_field" required="true" maxlength="255"
                  name="birth_place" placeholder="Место рождения (как в паспорте)"></p>
        
        <p><input type="password" class="auth_field" required="true"
                  name="password" placeholder="Пароль"></p>
        <p><input type="password" class="auth_field" required="true"
                  name="repeat_password" placeholder="Подтвердите пароль"></p>
        <p>
          <input type="submit" name="send" value="Зарегистрироваться">
        </p>
      </form>
    </div>
  </body>
</html>
