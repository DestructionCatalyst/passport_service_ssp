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
    let passwordFields = $("input[type='password']");
    

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
                if ((getattr($(this), 'required'))){
                    let re = getattr($(this), 'regexp');
                    if (re){
                        let regexp = new RegExp(re);
                        if (!(regexp.test(value))){
                            alertText += $(this).attr('hint') + '</br>'; 
                            validationPass = false;
                        }
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
        
        password = null;
        passwordFields.each(function(){
            insertedPassword = $(this).val();
            
            if(password === null){
                password = insertedPassword;
                
                if (insertedPassword.length < 8){
                alertText += "Пароль должен содержать минимум 8 символов.</br>";
                validationPass = false;   
                }
                if (!insertedPassword.match(/[0-9]/)){
                    alertText += "Пароль должен содержать хотя бы 1 цифру</br>";
                    validationPass = false;   
                }
                if (!insertedPassword.match(/[A-ZА-ЯЁ]/)){
                    alertText += "Пароль должен содержать хотя бы одну заглавную букву</br>";
                    validationPass = false;   
                }
                if (!insertedPassword.match(/[a-zа-яё]/)){
                    alertText += "Пароль должен содержать хотя бы одну строчную букву</br>";
                    validationPass = false;   
                }
                if (!insertedPassword.match(/[^A-ZА-ЯЁa-zа-яё0-9]/)){
                    alertText += "Пароль должен содержать хотя бы один специальный символ</br>";
                    validationPass = false;   
                }
                
            }
            else if(password !== insertedPassword){
                alertText += "Введенные пароли не совпадают</br>";
                validationPass = false;
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
