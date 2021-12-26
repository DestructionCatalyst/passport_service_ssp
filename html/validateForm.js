function getattr(element, attrName){
    let attrValue = element.attr(attrName);
    if (typeof attrValue === undefined || attrValue === false)
    {
        return null;
    }
    else return attrValue;
}

$(document).ready(function() {
    let textFields = $("input[type='text']");
    let dateFields = $("input[type='date']");
    

    $('form').submit(function() {
        let validationPass = true;
        let alertText = '';
        
        textFields.each(function(){
            let value = $(this).val();
            if ((getattr($(this), 'required')) && (value === '')){
                alertText = 'Заполните все обязательные поля!</br>'; 
                validationPass = false;
            }
            else {
                let re = getattr($(this), 'regexp');
                if (re){
                    let regexp = new RegExp(re);
                    if (!(regexp.test(value))){
                        alertText += $(this).attr('hint') + '</br>'; 
                        validationPass = false;
                    }
                }
            }
        });
        
        dateFields.each(function(){
            let value = $(this).val();
            if ((getattr($(this), 'required')) && (value === '')){
                alertText = 'Заполните все обязательные поля!</br>'; 
                validationPass = false;
            }
            else {
                let parsedDate = Date.parse(value);
                if (isNaN(parsedDate)){
                    alertText += 'Введите '
                            + $(this).attr('verbose_name_acc').toLowerCase()
                            + ' в правильном формате!</br>'; 
                    validationPass = false;
                }
                else {
                    let before = getattr($(this), 'before');
                    if ((before) && (parsedDate > Date.parse(before))){
                        alertText += $(this).attr('hint') + '</br>'; 
                        validationPass = false;
                    }
                    let after = getattr($(this), 'after');
                    if ((after) && (parsedDate < Date.parse(after))){
                        alertText += $(this).attr('hint') + '</br>'; 
                        validationPass = false;
                    }
                }
            }
        });
        
        //alert(validationPass);
        if (!validationPass){
            $("#dangerAlertBlock").html(alertText);
            $("#dangerAlertBlock").show();
        }
        
          
        return validationPass;
        
    });
});
