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
    <title>Временная регистрация</title>
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet" 
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <script type="text/javascript" 
        src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="http://site.local/session_timeout.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            dataType: "json",
            url: '../api/user_extended_data.php', 
            data: '', 
            success: function(data){
                if (data !== null){
                    if (!data['has_open_applications']){
                       $(':submit').prop('disabled', false);
                       return;
                    }
                }
                $(':submit').prop('disabled', true);
            },
            error: function(){
                $(':submit').prop('disabled', true);
            }
        });
    });
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
    <script type="text/javascript"> 
        $(function(){
          $("#sideMenu").load('account_side_menu.html'); 
        });
    </script>
  </head>
  <body>
      <?php
        include '../mysqldb.php';
        include '../redirect.php';

        if (!$_SESSION['userid']) {
            redirect('../auth.php');
        } else {
            $db = new MySQLDB("/usr/local/etc/db_config");
            $request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
            $existing_data = '';
            if($request_method == 'GET'){
                $existing_data = $db->selectFirst("temporary_registration", 
                    ["address", "start_date", "end_date", "registration_organ"], 
                    "user_id = '".$_SESSION['userid']."'");

                if($existing_data){
                    $address = $existing_data['address'];
                    $start_date = $existing_data['start_date'];
                    $end_date = $existing_data['end_date'];
                    $organ = $existing_data['registration_organ'];
                }
            }
            elseif($request_method == 'POST'){
                $safePost = filter_input_array(INPUT_POST, [
                    "address" => FILTER_SANITIZE_STRING,
                    "registrationStartDate" => FILTER_SANITIZE_STRING,
                    "registrationEndDate" => FILTER_SANITIZE_STRING,
                    "registrationOrgan" => FILTER_SANITIZE_STRING,
                    "noRegistration" => FILTER_SANITIZE_STRING
                   ]);
                if($safePost){
                    if ($safePost["noRegistration"] != 'Yes'){
                        $checks_passed = true;
                        if (!preg_match("/[^A-Za-z]+/",$safePost["address"]))
                        {
                            $checks_passed = false;
                        }
                        $start_date = 
                                new DateTime($safePost["registrationStartDate"]);
                        if ($start_date > new DateTime()){
                            $checks_passed = false;
                        }
                        $end_date = 
                                new DateTime($safePost["registrationEndDate"]);
                        if ($end_date < new DateTime()){
                            $checks_passed = false;
                        }
                        if (!preg_match("/[^A-Za-z]+/",
                                $safePost["registrationOrgan"]))
                        {
                            $checks_passed = false;
                        }
                        if ($checks_passed){
                            $db->addOrReplace(
                                "temporary_registration", 
                                array("address", "start_date", "end_date",
                                      "registration_organ", "user_id"), 
                                array($safePost["address"], 
                                    $safePost["registrationStartDate"], 
                                    $safePost["registrationEndDate"], 
                                    $safePost["registrationOrgan"], 
                                    $_SESSION['userid']),
                                    "user_id"
                                    );
                        }
                        else{
                            echo "something's wrong";
                        }
                    }
                    else {
                        $db->delete("temporary_registration", 
                                "user_id = ".$_SESSION['userid']);
                    }
                    redirect('?success=true');
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
            <h1>Временная регистрация</h1>
            <div class="alert alert-danger" id="alertBlock" 
                 role="alert" style="display: none;"></div>
            <div class="alert alert-success" id="successAlertBlock" '
                . 'role="alert" style="display: none;"></div>
            <form id="regForm" method="POST" action="temporary_registration_place.php">
              <div class="mb-3">
                <label for="registrationAddress" class="form-label">
                  Адрес места регистрации</label>
                <input type="text" class="form-control" id="address"
                       name="address" verbose_name="адрес прописки"
                       <?php if($existing_data){echo 'value="'.$address.'"';}?>>
              </div>
              <div class="mb-3">
                <label for="registrationStartDate" class="form-label">
                  Дата начала регистрации</label>
                <input type="date" class="form-control" id="registrationStartDate"
                       name="registrationStartDate" restriction="past"
                       verbose_name="дату начала регистрации"
                       <?php if($existing_data){echo 'value="'.$start_date.'"';}?>>
              </div>
              <div class="mb-3">
                <label for="registrationEndDate" class="form-label">
                  Дата окончания регистрации</label>
                <input type="date" class="form-control" id="registrationEndDate"
                       name="registrationEndDate" restriction="future"
                       verbose_name="дату окончания регистрации"
                       <?php if($existing_data){echo 'value="'.$end_date.'"';}?>>
              </div>
              <div class="mb-3">
                <label for="registrationOrgan" class="form-label">
                  Регистрационный орган</label>
                <input type="text" class="form-control" id="registrationOrgan"
                       name="registrationOrgan"
                       verbose_name="название регистрационного органа"
                       <?php if($existing_data){echo 'value="'.$organ.'"';}?>>
              </div>
              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" 
                       id="noRegistration" name="noRegistration" value="Yes">
                <label class="form-check-label" for="noRegistration">
                  У меня нет временной регистрации</label>
              </div>
              <input type="submit" id="submit" class="btn btn-primary" 
                     value="Подтвердить">
              </input>
            </form>
            
          <div>
        </div>
      </div>
      <script type="text/javascript" src="regPlaceFormValidate.js"></script> 
  </body>
</html>
