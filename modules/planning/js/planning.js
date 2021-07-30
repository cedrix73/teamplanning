/* ----------------------------------------------------------------------------
   ----------------------------------------------------------------------------
   DESCRIPTION :                                                         
 * Bibliothèque javascript pour le formulaire principal de team planning  
 * 
   ----------------------------------------------------------------------------
 * @author : Cédric Von Felten
 * @since  : 21/07/2015
 * @version : 1.0
   --------------------------------------------------------------------------*/



function cacherComposantsInfo() {
    $('#div_saisie_activite').hide();
    $("#message").hide();
    $("#img_loading").hide();
}


/**
 * initialiserFormulaire
 * Initie le datePicker et son comportement (affichage, sélection d'une date)
 * @returns néant
 */   
function initialiserFormulaire(){
    
    // initialisation calendriers
        this.datecal = "";
        $("#div_date").datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            firstDay: 1, 
            dateFormat: 'dd/mm/yy',
            altField: "#datepicker",
            closeText: 'Fermer',
            prevText: 'Précédent',
            nextText: 'Suivant',
            currentText: 'Aujourd\'hui',
            monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
            dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
            dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
            weekHeader: 'Sem.',
            onSelect: function (dateText, inst) {
                initialiserFormulaire.datecal = dateText;
                refreshCalendar(dateText);
            }
        });
        // Encodage UTF8 en ISO8591-1 
        //$('#lst_periodes').html(utf8_decode($('#lst_periodes').html()))
        
    
    initialiserFormulaire.datecal = $.datepicker.formatDate("dd/mm/yy", new Date());
    cacherComposantsInfo();
    // site
    str_site = $('#cbo_sites').val();
    // departement
    str_departement = $('#cbo_departements').val();
    // service
    //liste_services_load(str_site, str_departement); 
    refreshCalendar(initialiserFormulaire.datecal);
    
}


function refreshCalendar(dateText=null){
    if(dateText === null){
        dateText = initialiserFormulaire.datecal;
    }
    str_date = dateText;
    
    // Sites
    str_site = $('#cbo_sites').val();
    if(str_site == "Tous *" || str_site == " "){
        str_site="";
    }
    // Departements
    str_departement = $('#cbo_departements').val();
    if(str_departement == "Tous *" || str_departement == " "){
        str_departement="";
    }
    // Services
    str_services = $('#cbo_services').val();
    if(str_services == "Tous *" || str_services == " "){
        str_services="";
    }
    
    // largeur cadre min
    var l_planning = 1100; 
    var nb_col_sup = 0;


    l_fenetre = $(window).width(); 
    var largeur_col = 293;
    var largeur_cadre= convertPxToInt($("#cadre").css("width"));

    var largeur_colonne_droite= convertPxToInt($("#planning").css("width"));
    
    nb_col_sup = (l_fenetre - largeur_colonne_droite) < 50 ? 0 : parseInt((l_fenetre - 1100) / largeur_col);
    // redimensionnement horizontal: à implémenter 
   // nb_col_sup = parseInt((l_fenetre - 1100) / largeur_col);
    $("#img_loading").show();
    // requête ajax
    $.post("ajax/afficherCal.php", 
        {date_sel: str_date, 
         site_sel: str_site,
         departement_sel: str_departement, 
         service_sel: str_services,
         col_sup: nb_col_sup}, 
         function(data){
            $("#img_loading").hide();
            if(data.length >0) {
                $('#planning').html(data);
               


                // redimensionnement horizontal 
                // On utilise des valeurs précalculées à partoir de lireDimensions()
                
                var l_defilement = parseInt(232 + (parseInt(nb_col_sup + 2) * largeur_col)  + 32);//1100
                var l_cadre = 250 + l_defilement;
                var delta = parseInt(l_fenetre -l_cadre);
                nb_col_sup = parseInt(delta / largeur_col);
                if(l_cadre>=1100){
                    $('#defilement').css("width" , l_defilement + "px");
                    $('.col_droite').css("width" , parseInt(l_defilement) + "px");
                    $('#cadre').css("width" , l_cadre + "px");
                }
            }
            // recentrage du cadre principal
            $('#cadre').css('margin-left', parseInt(delta/2));
    });
  
}



function setDateWidget(dateRetournee){
    $('#div_date').datepicker({dateFormat: "dd/mm/yy"}).
        datepicker("setDate", dateRetournee);
}


function noWeekendsOrHolidays(date) {
	var noWeekend = jQuery.datepicker.noWeekends(date);
	return noWeekend;
}
  


/**
 * afficherSaisie
 * @param {type} date
 * @param {type} ressource_id
 * @param {type} numActivite (=0 pour tout jour travaillé) 
 * @param {type} numPeriode (=0 pour tout jour travaillé)
 * @returns {undefined}
 */
function afficherSaisie(date, ressource_id, numActivite = null, numPeriode) {
    var today = new Date();
    $("#div_saisie_activite").html(initialiserFormulaire.saisieActivite);
    $('#btn_valider_saisie').attr('onclick', 'verifierEvent()');
    // patch 1.1.1 met à jour la liste des activités
    $.post("ajax/getActivites.php", 
        {
            id_activite:  numActivite,
            datatype: 'html'
        })  
        .done(function(data){
                if(data.length >0) {
                $( "#lst_activites" ).empty().append(data);
                //$('#lst_activites').html(data);
                $('#lst_activites_modif').empty().append(data);
                }
            });

        
    $( "#supprimer" ).hide();
    $("#message").hide();
    $("#div_saisie_activite").slideDown();
    $("#txt_str_date_debut").val(date);
    $("#txt_str_date_fin").val(date);
    $("#lst_periodes").val(numPeriode);
    infoRessource.id = ressource_id;
    
    
    if(numActivite > 0){
        // Modification ou supression
        $('#lst_activites').refresh();
        
        infoRessource.action = "modification";
        $("#btn_valider_saisie").val("Modifier");
        //$('#btn_valider_saisie').attr('onclick', 'verifierEvent()');
        $("#fld_saisie_activite").prop("disabled", true);
        $( "#supprimer" ).show();
        $("#fld_modification_activite").show();
        
        $('#lst_periodes option').clone().appendTo('#lst_periodes_modif');
        $("#txt_str_date_debut_modif").val($("#txt_str_date_debut").val());
        $("#txt_str_date_fin_modif").val($("#txt_str_date_fin").val());
        $("#lst_periodes_modif").val(numPeriode);
        

    } else {
        // Insertion
        infoRessource.action = "insertion";
        $("#fld_modification_activite").hide();
        $("#btn_valider_saisie").val("Valider");
    }
    
    if(dateNativePourComparaison(today) > validDatePourComparaison(date)) {
        $("#fld_saisie_activite").prop("disabled", true);
        $("#fld_modification_activite").prop("disabled", true);
        $( "#supprimer" ).hide();

    }
   
   // jour choisi = jour min dans datePicker
   var datePat = /^(\d{1,2})(\/)(\d{1,2})(\/)(\d{4})$/;
   var matchArray = date.match(datePat);
   jour = matchArray[1];
   mois = parseInt(matchArray[3]-1);
   annee = parseInt(matchArray[5]);

   
   $(".champ_date_fin").datepicker({
       minDate: date,
       dateFormat: 'dd/mm/yy',
       firstDay: 1,
       monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
       dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
       constrainInput: true,
	   beforeShowDay: noWeekendsOrHolidays});

    $("#txt_str_date_debut_modif").datepicker({
        minDate: new Date(),
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
        constrainInput: true,
        beforeShowDay: noWeekendsOrHolidays});
}


function supprimerSaisie() {
    infoRessource.action = "suppression";
    modifierSaisie();
}


function verifierEvent() {
    var message = null;
    var fonction_js = null;
    var date_debut = null;
    var date_fin = null;
    var no_need = false;
    if(infoRessource.action == 'insertion') {
        fonction_js = 'validerSaisie()';
        date_debut = $("#txt_str_date_debut").val();
        date_fin = $("#txt_str_date_fin").val();
        //  Pas besoin confirmation si date debut = fin
        if(date_debut === date_fin) {
            no_need = true;
        }
    } else {
        fonction_js = 'modifierSaisie()';
        date_debut = $("#txt_str_date_debut_modif").val();
        date_fin = $("#txt_str_date_fin_modif").val();
        // si on ne modifie un autre attribt que la date
        if(date_debut === $("#txt_str_date_debut").val() && date_debut === date_fin) {
            no_need = true;
        }
        
    }
    //alert(fonction_js + 'no nedd: ' + no_need + 'action:' + infoRessource.action);
    
    if((validDatePourComparaison(date_debut) > validDatePourComparaison(date_fin))){
        message = "La date de début doit être égale ou antérieure à la date de fin.";
        afficherMessage(message);
    }else{
        if(no_need) {
            eval(fonction_js);
        } else {
            $("#img_loading").show();
            $.post("ajax/verifierEvent.php",{
                action_user: infoRessource.action,
                date_debut: ""+date_debut+"", 
                date_fin: ""+$("#txt_str_date_fin").val()+"", 
                ressource_id: ""+infoRessource.id+"", 
                activite_sel: ""+$("#lst_activites").val()+"", 
                periode_sel: ""+$("#lst_periodes").val()+""}, 
                function(data){
                    $("#img_loading").hide();
                    $( "#supprimer" ).html("&nbsp;");   
                    if(data !== null && data.length >0) {
                        $("#message").append(data);
                    } else {
                        eval(fonction_js);
                    }
                }       
            );
        }
    }
}


function validerSaisie() {
    message = '';
    var date_debut = $("#txt_str_date_debut").val();
    var date_fin = $("#txt_str_date_fin").val();
    if((validDatePourComparaison(date_debut) > validDatePourComparaison(date_fin))){
        message = "La date de début doit être égale ou antérieure à la date de fin.";
        afficherMessage(message);
    }else{
        $("#img_loading").show();
        $.post("ajax/insererEvent.php", {
            action_user: infoRessource.action,
            date_debut: ""+date_debut+"", 
            date_fin: ""+$("#txt_str_date_fin").val()+"", 
            ressource_id: ""+infoRessource.id+"", 
            activite_sel: ""+$("#lst_activites").val()+"", 
            periode_sel: ""+$("#lst_periodes").val()+""}, 
            function(data){
                $("#img_loading").hide();
                $( "#supprimer" ).html("&nbsp;");
                if(data.length >0) {
                    $("#div_saisie_activite").slideUp(2000).delay( 2000 ).fadeOut( 1000 );
                    refreshCalendar(initialiserFormulaire.datecal);
                    afficherMessage(data);
                    
                }
            }       
        );
    }
}



function modifierSaisie() {
    var fichierAjax = "modifierEvent.php";
    if(infoRessource.action == "suppression"){
        fichierAjax = "supprimerEvent.php";
    }
    message = '';
    var date_debut = $("#txt_str_date_debut_modif").val();
    var date_fin = $("#txt_str_date_fin_modif").val();
    if((validDatePourComparaison(date_debut) > validDatePourComparaison(date_fin))){
        message = "La date de début doit être égale ou antérieure à la date de fin.";
        afficherMessage(message);
    }else{
        $("#img_loading").show();
        $.post("ajax/" + fichierAjax, {
            action_user: infoRessource.action, 
            old_date_debut: ""+$("#txt_str_date_debut").val()+"", 
            old_date_fin: ""+$("#txt_str_date_fin").val()+"", 
            date_debut: ""+date_debut+"", 
            date_fin: ""+date_fin+"", 
            ressource_id: ""+infoRessource.id+"", 
            activite_sel: ""+$("#lst_activites_modif").val()+"", 
            periode_sel: ""+$("#lst_periodes_modif").val()+""}, 
            function(data){
                $("#img_loading").hide();
                $( "#supprimer" ).html("&nbsp;");
                if(data.length >0) {
                    $("#div_saisie_activite").slideUp(2000).delay( 2000 ).fadeOut( 1000 );
                    refreshCalendar(initialiserFormulaire.datecal);
                    afficherMessage(data);
                    
                }
            }       
        );
    }
}



function liste_activites_load(){
    $.ajax({
        type: "POST",
        url: "ajax/listeEventLoad.php",
        datatype: "json",
        success: function(data)
        {
            if(ctle_erreur(data)){
                var tab_elems = [];
                var str_feedback = jQuery.parseJSON(data);
                $.each(str_feedback, function(cle, valeur) {
                        tab_elems.push('<option value="' + valeur + '">' + valeur + '</option>');
                });
                $("#cbo_activites").html(tab_elems.join(''));
            }
        }
    });
}

function attribuerDateFinModif(valeur_date) {
    $("#txt_str_date_fin_modif").val(valeur_date);
    $("#txt_str_date_fin_modif" ).datepicker( "option", "minDate", valeur_date);

}


function lireDimensions(){
    var largeur_menu= convertPxToInt($("#menu_gauche").css("width"));
    var largeur_menu_pad_gauche = convertPxToInt($("#menu_gauche").css("padding-left"));
    var largeur_menu_pad_droite = convertPxToInt($("#menu_gauche").css("padding-right"));
    var largeur_menu_tot = largeur_menu + largeur_menu_pad_gauche + largeur_menu_pad_droite;

    var largeur_col_droite_min= convertPxToInt($(".col_droite").css("width"));
    var largeur_col_droite_pad_g= convertPxToInt($(".col_droite").css("padding-left"));
    var largeur_col_droite_pad_d= convertPxToInt($(".col_droite").css("padding-right"));
    var largeur_col_droite_tot = largeur_col_droite_min + largeur_col_droite_pad_g + largeur_col_droite_pad_d;

    var largeur_cadre_margin_g = convertPxToInt($("#cadre").css("margin-left"));
    var largeur_cadre_margin_d = convertPxToInt($("#cadre").css("margin-right"));
    var largeur_cadre_margin_tot = largeur_cadre_margin_g + largeur_cadre_margin_d;

    var espace_libre = largeur_menu_tot + largeur_col_droite_tot + largeur_cadre_margin_tot;
    var largeur_legende = convertPxToInt($(".legende_ressources").css("width"));
    var largeur_legende_pad = convertPxToInt($(".legende_ressources").css("padding-left"));
    var largeur_legende_ress = largeur_legende + largeur_legende_pad;//232
    //alert(espace_cadre + largeur_menu_tot);//250

    var largeur_col = convertPxToInt($(".entete_semaine").css("width")) + 32 ;//293
    var l_defilement = parseInt(largeur_legende_ress  + 32);//1100
    var l_cadre = espace_cadre + largeur_menu_tot + largeur_col_droite_margin + largeur_legende_ress + l_defilement;
    
    nb_col_sup = parseInt((l_fenetre -l_cadre) / largeur_col);

    
    if(l_cadre>=1100){
        $('#defilement').css("width" , l_defilement + "px");
        $('.col_droite').css("width" , parseInt(l_defilement) + "px");
        $('#cadre').css("width" , l_cadre + "px");
    }
    

    return nb_col_sup;
    
}



