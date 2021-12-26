<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
  <head>
    <meta charset="UTF-8">
    <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
    rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
    crossorigin="anonymous">
        <script type="text/javascript" 
        src="http://code.jquery.com/jquery-latest.min.js"></script>
    <title></title>
  </head>
  <body>
    <div class="container">
        <div class="row mt-3">
            <div class="col">

        <?php
        include 'custom_form.php';
            $form = new CustomForm(
                    name: 'myForm',
                    fields: [
                        new CustomTextField(
                            name: 'asdasd',
                            label: 'auto',
                            verbose_name_nom: 'Поле',
                            verbose_name_acc: 'Поле',
                            required: true,
                            regexp: '^[^A-Za-z]+$',
                            maxlength: 10,
                            hint: "Используйте кириллицу",
                            value: "testtest" 
                        ),
                        new CustomDateField(
                            name: 'adate',
                            label: 'auto',
                            verbose_name_nom: 'Дата',
                            verbose_name_acc: 'Дату',
                            required: true,
                            before: "2030-10-10",
                            after: "-P3Y",
                            hint: "Дата должна быть между..."
                        )
                    ]
                    );
            
            echo $form->render();
            echo password_hash('Sergey_115',
                                    PASSWORD_DEFAULT)
        ?>
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="validateForm.js"></script> 
  </body>
</html>
