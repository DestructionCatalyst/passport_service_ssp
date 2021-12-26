$(document).ready(function() {
    let formFields = $('#regForm :input')
            .not('#noRegistration')
            .not('#submit');


    $("#noRegistration").click(function() {
        if ($('#noRegistration').is(":checked"))
        {
            formFields.each(function(){
                $(this).attr("disabled", "disabled");
            });
        }
        else {
            formFields.each(function(){
                $(this).removeAttr("disabled");
            });
        }
    });
    $('#regForm').submit(function() {
        if (!$('#noRegistration').is(':checked')) {
            let fieldsFilled = true;
            let validationPass = true;
            let alertText = '';
            formFields.each(function(){
                fieldsFilled = fieldsFilled && ($(this).val() !== '');
                if ($(this).attr('type') === 'text'){
                    if(!(/^[^A-Za-z]+$/.test($(this).val()))){
                        alertText += 
                                'Укажите ' + $(this).attr('verbose_name')
                                + ' на русском языке</br>';
                        validationPass = false;
                    }    
                }
                else if ($(this).attr('type') === 'date'){
                    let parsedDate = Date.parse($(this).val());
                    if (!isNaN(parsedDate)){
                        restriction = $(this).attr('restriction');
                        
                        if (typeof restriction === 'undefined' 
                                && restriction === false)
                            return;
                        
                        if(restriction === 'past' 
                                && parsedDate < new Date())
                            return;
                        
                        if(restriction === 'future' 
                                && parsedDate > new Date())
                            return;
                    }
                    alertText += 'Укажите коррректную '
                                    + $(this).attr('verbose_name') + '</br>';
                            validationPass = false;
                }
            });
            if(!fieldsFilled){
                alertText = 'Заполните все поля!';    
            }
            if (!(fieldsFilled && validationPass)){
                $("#alertBlock").html(alertText);
                $("#alertBlock").show();
            }
            return fieldsFilled && validationPass;
        }
    });
});
