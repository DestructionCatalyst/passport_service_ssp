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
    <script type="text/javascript" 
        src="http://site.local/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            let searchParams = new URLSearchParams(window.location.search);
            if (searchParams.has('success')){
                window.location.replace('auth.php');
            }
            $('#phone').after('<p class="mt-3 form-label">Пол*' +
                '<ul class="list-group list-group-horizontal list-unstyled"><li class="me-2">' +
                '<input type="radio" id="sex_m" name="sex" value="М" checked class="form-check-input">' +
                '<label class="form-label" for="sex_m">М</label>' +
                '<li class="ms-2"></li>' +
                '<input type="radio" id="sex_f" name="sex" value="Ж" class="form-check-input">' +
                '<label class="form-label" for="sex_f">Ж</label>' +
                '</li></ul></p>'
            );
        });
    </script>
    <title>Регистрация</title>
  </head>
  <body>
    <?php
        include 'mysqldb.php';
        include 'redirect.php';
        include 'custom_form.php';

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
        
        setlocale(LC_ALL, "ru_RU.UTF-8");
        if ($safePost and $safePost["last_name"] and $safePost["first_name"] and 
                $safePost["birth_date"] and $safePost["email"] and 
                $safePost["phone"] and $safePost["sex"] and 
                $safePost["birth_place"] and $safePost["password"] and 
                $safePost["repeat_password"])
        {
            $checks_passed = true;
            preg_match("/^[А-ЯЁ][а-яё]+(\-[А-ЯЁ][а-яё]+)?$/u", 
                    $safePost["last_name"], $matches);
            if (!$matches){
                $checks_passed = false;
                $hint = $hint."Фамилия должна начинаться с заглавной буквы. "
                        . "Для ввода используйте кириллицу.</br>";
            }
            preg_match("/^[А-ЯЁ][а-яё]+(\-[А-ЯЁ][а-яё]+)?$/u", $safePost["first_name"], $matches);
            if (!$matches){
                $checks_passed = false;
                $hint = $hint."Имя должно начинаться с заглавной буквы. "
                        . "Для ввода используйте кириллицу.</br>";
            }
            preg_match("/^[А-ЯЁ][а-яё]+$/", $safePost["patronym"], $matches);
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
            preg_match("/^\+\d{11}$/", $safePost["phone"], $matches);
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
            if (!preg_match("/^[^A-Za-z]+$/",$safePost["birth_place"]))
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
                        . "сервиса. Вы можете получить заграничный паспорт "
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

        $form = new CustomForm(
                name: 'registrationForm',
                fields: [
                        new CustomTextField(
                                name: 'last_name', 
                                label: 'auto', 
                                verbose_name_nom: 'Фамилия', 
                                verbose_name_acc: 'Фамилию', 
                                required: true, 
                                regexp: '^[А-ЯЁ](\'[А-ЯЁ])?[а-яё]+(\-[А-ЯЁ](\'[А-ЯЁ])?[а-яё]+)?$', 
                                hint: "Фамилия должна начинаться с заглавной буквы."
                                . " Для ввода используйте кириллицу.",
                                maxlength: 64),
                        new CustomTextField(
                                name: 'first_name', 
                                label: 'auto', 
                                verbose_name_nom: 'Имя', 
                                verbose_name_acc: 'Имя', 
                                required: true, 
                                regexp: '^[А-ЯЁ][а-яё]+(\-[А-ЯЁ][а-яё]+)?$', 
                                hint: "Имя должно начинаться с заглавной буквы."
                                . " Для ввода используйте кириллицу.",
                                maxlength: 64),
                        new CustomTextField(
                                name: 'patronym', 
                                label: 'auto', 
                                verbose_name_nom: 'Отчество', 
                                verbose_name_acc: 'Отчество', 
                                required: false, 
                                regexp: '^[А-ЯЁ][а-яё]+$', 
                                hint: "Отчество должно начинаться с заглавной буквы."
                                . " Для ввода используйте кириллицу.",
                                maxlength: 64),
                        new CustomEmailField(
                                name: 'email', 
                                label: 'auto', 
                                verbose_name_nom: 'Адрес электронной почты', 
                                verbose_name_acc: 'Адрес электронной почты', 
                                required: true),
                        new CustomTextField(
                                name: 'phone', 
                                label: 'auto', 
                                verbose_name_nom: 'Номер телефона', 
                                verbose_name_acc: 'Номер телефона', 
                                required: true, 
                                regexp: '^\+\d{11}$', 
                                hint: "Номер телефона должен начинаться с + и "
                                . "состоять из 11 цифр",
                                maxlength: 12),
                        new CustomDateField(
                            name: 'birth_date',
                            label: 'auto',
                            verbose_name_nom: 'Дата рождения',
                            verbose_name_acc: 'Дату рождения',
                            required: true,
                            before: "-P18Y",
                            hint: "Укажите дату рождения в правильном формате. "
                            . "Если Вам не исполнилось 18 лет, "
                            . "Вы не сможете воспользоваться услугами данного "
                            . "сервиса. Вы можете получить заграничный паспорт"
                            . "лично явившись в УМВД с одним из родителей."
                        ),
                        new CustomTextField(
                                name: 'birth_place', 
                                label: 'auto', 
                                verbose_name_nom: 'Место рождения', 
                                verbose_name_acc: 'Место рождения', 
                                required: true, 
                                regexp: '^[^A-Za-z]+$', 
                                hint: "Укажите место рождения на русском языке.",
                                maxlength: 255),
                        new CustomPasswordField(
                            name: 'password', 
                            label: 'auto', 
                            verbose_name_nom: 'Пароль', 
                            verbose_name_acc: 'Пароль', 
                            required: true),
                        new CustomPasswordField(
                            name: 'repeat_password', 
                            label: 'auto', 
                            verbose_name_nom: 'Повторите пароль', 
                            verbose_name_acc: 'Повторите пароль', 
                            required: true),
                    ],
                action: "?success=true" 
        );
        
    ?>
    <div class="central_column">
      <h1 class="central_header">Регистрация</h1>
      <?php 
        echo $form->render();
      ?>
    </div>
    <script type="text/javascript" src="../validateForm.js"></script> 
  </body>
</html>
