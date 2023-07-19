
function validerSaisieAuth(){
    var json_string = validerSaisieForm("formulaire"); 
    // var string_mail  = $('#usermail').val();
    // var string_pwd  = $('#userpassword').val();
    if(json_string !== false && json_string !==undefined){
        $("#img_loading").show();
        $.post("/teamplanning/ajax/cerberos.php", {
            json_datas: json_string},
            function(data){
                $("#img_loading").hide();
                var str_feedback = jQuery.parseJSON(data);
                if (str_feedback.is_ok == true) {
                    $("#div_redirect").show();
                    //window.location.replace("modules/planning/planning.php");
                }
                
                
                
                $('#message').html(str_feedback.message); 
                alert(data);
                //$("#message").slideUp(2000).delay( 2000 ).fadeOut( 1000 ); 
            }       
        );
    }
}
