<?php
    include '../redirect_if_not_authentificated.php';
    include 'forms.php';
?>
<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
  <head>
    <meta charset="UTF-8">
    <title>Подать заявление</title>
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet" 
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <script type="text/javascript" 
    src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="http://site.local/session_timeout.js"></script>
    <script type="text/javascript" 
    src="http://site.local/validateFunction.js"></script>
    
    <script type="text/javascript">
        let collectedData = {'workplaces': []};
        let curStep = -1;
        let firstStepForm = '<?php echo $applicationForm->render()?>';
        let workplaceForm = '<?php echo $workPlaceForm->render()?>';
        
        function loadWorkplaces() {
            $.getJSON('api/workplaces_data.php', function(data){
                if (data !== null){
                    collectedData.workplaces = data;
                }
            });
        }
        
        function getCookie(c_name) {
            if (document.cookie.length > 0) {
                c_start = document.cookie.indexOf(c_name + "=");
                if (c_start != -1) {
                    c_start = c_start + c_name.length + 1;
                    c_end = document.cookie.indexOf(";", c_start);
                    if (c_end == -1) {
                        c_end = document.cookie.length;
                    }
                    return decodeURIComponent(document.cookie.substring(c_start, c_end));
                }
                return null;
            }
            return null;
        }
        
        function loadFirstForm() {
            let draft = getCookie('draft');
            if (draft !== null){
                collectedData = JSON.parse(draft);
            }
            else {
                loadWorkplaces();
            }
            $('#form_container').html(firstStepForm);
            $('.d-flex.form-row').prepend($('<div class="m-2">').append(
                    $('<button id="back" class="btn btn-danger">').append("Назад <")
                    ));
            $('#subheader').html('Шаг 1. Выберите необходимый пункт');
            $('#back').click(function(e) {
                if (confirm('Вернутся в профиль? Введенные данные будут сохранены еще 10 минут.')){
                    obj = $("#applicationForm").serializeArray().reduce(
                        function(m,v){m[v.name] = v.value;return m;}, {});
                    collectedData['reason_and_limitations'] = obj;
                    document.cookie = "draft=" + 
                            encodeURIComponent(JSON.stringify(collectedData)) +
                            "; max-age=600; samesite=strict;";
                    window.location.replace('../');
                }
                e.preventDefault();
            });
            $( "#applicationForm" ).on("submit", function(e) {
                obj = $(this).serializeArray().reduce(
                    function(m,v){m[v.name] = v.value;return m;}, {});
                collectedData['reason_and_limitations'] = obj;
                curStep++;
                loadWorkplaceForm();
                e.preventDefault();
            });
            $.getJSON('api/reasons_data.php', '', function(data){
                if (data !== null){
                   reasons = $('#reason');
                   for (let index in data) {
                        object = data[index];
                        reasons.append(
                                $('<option value="' + object + '">').append(object)
                        );
                    }
                    if('reason_and_limitations' in collectedData){
                        let reas_lim = collectedData['reason_and_limitations'];
                        $('#reason option[value="' + reas_lim.reason + '"]').prop('selected', true);
                        for (let index in reas_lim) {
                            if (index !== 'reason'){
                                $('#'+index).prop('checked', true);
                            }
                        }
                    }
                }
                
            });
        }
        
        function saveFormData(){
            obj = $('#workPlaceForm').serializeArray().reduce(
                        function(m,v){m[v.name] = v.value;return m;}, {});
            collectedData.workplaces[curStep] = obj;       
            $('#workPlaceForm')[0].reset();
        }
        
        function loadFormData(){
            data = collectedData.workplaces[curStep];
            for (let key in data) {
                $("#" + key).each(function(){
                    if ($(this).prop('tagName') === "INPUT"){
                        $(this).val(data[key]);
                    }
                    else {
                        $(this).html(data[key]);
                    }
                });

            }
            $('#dangerAlertBlock').hide();
        }
        
        function isFormEmpty(){
            return $('#name').val() === '' && $('#address').val() === ''
                && $('#employment_date').val() === ''
                && $('#unemployment_date').val() === '';
        }
        
        function loadWorkplaceForm(){
            $('#form_container').html(workplaceForm);
            $('.d-flex.form-row').prepend(
                $('<div class="m-2">').append(
                    $('<button id="prevWorkplace" class="btn btn-primary">')
                        .append("Назад <")
                ),
                $('<div class="m-2">').append(
                    $('<button id="nextWorkplace" class="btn btn-primary">')
                        .append("Далее >")
                )
            );
    
            loadFormData();
    
            $('#prevWorkplace').click(function(e) {
                if (isFormEmpty() || validate()){
                    if (validate()){
                        saveFormData();
                    }
                    curStep--;
                    if (curStep === -1){
                        loadFirstForm();
                    }
                    else {
                        loadFormData();
                    }
                }
                
                e.preventDefault();
            });

            $('#nextWorkplace').click(function(e) {
                if (validate()){
                    saveFormData();
                    curStep++;
                    loadFormData();
                }
                e.preventDefault();
            });
            
            $('#subheader').html('Шаг 2. Укажите места работы, учебы или службы за последние 5 лет');
            
            $("#submit").click(function(e) {
                if (isFormEmpty()) {

                    $.ajax({
                        type: "POST",
                        url: "api/applications_data.php",
                        data: collectedData,
                        success: function () {
                            window.location.replace('../');
                        }
                    });
                    e.preventDefault();
                }
                else if (validate()){
                    saveFormData();
                    $.ajax({
                        type: "POST",
                        url: "api/applications_data.php",
                        data: collectedData,
                        success: function () {
                            window.location.replace('../');
                        }
                    });
                    e.preventDefault();
                }
            });
            
        }
        
    $(document).ready(function() {

        loadFirstForm();

    });
    </script>
  </head>
  <body>
    <div class="container">
      <div class="row mt-3">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h2>Подать заявление</h2>
              <h4 id="subheader">Шаг 1. Выберите необходимый пункт</h4>
              <div id="form_container">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
