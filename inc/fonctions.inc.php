<?php
// FONCTION INTERNAUTE AUTEHNTIFIE
function connect()
{
    // Si l'internaute 'user' n'est pas définit dans la session, cela veur dire que 
    //l'internaute n'est pas passé par la page connexion, donc n'est pas authentifié sur le site
    if(!isset($_SESSION['user']))
    {
        return false;
    }
    else // Sinon l'indice 'user' est définit dans lea session, l'internaute est passé par la page connexion et est
    // autentifié sur le site.
    {
        return true;
    }
}

// FONCTION INTERNAUTE AUTHENTIFIE ET ADMINISTRATEUR DU SITE
function adminConnect()
{   // l'indice 'user est définit dans la session (connect() ) s et si l'indice 'statut ' dans la session, donc la BDD
    // a pour valeur 'admin' n, cela veur dire que l'internaute est authetifieé et qu'il est administrateur du site
    
    if(connect()  && $_SESSION['user']['statut'] == 'admin')
    {
        return true;
    }     
    else // Sinon, l'indice 'user' n'est pas definit, donc l'utilisateur n'est pas authentifié
        // ou alors son statut est 'user', donc l'utilisateur n'est pas admintrateur du site
    {
        return false;
    }
        
}
       


 
       
    