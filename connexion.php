<?php 
require_once('inc/init.inc.php');
// echo '<pre>'; print_r($_GET); echo '</pre>';

// Si l'indice 'acton est définit dans l'URL et qu'il a pour valeur d'éconnexion,
// cela veut dire que l'internaute à cliquer sur le lien 'deconnexion' et donc transmise
// dans l'URL les parmetres 'action=deconnexion', alors oon entre dans la c=ci=ondition IF et on 
//suprime 'indice 'user' dans la session afin qu'il soit plus authentifié sur le site
if(connect())
{
    header('location: profil.php');
}



if(isset($_GET['action']) && $_GET['action'] == 'deconnexion')
{
    // echo "Je veux me deconnecter <hr>";
    unset($_SESSION['user']);
}

echo'<pre>' ; print_r($_POST); echo '</pre>';
if(isset($_POST['pseudo_email'], $_POST['password'], $_POST['submit']))
{
    $verifUser = $bdd->prepare("SELECT * FROM membre WHERE pseudo = :pseudo OR email = :email");
    $verifUser->bindvalue(':pseudo', $_POST['pseudo_email'], PDO::PARAM_STR);
    $verifUser->bindvalue(':email', $_POST['pseudo_email'], PDO::PARAM_STR);
    $verifUser->execute();

    // echo "nd résultat : ".$verifUser->rowCount() . '<hr>';


    // rowCount()retourn un résultat de 1, cela veut dire que pseudo ou l'email saisi dans
    // le formulaire existe dans la BDD, la requette SELECT retourne 1 resultat
   if($verifUser->rowCount() > 0)
   {
    //    echo "pseudo ou email ok ! <hr>";

    // On execute fetch sur le resulat de la requete SELECT afin de récuperer les données en BDD sous forme de tableau ARRAY de 
    // l'internaute aui a saisi le bon pseudo /email dans le formulaire
    $user = $verifUser->fetch(PDO::FETCH_ASSOC);
    // echo '<pre>'; print_r($user); echo '</pre>';

    // Contrôle du mot de passe
    // password_verify() : Fonction prédéfinie permettant de comparer une clé de hachage (le mot de passe crypté en BDD) 
    // à une chaine de caractre (le mot de passe saisi dans le formulaire)
    if(password_verify($_POST['password'], $user['password']))
    {
        echo "mot de passe ok!";

        // On crée un tableau multidimensionnel dans la session ic on crée un indic 
        // 'user' dans la session qui a pour valeur un tableau ARRAY contenant 
        // toutes les données de l'internaute autentifié sur la site
        foreach($user as $key => $value)
        {   
            // nom
            if($key != 'password')
            {
            // Contrôle du mot de pass
            $_SESSION['user'][$key] = $value;
            }
            // $_SESSION['user']['nom'] = Lacroix
      
        }
        // echo '<pre>'; print_r($user); echo '</pre>';

        header('location: profil.php');
    }
       
    else
    {
        $error = "<p class='col-6 bg-danger text-white text-center mx-auto p-3'>Identifiants invalides.</p>";
    }


   }
   
   else // Sinon, le pseudo ou email saisi n'est pas connu en BDD la requete 
   // SELECT ne retourne aucun resultat
   {
       $error = "<p class='col-6 bg-danger text-white text-center mx-auto p-3'>Identifiants invalides.</p>";
   }

} 

require_once('inc/inc_front/header.inc.php');
require_once('inc/inc_front/nav.inc.php');
?>  
    <!-- On affiche le message de validation d'inscription stocké dans le fichier de session de l'utilisateur -->
    <?php 
    if(isset($_SESSION['valid_inscription']))  echo $_SESSION['valid_inscription'];
    if(isset($error)) echo $error;
    
    ?>

    <h1 class="text-center my-5">Identifiez-vous</h1>

    <form action="" method="post" class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 mx-auto">
        <div class="mb-3">
            <label for="pseudo_email" class="form-label">Nom d'utilisateur / Email</label>
            <input type="text" class="form-control" id="pseudo_email" name="pseudo_email" placeholder="Saisir votre Email ou votre nom d'utilisateur">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Saisir votre mot de passe">
        </div>
        <div>
            <p class="text-end mb-0"><a href="" class="alert-link text-dark">Pas encore de compte ? Cliquez ici</a></p>
            <p class="text-end m-0 p-0"><a href="" class="alert-link text-dark">Mot de passe oublié ?</a></p>
        </div>
        <input type="submit" name="submit" value="Continuer" class="btn btn-dark">
    </form>

<?php 
// On suprime dans la session l'indice 'valid_inscription afin d'éviter que le message ne s'affiche tout le temps sur la page connexion
unset($_SESSION['valid_inscription']);
require_once('inc/inc_front/footer.inc.php');        