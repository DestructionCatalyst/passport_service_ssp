<?php
include '../redirect_if_not_employee.php';
?>

<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style.css">
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet" 
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <script type="text/javascript" 
        src="http://code.jquery.com/jquery-latest.min.js"></script>
    <title>Обработка заявлений</title>
    <script type="text/javascript"> 
        $(function(){
          $('#navbarContainer').load('../navbar.html', function() {
                $('#navbarStartingContent').html(
                  '<a class="navbar-brand" href="#">Управление оформлением заграничных паспортов</a>'  
                );
                $.getJSON('api/employee_name_data.php', '', function(data){
                    if (data !== null){
                        $('#full_name').html(data['full_name']);
                    }
                });
              });
        });
    </script>
    <script type="text/javascript">
    let options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric'
    };
    $(document).ready(function() {
        
        function loadApplications(data, targetId, itemId){
            if (data !== null){
                applicationsList = $('#'+targetId);
                applicationsList.html('');
                for (let index in data) {
                    object = data[index];

                    let app_date = new Date(Date.parse(object.application_date))
                            .toLocaleString(undefined, options);
                    let status = object.status.toLowerCase().replaceAll(' ', '_');
                    applicationsList.append(
                        $('<li class="list-group-item" id="' 
                            + itemId + '" dbid="' + object.id + '">').append(
                            $('<div class="d-flex w-100 justify-content-between">').append(
                                $('<h5 class="mb-1">').append('Заявление'),
                                $('<small>').append('Дата подачи: ' + app_date)
                            ),
                            $('<p class="mb-1">').append('Статус: ', 
                                    $('<span class="' + status + '">')
                                            .append(object.status))
                        )
                    );
                }
            }
            
        }
        
        $(document).on("click touchend", "#newApplication", function () {
            $.ajax({
                type: "POST",
                url: "api/new_applications_data.php",
                data: {
                    "id": $(this).attr('dbid'), 
                    'lock': $('#tableLockSwitchCheck').is(':checked')
                },
                success: function () {
                    // Обновить списки
                    myApplications();
                    newApplications();
                },
                error: function(response) {
                    if (response.status === 409){
                        alert( "Это заявление уже принято в обработку другим сотрудником");
                    }
                    else {
                        alert( "Произошла ошибка при обработке запроса");
                    }
                }
            });
        });
        
        $(document).on("click touchend", "#myApplication", function () {
            window.location.replace('review.php?id='+$(this).attr('dbid'));
        });
        
        function newApplications(){
            $.getJSON('api/new_applications_data.php', '', function(data){
                loadApplications(data, 'new_applications_list', 'newApplication');
            });
        }
        
        function myApplications(){
            $.getJSON('api/employee_applications_data.php', '', function(data){
                loadApplications(data, 'applications_list', 'myApplication');
            });
        }
        
        myApplications();
        newApplications();
        setInterval(function(){
            if ($('#autoUpdateSwitchCheck').is(':checked')){
                newApplications();
            }
        }, 5000);
        
        
        
    });
    </script>
  </head>
  <body>
    <div id="navbarContainer"></div>
    <div class="container">
      <div class="row mt-3">
        <div class="col-sm">
            <div class="card">
                <div class="card-body">
                    <h3>Заявления в обработке</h3>
                    <ul class="list-group" id="applications_list">
                    </ul>
                </div>
            </div>   
        </div>
        <div class="col-sm">
            <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                        <h3>Новые заявления</h3>
                    </div>
                    <div class="col">
                      <div class="d-flex justify-content-end">
                        <div class="form-check form-switch">
                          <label class="form-check-label" for="flexSwitchCheckDefault">Блокировка</label>
                          <input class="form-check-input" type="checkbox" id="tableLockSwitchCheck" checked>
                        </div>
                        <div class="form-check form-switch mx-2">
                          <label class="form-check-label" for="flexSwitchCheckDefault">Автоообновление</label>
                          <input class="form-check-input" type="checkbox" id="autoUpdateSwitchCheck" checked>
                        </div>
                      </div>
                    </div>
                  </div>
                  <ul class="list-group" id="new_applications_list">
                  </ul>
                </div>
            </div> 
        </div>
      </div>
    </div>
  </body>
</html>
