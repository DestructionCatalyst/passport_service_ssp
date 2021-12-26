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
    <title>Обработка заявления</title>
    <script type="text/javascript" 
    src="http://site.local/getUrlParameter.js"></script>
    <script type="text/javascript" 
    src="http://site.local/loadApplication.js"></script>
    <script type="text/javascript">
    function updateButtons(status){
        $('#processApplication').hide();
        $('#confirmPassportReady').hide();
        $('#confirmPassportIssued').hide();
        if(status === 'Принято в обработку'){
            $('#sendToRework').prop("disabled", false);
            $('#denyApplication').prop("disabled", false);
            $('#processApplication').show();
            $('textarea').prop("disabled", false);
        }
        else if(status === 'Отправлено на доработку'){
            $('#denyApplication').prop("disabled", false);
        }
        else if(status === 'Паспорт оформляется'){
            $('#denyApplication').prop("disabled", false);
            $('#confirmPassportReady').show();
        }
        else if(status === 'Паспорт готов к получению'){
            $('#sendToRework').prop("disabled", false);
            $('#denyApplication').prop("disabled", false);
            $('#confirmPassportIssued').show();
        }
    }
    
    function onLoad(data){
        updateButtons(data.application.status);
                
        $(document).on("click touchend", ".btn-control", function () {
            toSend = {
                'id': data.application.id,
                'new_state': $(this).attr('action')
            };
            if(!$(this).hasClass("btn-success")){
                comments = [];
                now = new Date();
                dateString = now.getFullYear() + '-' 
                        + now.getMonth() + '-' 
                        + now.getDate();
                $('textarea').each(function(index){
                    value = $(this).val();
                    if (value) {
                        comments.push({
                            'application_id': data.application.id,
                            'stage': index + 1,
                            'description': value,
                            'creation_date': dateString
                        });
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "../api/comment_data.php",
                    data: {'comments': comments, 'action': 'create'}
                });
            }
            $.ajax({
                type: "POST",
                url: "api/employee_applications_data.php",
                data: toSend,
                success: function () {
                    window.location.replace('index.php');
                }
            });
        });
    }
    
    function activateCommitButtons(commitButton, comment, application){
        if(application.status === "Принято в обработку"
                && comment.status === "Внесены правки"){
            $(commitButton).show();
            $(commitButton).click(function(e){
                $.ajax({
                    type: "POST",
                    url: "../api/comment_data.php",
                    data: {'comment_id': comment.id, 'action': 'approve'},
                    success: function(){
                        commitButton.prop('disabled', true);
                        $(commitButton).parent()
                                .parent()
                                .find('textarea')
                                .val('');
                        
                    }
                });
                e.preventDefault();
            });
        }
    }
    
    $(document).ready(function() {

        loadApplication(onLoad, activateCommitButtons);

    });
    </script>
    <script type="text/javascript"> 
        $(function(){
            $('#navbarContainer').load('../navbar.html', function() {
                $('#navbarStartingContent').html(
                  '<a href="index.php">\
                    <button type="button" class="btn btn-danger ms-3 me-3">\
                      < На главную</button></a>'
                );
            });
            $.getJSON('api/employee_name_data.php', '', function(data){
            if (data !== null){
                $('#full_name').html(data['full_name']);
                $('#full_name').attr('href', 'index.php');
            }
            });
        });
    </script>
  </head>
  <body>
    <div id="navbarContainer"></div>
    <div class="container">
      <div class="row mt-3">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <div id="application"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
</html>
