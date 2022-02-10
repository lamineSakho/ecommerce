
<?php
require_once('../inc/init.inc.php');
/* Si l'internaute, son statut n'est pas 'admin' dans la session donc dans la BDD, il n'a rien 
à faire sur cette page , on le redirige vers la page connexion*/

if(!adminConnect())

{       // http://localhost/php-wf3-1098/09-ecommerce/connexion.php
    header('location: ' . URL . 'connexion.php');
}

// echo '<pre style="margin-left: 250px">', print_r($_POST); echo '</pre>';
// echo '<pre style="margin-left: 250px">', print_r($_FILES); echo '</pre>'; 

// SUPPRESSION PRODUIT
if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
    // echo "<p style='margin-left: 450px;'>Je veux supprimer ce produit</p>";
    //Exo : réaliser le traitement PHP + SQL permettant de supprimer le produit dans la BDD en fonction de l'id_produit dans l'URL
    //prepare + bindvalue + execute) 

    $nbDelete = $bdd->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
    $nbDelete->bindvalue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
    $nbDelete->execute();

    // On redéfinit la valeur de l'indice 'action 

    $_GET['action'] = 'affichage';

    $msg = "<p class='col-5 bg-success text-white text-center mx-auto p-3 mt-3'>L'article n°<strong> $_GET[id_produit]</strong> a été supprimé avec succés.</p>";
}


if(isset($_POST['reference'], $_POST['categorie'], $_POST['titre'], $_POST['description'], $_POST['couleur'], $_POST['taille'],
$_POST['public'], $_POST['prix'], $_POST['stock']))
{

    $photoBdd = "";

    if(isset($_GET['action']) && $_GET['action'] == 'modification')
    {
        $photoBdd = $_POST['photo-actuelle'];
    }

 // TRAITEMENT / ENREGISTREMENT DE LA PHO PRODUIT
    if(!empty($_FILES['photo']['name']))
    {
        // On nomme l'image avant l'enregistrer, on concatene la référence saisie dans 
        //le formulaire avec le nom de l'image recupérée dans $_FILES 
        $nomPhoto = $_POST['reference'] . "-" . $_FILES['photo']['name'];
        // echo "<p style='margin-left: 250px'>$nomPhoto</p><hr>";

        // URL DE L'IMAGE (enrégistrée en BDD)
        // ex: http://localhost/PHP-wf3-1098/09-ecommerce/asset/uploads/15A89-tee-shirt6.jpg
        $photoBdd = URL . "assets/uploads/$nomPhoto";
        // echo "<p style='margin-left: 250px'>$photoBdd</p><hr>";
        
        // CHEMIN PHYSIQUE DE L'IMAGE SUR LE SERVEUR
        // EX C:/wamp64/www/PHP-wf3-1098/09-ecommerce/assets/uploads/15A89-tee-shirt8.jpg
        $photoDossier = RACINE_SITE . "assets/uploads/$nomPhoto";
        // echo "<p style='margin-left: 250px'>$photoDossier</p><hr>";

        // COPIE DE L IMAGE DANS LE DOSSIER UPLOADS
        // copy() : fonction predéfinit permettant de copier un fichier uploadé dans un dossie sur le serveur
        // 1. Le fichierntemporaire de l'image disponible dans le $_FILES
        // 2. Le chemin physique de l'image où elle doit etre enregistrée sur le serveur
        copy($_FILES['photo']['tmp_name'], $photoDossier);        
    }

    if(isset($_GET['action']) && $_GET['action'] == 'ajout')
    {
        // ENREGISTREMENT PRODUIT
        $insertProduit = $bdd->prepare("INSERT INTO produit(reference, categorie, titre, description, couleur, taille, photo, public, prix, stock)
    VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :photo, :public, :prix, :stock)");

    $_GET['action'] = 'affichage';

        $validInsert = "<p class='col-7 bg-success text-white text-center mx-auto p-3 mt-3'>
        le produit reférence<strong>$_POST[reference]</strong> a été enregistré avec succes.</p>";

    }
    elseif(isset($_GET['action']) && $_GET['action'] == 'modification')
    {
        // MODIFICATION PRODUIT
        $insertProduit = $bdd->prepare("UPDATE produit SET reference = :reference, categorie = :categorie, titre = :titre, description = :description,
        couleur = :couleur, taille = :taille, photo = :photo, public = :public, prix = :prix, stock = :stock WHERE id_produit = :id_produit");

        $insertProduit->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);

        $_GET['action'] = 'affichage';

        $validInsert = "<p class='col-7 bg-success text-white text-center mx-auto p-3 mt-3'>
        le produit reférence<strong>$_POST[reference]</strong> a été enregistré avec succes.</p>";
        

    
    }
    
    
    
    $insertProduit->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
    $insertProduit->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
    $insertProduit->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
    $insertProduit->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
    $insertProduit->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
    $insertProduit->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
    $insertProduit->bindValue(':photo', $photoBdd, PDO::PARAM_STR);
    $insertProduit->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
    $insertProduit->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
    $insertProduit->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);

    $insertProduit->execute();

    

    
}

// MODIFICATION ARTICLE
if(isset($_GET['action']) && $_GET['action'] == 'modification')
{
    // echo "<p style='margin-left: 250px, > je veucx modifier ce produit</p>";
    $update = $bdd->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $update-> bindvalue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
    $update->execute();

    $produitActuel = $update->fetch(PDO::FETCH_ASSOC);
    echo '<pre style="margin-left: 300px">'; print_r($produitActuel); echo '</pre>';

    // On stock chaque valeur de l'aticle dans des variables distincte afin de les injecter dans les attributs 'value' du formulaire HTML
    $reference = (isset($produitActuel['reference'])) ? $produitActuel['reference'] : '' ;
    $categorie = (isset($produitActuel['categorie'])) ? $produitActuel['categorie'] : '' ;
    $titre = (isset($produitActuel['titre'])) ? $produitActuel['titre'] : '' ;
    $description = (isset($produitActuel['description'])) ? $produitActuel['description'] : '' ;
    $couleur = (isset($produitActuel['couleur'])) ? $produitActuel['couleur'] : '' ;
    $taille = (isset($produitActuel['taille'])) ? $produitActuel['taille'] : '' ;
    $public = (isset($produitActuel['public'])) ? $produitActuel['public'] : '' ;
    $photo = (isset($produitActuel['photo'])) ? $produitActuel['photo'] : '' ;
    $prix = (isset($produitActuel['prix'])) ? $produitActuel['prix'] : '' ;
    $stock = (isset($produitActuel['stock'])) ? $produitActuel['stock'] : '' ;
  
    // echo  "<pre style='margin-left: 300px'> $reference</pre>";

}
 

require_once('../inc/inc_back/header.inc.php');
require_once('../inc/inc_back/nav.inc.php');
?>

    <!--- LIENS PRODUITS  -->

    <div class="mt-3 text-center">
        <a href="?action=ajout" class="btn btn-secondary">Nouvel article</a>
        <a href="?action=affichage" class="btn btn-secondary">Affichage des articles</a>

    </div>
    <!--Si l'indice 'action' est définit dans l'URL et qu'il a pour valeur 'affichage', cela veut dire que 
    l'internaute a cliqué sur le lien 'affichage des articles' et par conséquent transmis dans l'URL 'actionaffichage', 
    alors on entre dans la condition IF et on execute le code d'affichage des articles.
    -->
    <?php  if(isset($_GET['action']) && $_GET['action'] == 'affichage'): ?>     

     <!-- AFFICHAGE DES PRODUITS  -->

    <h1 class="text-center my-5">Affichages des produits</h1>
    

<?php 
        // Affichage message utilisateur
        if(isset($msg)) echo $msg;

        //afficher sous forme de tableau de HTML l'enselble des produits stockés en BDD
        $bddStatement = $bdd->query("SELECT * FROM produit");

        // echo '<pre>'; print_r($bdd); echo '</pre>';

        // rowCount() : méthode (fonction) issue de la classe pdoStatement qui retourne le nombre de résultats issue de la requete de selection
        echo "Nombre de produit: <span class='badge bg-success'>" . $bddStatement->rowCount() . "</span><hr>";

        // echo '<pre>'; var_dump($bddStatement); echo '</pre>';

        //3. récupérer les informations sous forme de tableau (fetchAll)
        $produit = $bddStatement->fetchAll(PDO::FETCH_ASSOC); 
        //  echo '<pre>'; print_r($produit); echo '</pre>';

        echo '<table class="table table-bordered"><tr>';
        
        //       ARRAY
        foreach($produit[0] as $key => $value)
        {
            echo "<th class='text-center'>" . ucfirst($key) . "<th>";
        }           
            echo "<th class='text-center'>Actions</th>";
        echo '<tr>';
        foreach($produit as $key => $tab)
        {
            echo '<tr>' ;
            //             [photo] http://localhost/.../15A89-tee-shirt.jpg
            foreach($tab as $key2 => $value)
            {
                if($key2 == 'photo')
                echo "<td><img src='$value' alt='$tab[titre]' class='img-products'></td>"; 
                elseif($key2 == 'couleur')
                echo "<td style='background-color: $value;' class='text-white'>$value</td>"; 
                elseif($key2 == 'description')
                echo "<td>$value</td>"; 
                else
                echo "<td class='text-center'>$value<td>";
            }

            echo "<a href='?action=modification&id_produit=$tab[id_produit]' class='btn btn-primary mb-3'><i class='bi bi-pencil-square'></i></a>";
            
            echo "<a href='?action=suppression&id_produit=$tab[id_produit]' class='btn btn-dark' onclick='return(confirm(\"En êtes vous certains ?\"));
            '><i class='bi bi-trash'></i></a>";
            echo '</tr>';
        }
        echo'</table>';

        // -Si l'indice 'action' est définit dans l'URL et qu'il a pour valeur 'ajout', cela veut dire que 
        // l'internaute a cliqué sur le lien 'ajout des articles' et par conséquent transmis dans l'URL 'actonaajout', 
        // alors on entre dans la condition IF et on execute le code d'ajout des articles.
         
    endif;
    if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')):
?>       

    <h1 class="text-center my-5"><?= ucfirst($_GET['action'])?> produit</h1>

    <?php if(isset($validInsert)) echo $validInsert; ?>

    <!-- enctype="multiparrtform_data": permet de récuperer les données d'un fichier uploadé 
       (nom, extension, taille etc....) accessible en PHP vuia la superglobale $_files  -->
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label for="reference" class="form-label">Référence</label>
            <input type="text" class="form-control" id="reference" name="reference"
            value= "<?php if(isset($reference)) echo $reference; ?>">
        </div>
        <div class="col-md-6">
            <label for="categorie" class="form-label">Catégorie</label>
            <input type="text" class="form-control" id="categorie" name="categorie"value= "<?php if(isset($categorie)) echo $categorie; ?>" >
        </div>
        <div class="col-12">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre"value= "<?php if(isset($titre)) echo $titre; ?>">
        </div>
        <div class="col-10">
            <label for="description" class="form-label">Description</label>
            <textarea type="text" class="form-control" id="description" name="description" rows="10"><?php if(isset($description)) echo $description; ?></textarea>
        </div>
        <div class="col-4">
            <label for="couleur" class="form-label">Couleur</label>
            <input type="color" class="form-control input-couleur" id="couleur" name="couleur"value= "<?php if(isset($couleur)) echo $couleur; ?>" >
        </div>
        <div class="col-4">
            <label for="taille" class="form-label">Taille</label>
            <select id="taille" name="taille" class="form-select">
                <option value="s" <?php if(isset($taille) && $taille == 's') echo 'selected'; ?>>S</option>
                <option value="m" <?php if(isset($taille) && $taille == 'm') echo 'selected'; ?>>M</option>
                <option value="l" <?php if(isset($taille) && $taille == 'l') echo 'selected'; ?>>L</option>
                <option value="xl" <?php if(isset($taille) && $taille == 'xl') echo 'selected';?>>XL</option>
            </select>
        </div>
        <div class="col-4">
            <label for="public" class="form-label">Public</label>
            <select id="public" name="public" class="form-select">
                <option value="homme" <?php if(isset($public) && $public == 'h') echo 'selected'; ?>>homme</option>
                <option value="femme" <?php if(isset($public) && $public == 'f') echo 'selected'; ?>>Femme</option>
                <option value="mixte" <?php if(isset($public) && $public == 'm') echo 'selected'; ?>>Mixte</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" class="form-control" id="photo" name="photo">
            
            <input type="hidden" id="photo_actuelle" name="photo-actuelle" value="<?php if(isset($photo)) echo $photo; ?>">
        </div>
        <div class="col-4">
            <label for="prix" class="form-label">Prix</label>
            <input type="text" class="form-control" id="prix" name="prix"value= <?php if(isset($prix)) echo $prix; ?>>
        </div>
        <div class="col-4">
            <label for="stock" class="form-label">Stock</label>
            <input type="text" class="form-control" id="stock" name="stock"value= "<?php if(isset($stock)) echo $stock; ?>">
        </div>

        <?php if(isset($photo) && !empty($photo)): ?>

            <div class="d-flex flex-column align-items-center"> 
                <small class="fst-italic">photo actuelle de l'article. Vous pouvez uploader une nouvelle photo si vous souhaitez la modifier.</small>

                <img src="<?= $photo ?>" alt="" class="img-product-update">
            </div>

        <?php endif; ?>

        <div class="col-12">
            <button type="submit" class="btn btn-dark mb-5"><?= ucfirst($_GET['action'])?> article</button>
        </div>
</form>


<?php 
endif;          
require_once('../inc/inc_back/nav.inc.php');       