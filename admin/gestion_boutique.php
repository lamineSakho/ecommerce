<?php
require_once('../inc/init.inc.php');

echo '<pre style="margin-left: 250px">', print_r($_POST); echo '</pre>';
echo '<pre style="margin-left: 250px">', print_r($_FILES); echo '</pre>'; 

if(isset($_POST['reference'], $_POST['categorie'], $_POST['titre'], $_POST['description'], $_POST['couleur'], $_POST['taille'],
$_POST['public'], $_POST['prix'], $_POST['stock']))
{
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

    // ENREGISTREMENT PRODUIT
    // Exo : réalisé le traitement PHP + SQL permettant d'insérer  un produit à la validation du formulaire 
    // (prepare + bindValue + execute) 

    $insertProduit = $bdd->prepare("INSERT INTO produit(reference, categorie, titre, description, couleur, taille, photo, prix, stock)
    VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :photo, :prix, :stock)");
    
    $insertProduit->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
    $insertProduit->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
    $insertProduit->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
    $insertProduit->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
    $insertProduit->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
    $insertProduit->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
    $insertProduit->bindValue(':photo', $photoBdd, PDO::PARAM_STR);
    $insertProduit->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
    $insertProduit->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);

    $insertProduit->execute();

    $validInsert = "<p class='col-7 bg-success text-white text-center mx-auto p-3 mt-3'>
    le produit reférence<strong>$_POST[reference]</strong> a été enregistré avec succes.</p>";

}
require_once('../inc/inc_back/header.inc.php');
require_once('../inc/inc_back/nav.inc.php');
?>

    <!-- 
        Exo ; afficher sous forme de tableau de HTML l'enselble des produits stockés en BDD
        1. requete de selection (querry)
        2. Afficher le nombre de produit selectionnés en BDD (rowCouynt)
        3. récupérer les informations sous forme de tableau (fetchAll)
        4. Déclarer le tableau HTML (<table>)
        5. Afficher les entêtes du tableau (<th>) en passant par le résultat du fetchAll()
        6. Afficher tout les produits de la BDD à l'aide de boucle (foreach) dans des lignes (<tr>) et cellules (<td>) du tableau 
        7. Prévoir un lien de modification / suppression pour chaque produit dans le tableau HTML
    -->
    <?php       
        // echo '<pre>'; print_r($bdd); echo '</pre>';

        $bddStatement = $bdd->query("SELECT * FROM produit");

        // rowCount() : méthode (fonction) issue de la classe pdoStatement qui retourne le nombre de résultats issue de la requete de selection
        echo "Nombre d'employés : <span class='badge bg-success'>" . $bddStatement->rowCount() . "</span><hr>";
        echo '<pre>'; var_dump($bddStatement); echo '</pre>';

        
        $pdoStatement = $bdd->query("SELECT * FROM produit");
        $produit = $pdoStatement->fetch(PDO::FETCH_ASSOC); 

        echo '<div class="col-3 mx-auto bg-success text-white text-center p-3">'; 

        foreach($produit as $key => $value)
        {
            echo "$key: $value<br>";
        }
        echo '</div>';
        echo "<h2 class='text-center my-5'>04. PDO: QUERY - WHILE + SELECT + FETCH_ASSOC (plusieurs résultats)</h2>";
    ?>

    <h1 class="text-center my-5">Ajout produit</h1>

    <?php if(isset($validInsert)) echo $validInsert; ?>

    <!-- enctype="multiparrtform_data": permet de récuperer les données d'un fichier uploadé 
       (nom, extension, taille etc....) accessible en PHP vuia la superglobale $_files  -->
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label for="reference" class="form-label">Référence</label>
            <input type="text" class="form-control" id="reference" name="reference">
        </div>
        <div class="col-md-6">
            <label for="categorie" class="form-label">Catégorie</label>
            <input type="text" class="form-control" id="categorie" name="categorie">
        </div>
        <div class="col-12">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre">
        </div>
        <div class="col-10">
            <label for="description" class="form-label">Description</label>
            <textarea type="text" class="form-control" id="description" name="description" rows="10"></textarea>
        </div>
        <div class="col-4">
            <label for="couleur" class="form-label">Couleur</label>
            <input type="color" class="form-control input-couleur" id="couleur" name="couleur">
        </div>
        <div class="col-4">
            <label for="taille" class="form-label">Taille</label>
            <select id="taille" name="taille" class="form-select">
                <option value="s">S</option>
                <option value="m">M</option>
                <option value="l">L</option>
                <option value="xl">XL</option>
            </select>
        </div>
        <div class="col-4">
            <label for="public" class="form-label">Public</label>
            <select id="public" name="public" class="form-select">
                <option value="homme">homme</option>
                <option value="femme">Femme</option>
                <option value="mixte">Mixte</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" class="form-control" id="photo" name="photo">
        </div>
        <div class="col-4">
            <label for="prix" class="form-label">Prix</label>
            <input type="text" class="form-control" id="prix" name="prix">
        </div>
        <div class="col-4">
            <label for="stock" class="form-label">Stock</label>
            <input type="text" class="form-control" id="stock" name="stock">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-dark mb-5">Ajout produit</button>
        </div>
</form>


<?php           
require_once('../inc/inc_back/nav.inc.php');       