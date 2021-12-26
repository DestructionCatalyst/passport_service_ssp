function loadDataFromApi(api_page){
    //console.log('start');
    $(document).ready(function() {
        $.getJSON('api/' + api_page, '', function(data){
            if (data !== null){
                for (let key in data) {
                    //console.log(key, data[key]);
                    $("#" + key).each(function(){
                        if ($(this).prop('tagName') === "INPUT"){
                            $(this).val(data[key]);
                        }
                        else {
                            $(this).html(data[key]);
                        }
                    });

                }
            }
        });
    });

}