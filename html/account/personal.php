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
    <title>Личные данные</title>
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet" 
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <script type="text/javascript" 
        src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript"> 
        $(function(){
          $("#sideMenu").load('account_side_menu.html'); 
        });
    </script>
    <script type="text/javascript" src="../api/loadApiData.js"></script>
    <script type="text/javascript">
        loadDataFromApi('user_data.php');
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            let searchParams = new URLSearchParams(window.location.search);
            if (searchParams.has('success')){
                $("#successAlertBlock").html("Данные сохранены");
                $("#successAlertBlock").show();
            }
        });
    </script>
  </head>
  <body>
      <?php
        include '../custom_form.php';
        include '../redirect.php';
        include '../send_request.php';
        
        $request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        
        if (!isset($_SESSION['userid'])) {
            redirect('../auth.php');
        } else {
            $form = new CustomForm(
                    name: 'personalDataForm',
                    fields: [
                        new CustomTextField(
                                name: 'last_name', 
                                label: 'auto', 
                                verbose_name_nom: 'Фамилия', 
                                verbose_name_acc: 'Фамилию', 
                                required: true, 
                                regexp: '^[А-ЯЁ][а-яё]+$', 
                                hint: "Фамилия должна начинаться с заглавной буквы."
                                . " Для ввода используйте кириллицу.",
                                maxlength: 64),
                        new CustomTextField(
                                name: 'first_name', 
                                label: 'auto', 
                                verbose_name_nom: 'Имя', 
                                verbose_name_acc: 'Имя', 
                                required: true, 
                                regexp: '^[А-ЯЁ][а-яё]+$', 
                                hint: "Имя должно начинаться с заглавной буквы."
                                . " Для ввода используйте кириллицу.",
                                maxlength: 64),
                        new CustomTextField(
                                name: 'patronym', 
                                label: 'auto', 
                                verbose_name_nom: 'Отчество', 
                                verbose_name_acc: 'Отчество', 
                                required: true, 
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
                                name: 'phone_number', 
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
                    ],
                action: "?success=true" 
            );
            if($request_method == 'POST'){
                if ($form->validate()){
                    echo '<script type="text/javascript">'
                    . '$.post('
                            . '"../api/user_data.php",'
                            . '' . json_encode($form->getFormData())
                            . ');'
                    . '</script>';
                }
            }
        }
      ?>
    
    <div class="container">
      <div class="row mt-3">
        <div class="col">
          <div id="sideMenu"></div>
        </div>
        <div class="col-9">
          <h1>Личные данные</h1>
          <?php echo $form->render()?>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="../validateForm.js"></script> 
  </body>
</html>
