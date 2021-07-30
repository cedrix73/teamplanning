/* ----------------------------------------------------------------------------
   ----------------------------------------------------------------------------
   DESCRIPTION :                                                         
 * Bibliothèque javascript pour les evenements 
 * 
   ----------------------------------------------------------------------------
 * @author : Cédric Von Felten
 * @since  : 28/10/2014
 * @version : 1.3
   --------------------------------------------------------------------------*/




function afficherTypesEvents(){
    if( $("#affichage_activite").val() == 'type_events'){
        $("#div_saisie_activite").toggle();
        $("#affichage_activite").val("");
    } else {
        var contenuActivite = $("#div_saisie_activite").html();
        $.post("ajax/listeTypesEventLoad.php", 
             function(data){
                if(data.length >0) {
                    $('#div_saisie_activite').html(data);
                    $("#div_saisie_activite").slideDown();
                }
        });
        $("#affichage_activite").val("type_events");
    }
}

function modifierTypeEvent(activite_id){
    var activite_couleur  = $('#' + activite_id +'_couleur').val();
    var activite_abbrev  = $('#' + activite_id +'_affichage').val();
    $.post("ajax/modifierTypeEvent.php", {
        activite_id: activite_id, 
        activite_couleur: ""+activite_couleur+"", 
        activite_abbrev: ""+activite_abbrev+""}, 
        function(data){
            if(data.length >0) {
                refreshCalendar(initialiserFormulaire.datecal);
                afficherMessage(data);
            }
        }
    );
}

function insererTypeEvent(){
    var activite_libelle  = $('#new_libelle').val();
    var activite_couleur  = $('#new_color').val();
    var activite_abbrev  = $('#new_abbrev').val();
    $("#img_loading").show();
    $.post("ajax/insererTypeEvent.php", {
        activite_libelle: ""+activite_libelle+"", 
        activite_couleur: ""+activite_couleur+"", 
        activite_abbrev: ""+activite_abbrev+""}, 
        function(data){
            $("#img_loading").hide();
            if(data.length >0) {
                afficherTypesEvents();
                afficherMessage(data);
            }
        }       
    );
}





