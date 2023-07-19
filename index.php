<?php
 require_once 'config.php';

?>
<!DOCTYPE html>
<html>
    <head>
            <title>Bienvenue</title>
            <meta http-equiv="Content-Type" content="text/HTML" charset="utf-8" />
            <meta http-equiv="Content-Language" content="fr" />
            <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0" />
            <META NAME="Author" CONTENT="Cédric Von Felten">
            <link type="text/css" rel="stylesheet" href="styles/principal.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
            <script src="https://kit.fontawesome.com/908dc143e7.js" crossorigin="anonymous"></script>
            <script src="js/jquery-1.8.3.min.js"></script>
            <script src="js/general.js"></script> 
            <script src="js/auth.js"></script>
            

    </head>
    <body>
    
        <section class="hero is-primary is-fullheight">
            <div class="container">
                <h1 class="title is-1">Bienvenue sur notre site de gestion de planning !  </h1>
            </div>
            <div class="hero-body">
                <div class="container" id="auth">
                    <div id="div_redirect" class="box">
                        <a href="modules/planning/planning.php">Aller sur mon application de gestion de planning</a>
                    </div>


                    <div class="columns is-centered">
                        <div class="column is-5-tablet is-4-desktop is-3-widescreen">
                            <div id="formulaire" class="box">
                                <div class="field">
                                    <label for="usermail" class="label">Votre email</label>
                                    <div class="control has-icons-left">
                                        <input type="email" placeholder="e.g. bobsmith@gmail.com" class="input" id="usermail" name="usermail" maxlength="25" onchange="verifEmail($(this).attr('name'));" required>
                                        <span class="icon is-small is-left">
                                        <i class="fa fa-envelope"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="field">
                                    <label for="userpassword" class="label">Votre mot de passe</label>
                                        <div class="control has-icons-left">
                                            <input type="password" placeholder="*******" class="input" id="userpassword" name="userpassword" required>
                                            <span class="icon is-small is-left">
                                            <i class="fa fa-lock"></i>
                                            </span>
                                        </div>
                                    </div>
                                <div class="field">
                                    <label for="remember me" class="checkbox">
                                        <input type="checkbox" name ="chkbox_rememberme">
                                            Remember me
                                    </label>
                                </div>
                                <div class="field">
                                    <button class="button is-success" id="login_button" onclick="validerSaisieAuth();">
                                        Login
                                    </button>
                                </div>
                                <img id="img_loading" src="<?php echo '/../styles/img/' . 'loader.gif';?>" alt="Loading" />
                            
                            </div>
                            <div id="message">
                                    &nbsp;
                            </div>
                            <script>$("#div_redirect").hide();</script>
                        </div>
                    </div>
                </div>
            </div>
</section>
    </body>
</html>
