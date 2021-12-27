<?php
    include 'redirect_if_not_authentificated.php';
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
    <title>Личный кабинет</title>
    <script type="text/javascript" 
    src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="http://site.local/session_timeout.js"></script>
    <script type="text/javascript"> 
        $(function(){
          $('#navbarContainer').load('navbar.html', function() {
                $('#navbarStartingContent').html(
                  '<a class="navbar-brand" href="#">Система оформления заграничных паспортов</a>'  
                );
              });
        });
    </script>

    <script type="text/javascript" src="applications.js"></script>
  </head>
  <body>
    <div id="navbarContainer"></div>
    <div class="container">
      <div class="row mt-3">
        <div class="col">
            <div class="alert alert-warning" role="alert" id="passport_alert"
                 style="display: none;">
                <p>
                    <a href="account/passport.php">
                        Укажите данные паспорта</a>, чтобы можно было
                        заполнить заявление
                </p>
            </div>
            <div class="alert alert-warning" role="alert" 
                 id="registration_alert" style="display: none;">
                <p>Укажите данные 
                    <a href="account/registration_place.php">
                        о прописке</a>
                  или <a href="account/temporary_registration_place.php">
                        временной регистрации</a>, чтобы можно было
                        заполнить заявление
                </p>
            </div>
          <div class="card">
            <div class="card-body">
              <h2>Ваши заявления</h2>
              <ul class="list-group" id="applications_list">
                
                <li class="list-group-item">
                  <div class="d-grid gap-2">
                    <a class="d-grid gap-2" href="applications/create.php">
                        <button type="button" class="btn btn-outline-success" 
                                id="apply_button"
                                disabled>
                          + Подать заявление
                        </button>
                    </a>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
