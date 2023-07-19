/* ----------------------------------------------------------------------------
   ----------------------------------------------------------------------------
   DESCRIPTION :                                                         
 * Bibliothèque javascript au format jQuery de fonctions générales  
 * (formulaires, traitement des dates, encodage, REGEXs)
   ----------------------------------------------------------------------------
 * @author : Cédric Von Felten
 * @since  : 28/10/2014
 * @version : 1.3
   --------------------------------------------------------------------------*/


 
 /**
  * Hack de la fonction .text() de jQuery:
  * Elle est réutilisée pour afficher de l'html et
  * decode du texte depuis l'utf-8
  * @param {type} $
  * @param {type} oldHtmlMethod
  * @returns {undefined}
  */
  (function( $, oldHtmlMethod ){

    // Override the core html method in the jQuery object.
        $.fn.text = function(){
        // Check to see if we have an argument (that the user
        // is setting HTML content).
        //
        // NOTE: This does not take into account the fact
        // that we can now pass a function to this method -
        // this is just a lightweight demo.
        if (arguments.length){
        // Prepend our own custom HTML.
        //arguments[ 0 ] = utf8_decode(arguments[ 0 ]);
        }
    // Execute the original HTML method using the
    // augmented arguments collection.
       return(oldHtmlMethod.apply( this, arguments ));
       };

       $.fn.extend({
        refresh: function() { 
            var $parent = this.end();
            var selector = this.selector.substring($parent.selector.length).trim();
            return $parent.find(selector); 
        }

        
    });
   })( jQuery, jQuery.fn.html );           



   
   function utf8_decode(chaine){
       //return decodeURIComponent(escape(chaine));
   }
   
   function utf8_encode(chaine) {
     return unescape(encodeURIComponent(chaine));
   }
   
   /*
    * validDatePourComparaison
    * @param {type} strDate
    * @returns {String}
    * DESCRIPTION :
      Convertit la date <strDate> (qui est au format jj/mm/aaaa) au
      format international défini par l'ISO 8601:1988, c'est à dire
      au format "aaaa-mm-jj".
      L'avantage de ce format est qu'il peut être utilisé pour
      la comparaison de dates
    */
   function validDatePourComparaison(strDate) {
   
       var datePat = /^(\d{1,2})(\/)(\d{1,2})(\/)(\d{4})$/;
       var matchArray = strDate.match(datePat); // is the format ok?
    
       // parse date into variables
       var day = matchArray[1];
       var month = matchArray[3]; 
       var year = matchArray[5];
       // On ajoute des zéro (éventuellement) devant le jour et le mois
       if (day.length == 1) {
          day = "0" + day;
       }
       if (month.length == 1) {
          month = "0" + month;
       }
       return(year + "-" + month + "-" + day);
       
    }

    /**
     * @description Convertit une date native javascript en chaîne yyyy-mm-dd
     * @param {Date} date_param 
     * @returns {String} 
     */
    function dateNativePourComparaison (date_param) {
        var yyyy = date_param.getFullYear().toString();
        var mm = (date_param.getMonth()+1).toString(); // getMonth() is zero-based
        var dd  = date_param.getDate().toString();
        return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
      };
   
    function convertSqltoDate(date_sql) {
       var datePat = /^(\d{4})(-)(\d{1,2})(-)(\d{1,2})$/;
       var matchArray = date_sql.match(datePat);
       var jour = matchArray[5];
       var mois = (matchArray[3]);
       var annee = (matchArray[1]);
       return jour + "/" + mois + "/" + annee;
    }
   
    function convertPxToInt(chaine){
       return parseInt(chaine.replace("px", ""));
   }
   
   
   function afficherMessage(txt_message, temps=3000){
       $("#message").html(txt_message);
       //$("#message").fadeIn(800).delay( temps );
       //$("#message").fadeOut(400);
   }
   
   
   /**
    * @description Les retours jSon tolèrent difficilement les 
    * caractères d'espacement. On les remplace alors avec "---".
    * Au retour, cette fonction les reconvertit en caractères d'espacement. 
    * @param {*} chaine 
    * @returns {*} chaine 
    */
   function replaceBlancs(chaine){
       var reg=new RegExp("(---)", "g");
       if(reg.test(chaine)){
           chaine = chaine.replace(reg, " ");
       }
       return chaine;
   }
   
   function onlyNumberKey(evt) { 
             
       // call: onkeypress="return onlyNumberKey(event)"
       var ASCIICode = (evt.which) ? evt.which : evt.keyCode 
       if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) 
           return false; 
       return true; 
   } 
   
   function envoieFeedbackFormulaire(feedback_field, bln) {
       
       //alert(feedback_field.attr("name"));
       if(bln == false) {
           feedback_field.attr("class", "form_icon ui-icon ui-icon-circle-close");
       }else{
           feedback_field.attr("class", "form_icon ui-icon ui-icon-check");
       }
   }
   
   function regTest(regvar, field_name) {
       var field_verif;
       var fieldObj = $("input[name=" + field_name + "]");
       var feedback_field = $(fieldObj).next($("span[name=" + field_name + "_img]"));
       var retour = true;
       if(field_name !== null) {
           field_verif = fieldObj.val().replace(/ /g,'');
           fieldObj.val(field_verif);
           retour = regvar.test(fieldObj.val());
           envoieFeedbackFormulaire(feedback_field, retour);
       }
       return retour;
   }
   
   function verifStringAlpha(string_field_name) {
       var reg_string = new RegExp(/^[a-zA-Z-\s\-éèàüöñøå' ]*$/);
       return regTest(reg_string, string_field_name);
   }
   
   function verifStringAlphaNum(string_field_name) {
       var reg_string = new RegExp(/^[a-zA-Z0-9-\séèàüöñøå' ]*$/);
       return regTest(reg_string, string_field_name);
   }
   
   function verifDate(date_field_name) {
       var reg_date = new RegExp(/^(\d{4})(\-)(\d{1,2})(\-)(\d{1,2})$/);
       return regTest(reg_date, date_field_name);
   }
   
   function verifEmail(email_field_name) {
       var reg_email = new RegExp(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/);
       return regTest(reg_email, email_field_name);
   }
   
   function verifPhone(phone_field_name) {
       var reg_phone = new RegExp(/^[0-9.-]{9,}$/);
       return regTest(reg_phone, phone_field_name);
   }
   
   
   
   
   
   function validerSaisieForm(container_name){
       // verification champs formulaire front
       var formulaire = $("#" + container_name);
       var fields_tab = [];
       var unfilled_required_tab = [];
       var bln_ok = true;
       var label ='';
       var unfilled_required_string ='Les champs requis suivants n\'ont pas été remplis:<ul>';
       var uncorrect_fields = '';
       var message = '';
   
       var ressourceLabel = '';
       
   
       $(formulaire).find('input, select, textarea')
           .each(function() {
               
                var ressourceObject = new Object();
                ressourceObject.nom = $(this).attr('name');
                ressourceObject.valeur = $(this).val();
                ressourceObject.required = $(this).attr('required');
                ressourceLabel = $(this).prev("label").html();
                if(typeof  ressourceObject.label === 'undefined') {
                  ressourceLabel = $(this).attr('name');
                }
                ressourceObject.label = ressourceLabel;
                ressourceObject.type = $(this).prop('type');
               
               
               // verification des types generaux 
               // mail
               if(typeof(ressourceObject.valeur) !== 'undefined' && ressourceObject.valeur !== '' && ressourceObject.nom !== 'undefined'){
                   // Verification de qq champs spéciaux par libelle
                   if(ressourceObject.nom.includes("_nom") || ressourceObject.nom.includes("_prenom")) {
                       if(verifStringAlpha($(this).attr('name')) == false) {
                           uncorrect_fields += "<li>Le champ <i>" + ressourceLabel + "</i> est incorrect. il doit contenir uniquement " 
                                               + "des lettres, des espaces ou des trémas.</li>";
                           bln_ok = false;
                       }
                   }else{
                       switch(ressourceObject.type){
   
                           case 'email':
                               if(verifEmail($(this).attr('name')) == false) {
                                   uncorrect_fields += "<li>Le champ <i>" + ressourceLabel + "</i> est incorrect: il doit être de la forme xxx@xx.xxx</li>";
                                   bln_ok = false;
                               }
                           break;
                       
                           case 'tel': 
                               // telephone
                               if(verifPhone($(this).attr('name')) == false) {
                                   uncorrect_fields += "<li>Le champ <i>" + ressourceLabel + "</i> est incorrect. il doit être supérieur à 9 chiffres, " 
                                                       + " peut inclure des chiffres, des points ou des trémas.</li>";
                                   bln_ok = false;
                               }
                           break;
                         
                           case 'text': 
                           // alphanumerique
                               if(verifStringAlphaNum($(this).attr('name')) == false) {
                                   uncorrect_fields += "<li>Le champ <i>" + ressourceLabel + "</i> est incorrect. il doit contenir uniquement " 
                                                       + "des chiffres, des lettres ou des trémas.</li>";
                                   bln_ok = false;
                               }
                           break;
   
   
                           case 'date': 
                           // date
                               if(verifDate($(this).attr('name')) == false) {
                                   uncorrect_fields += "<li>Le champ <i>" + ressourceLabel + "</i> est incorrect. il doit contenir uniquement " 
                                                       + "des chiffres, des lettres ou des trémas.</li>";
                                   bln_ok = false;
                               }
                           break;
     
                       }
                   }
                   
               }

   
               // verification si champs obligatoires remplis
               if($(this).attr('required') && ($(this).val()===null || $(this).val()==='')){
                    bln_ok = false;
                    $(this).css({"border": "1px solid red"});
                    unfilled_required_tab.push(ressourceLabel);
                   
               }else {
                    $(this).css({"border": "1px solid #999"});
               }
               
               if(bln_ok && $(this).val()!=='' && $(this).prop('type')!='button'){
                   fields_tab.push(ressourceObject);
               }
               
           });
   
       if(!bln_ok){ 
           $.each(unfilled_required_tab, function(key, value) {
               unfilled_required_string += '<li>' + value + '</li>';
           });
           unfilled_required_string += '</ul>';
   
           if(uncorrect_fields !='') {
               uncorrect_fields = '<br>Champs incorrects:<br> ' + uncorrect_fields + '</ul>';
           }
           message = unfilled_required_string + uncorrect_fields;
           
   
           afficherMessage(message);
           return false;
       }else{
           var json_string = JSON.stringify(fields_tab); 
           return json_string;
       }
   }
   
   
   