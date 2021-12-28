$(document).ready(function() {
    $.getJSON('../api/user_extended_data.php', '', function(data){
        if (data !== null){
            $('#full_name').html(data['full_name']);
            var has_any_registration = 
                    data['has_registration'] ||
                    data['has_temp_registration'];
            var has_passport = data['has_passport'];
            var has_open_applications = data['has_open_applications'];
            if(!has_any_registration){
                $('#registration_alert').show();
            }
            if(!has_passport) {
                $('#passport_alert').show();
            }
            //console.log(has_any_registration && has_passport && (!has_open_applications));
            if(has_any_registration && has_passport && (!has_open_applications)){
                $('#apply_button').removeAttr("disabled");;
            }
        }
    });
     $.getJSON('../applications/api/applications_data.php', '', function(data){
        if (data !== null){
            applicationsList = $('#applications_list');
            for (let index in data) {
                object = data[index];
                var options = {
                    year: 'numeric',
                    month: 'numeric',
                    day: 'numeric'
                  };

                let app_date = new Date(Date.parse(object.application_date))
                        .toLocaleString(undefined, options);
                let status = object.status.toLowerCase().replaceAll(' ', '_');
                applicationsList.prepend(
                    $('<a class="text-decoration-none" href="applications/show.php?id=' 
                        + object['id'] + '">').append(
                    $('<li class="list-group-item">').append(
                        $('<div class="d-flex w-100 justify-content-between">').append(
                            $('<h5 class="mb-1">').append('Заявление №' + object.number),
                            $('<small>').append('Дата подачи: ' + app_date)
                        ),
                        $('<p class="mb-1">').append('Статус: ', 
                                $('<span class="' + status + '">')
                                        .append(object.status))
                    )));
            }
        }
    });
    $('#apply_button').click(function (){
        if (!$(this).prop('disabled')){
            window.location.replace('applications/create.php');
        }
    });
});