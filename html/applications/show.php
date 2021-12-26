<?php
    include '../redirect_if_not_authentificated.php';
?>
<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
  <head>
    <meta charset="UTF-8">
    <title>3аявление</title>
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet" 
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <script type="text/javascript" 
    src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" 
    src="http://site.local/getUrlParameter.js"></script>
    <script type="text/javascript" 
    src="http://site.local/loadApplication.js"></script>
    <script type="text/javascript">
        function activateControlButton(data){
            if(data.application.status === 'Отправлено на доработку'){
                ready = $('.btn-commit').toArray().every(function(commitButton){
                   return $(commitButton).prop('disabled') 
                           || $(commitButton).is(":hidden");
                });
                if(ready){
                    $('#processApplication').removeProp('disabled');
                    $('#processApplication').click(function(){
                        $.ajax({
                                type: "POST",
                                url: "api/application_resend.php",
                                data: {'id': data.application.id},
                                success: function(){
                                    window.location.replace('../index.php');
                                }
                            });
                        
                    });
                }   
            }
        }
        
        function activateCommitButtons(commitButton, comment, application){
            if(application.status === "Отправлено на доработку"
                    && comment.status === "К исправлению"){
                $(commitButton).show();
                $(commitButton).click(function(e){
                    $.ajax({
                        type: "POST",
                        url: "../api/comment_data.php",
                        data: {'comment_id': comment.id, 'action': 'fix'}
                        success: function(){
                            commitButton.prop('disabled', true);
                            $(commitButton).parent()
                                    .find('.last_change_date')
                                    .html(dateFormat(new Date()));
                            activateControlButton();
                            e.preventDefault();
                        }
                    });
                    
                });
            }
        }
        
        $(document).ready(function() {
            loadApplication(activateControlButton, 
            activateCommitButtons);
        });  
    </script>
    <script type="text/javascript"> 
        $(function(){
            $('#navbarContainer').load('../navbar.html', function() {
                $('#navbarStartingContent').html(
                  '<a href="../index.php">\
                    <button type="button" class="btn btn-danger ms-3 me-3">\
                      < На главную</button></a>'
                );
            });
            $.getJSON('../api/user_extended_data.php', '', function(data){
            if (data !== null){
                $('#full_name').html(data['full_name']);
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
    
  </body>
</html>
