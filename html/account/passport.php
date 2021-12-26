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
    <title>Паспортные данные</title>
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
    <script type="text/javascript" src="api/loadApiData.js"></script>
    <script type="text/javascript">
        loadDataFromApi('passport_data.php');
    </script>
    <script type="text/javascript" src="http://site.local/validateFunction.js">
    </script> 
    <script type="text/javascript">
    $(document).ready(function() {
        $("#passportForm").on("submit", function(e) {
            if (validate()){
                var dataString = $(this).serialize();
                console.log('sumbit');
                $.ajax({
                    type: "POST",
                    url: "api/passport_data.php",
                    data: dataString,
                    success: function () {
                        $("#dangerAlertBlock").hide();
                        $("#successAlertBlock").html("Данные сохранены");
                        $("#successAlertBlock").show();
                    }
                  });
                
            }
            e.preventDefault();
        });
    });
    </script>
  </head>
  <body>
      <?php
        include '../custom_form.php';
        include '../redirect.php';
        include 'forms.php';
        
        $request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        
        if (!isset($_SESSION['userid'])) {
            redirect('../auth.php');
        } else {
            
            /*if($request_method == 'POST'){
                if ($form->validate()){
                    echo '<script type="text/javascript">'
                    . '$.post('
                            . '".api/passport_data.php",'
                            . '' . json_encode($form->getFormData())
                            . ');'
                    . '</script>';
                }
            }*/
        }
      ?>
    <div class="container">
      <div class="row mt-3">
        <div class="col">
          <div id="sideMenu"></div>
        </div>
        <div class="col-9">
          <h1>Паспортные данные</h1>
          <?php echo $passportForm->render()?>
        </div>
      </div>
    </div>
  </body>
</html>
