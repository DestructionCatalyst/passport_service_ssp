var options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric'
    };
function dateFormat(str){
    return new Date(Date.parse(str))
            .toLocaleString(undefined, options);
}
function formatIfDate(key, object){
    if (key.includes('date')){
        return new Date(Date.parse(object))
            .toLocaleString(undefined, options);
    }
    return object;
}
function loadFromObject(data, objectName){
    for(key in data[objectName]){
        object = formatIfDate(key, data[objectName][key]);
        $('#'+key).html(object);
    }
}
function loadIfExists(data, objectName){
    if (objectName in data){
        $('#'+objectName).show();
        loadFromObject(data, objectName);
    }
    else{
        $('#'+objectName).hide();
    }
}

function loadApplication(success, commitButtonSetup){
    $(function(){
      $('#application').load('http://site.local/applicationTable.html', function() {
        $('#applicationControlButtons').load('applicationControlButtons.html', function() {
            
            $('.noteCell').each(function(){
                $(this).load('http://site.local/noteForm.html');
            });

            $.getJSON('../api/application_extended_data.php?id=' + getUrlParameter('id'),
            '', function(data){

                if (data !== null){
                    // Load personal data
                    loadFromObject(data, 'user');
                    loadFromObject(data, 'passport');
                    loadIfExists(data, 'permanent_registration');
                    loadIfExists(data, 'temporary_registration');
                    loadFromObject(data, 'application');
                    // Load workplaces
                    let workplaces = data['workplaces'];
                    for (index in workplaces){
                        workplace = workplaces[index];
                        $('#workplaceTable').append(
                            $('<tr>').append(
                                $('<td colspan="4" class="noteCell">')
                                    .load('http://site.local/noteForm.html')
                            ),
                            $('<tr>').append(
                                $('<th scope="row" colspan="2">').append('Название'),
                                $('<td colspan="2">').append(workplace['name'])
                            ),
                            $('<tr>').append(
                                $('<th scope="row" colspan="1">').append('Адрес'),
                                $('<td colspan="3">').append(workplace['address'])
                            ),
                            $('<tr id="dates' + index + '">').append(
                                $('<th scope="row">').append('Дата зачисления'),
                                $('<td>').append(workplace['employment_date']),
                            ),
                        );
                        if ('unemployment_date' in workplace){
                            $('#dates' + index).append(
                                $('<th scope="row">').append('Дата увольнения'),
                                $('<td>').append(workplace['unemployment_date'])
                            );
                        }
                    }
                    // Load comments
                    $.getJSON('../api/comment_data.php?id=' + 
                        getUrlParameter('id'),'', function(comment){
                            $('textarea').each(function(index){
                                var filtered = comment.filter(function(el) {
                                    return Number(el.stage) - 1 === index;
                                });
                                if (filtered.length > 0){
                                    $(this).val(filtered[0].description);
                                }
                            });
                            $('.noteForm').children('div').each(function(index){
                                var filtered = comment.filter(function(el) {
                                    return Number(el.stage) - 1 === index;
                                });
                                if (filtered.length > 0){
                                    $(this).children('textarea').val(filtered[0].description);
                                    $(this).find(".creation_date").html(
                                            dateFormat(filtered[0].creation_date)
                                    );
                                    if(filtered[0].last_change_date){
                                        $(this).find(".last_change_date")
                                                .html(
                                                dateFormat(filtered[0].last_change_date)
                                                );
                                    }
                                    if(commitButtonSetup){
                                        console.log(filtered[0]);
                                        commitButtonSetup(
                                                $(this).find(".btn-commit"), 
                                                filtered[0], 
                                                data.application);
                                    }
                                }
                            });
                            // Take extra action
                            if(success){    
                                success(data);
                            }
                        });
                    
                }

            });
        });
      });
    });
}