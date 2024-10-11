<?php
// URL de base de votre base de données Firebase
$firebase_url = 'https://cartevtest-default-rtdb.asia-southeast1.firebasedatabase.app/';

// Récupérer l'ID de la carte depuis l'URL
$cardId = $_GET['cardId'];

// Construire l'URL pour accéder aux données de la carte et de l'entreprise
$urlCarte = $firebase_url . 'cartes/' . $cardId . '.json';
$urlEntreprise = $firebase_url . 'entreprise.json';

// Récupérer les données de la carte depuis Firebase
$dataCarte = file_get_contents($urlCarte);
if ($dataCarte === FALSE) {
    die('Erreur : impossible de récupérer les données de la carte.');
}
$dataCarte = json_decode($dataCarte, true);

// Récupérer les données de l'entreprise depuis Firebase
$dataEntreprise = file_get_contents($urlEntreprise);
if ($dataEntreprise === FALSE) {
    die('Erreur : impossible de récupérer les données de l\'entreprise.');
}
$dataEntreprise = json_decode($dataEntreprise, true);

// Vérifier que la carte existe
if (empty($dataCarte)) {
    die('Erreur : carte non trouvée.');
}

// Générer le contenu du fichier .vcf
$vCardData = "BEGIN:VCARD\n";
$vCardData .= "VERSION:3.0\n";
$vCardData .= "FN:{$dataCarte['prenom']} {$dataCarte['nom']}\n";
$vCardData .= "EMAIL:{$dataCarte['email']}\n";
$vCardData .= "TEL:{$dataCarte['telephone']}\n";

// Ajout du profil LinkedIn personnel s'il existe
if (!empty($dataCarte['linkedin'])) {
    $vCardData .= "URL:{$dataCarte['linkedin']}\n";
}

// Ajout des informations de l'entreprise s'il existe
if (!empty($dataEntreprise)) {
    if (!empty($dataEntreprise['nom_entreprise'])) {
        $vCardData .= "ORG:{$dataEntreprise['nom_entreprise']}\n";
    }
    if (!empty($dataEntreprise['site_entreprise'])) {
        $vCardData .= "URL:{$dataEntreprise['site_entreprise']}\n";
    }
    if (!empty($dataEntreprise['facebook_entreprise'])) {
        $vCardData .= "URL:{$dataEntreprise['facebook_entreprise']}\n";
    }
    if (!empty($dataEntreprise['linkedin_entreprise'])) {
        $vCardData .= "URL:{$dataEntreprise['linkedin_entreprise']}\n";
    }
}

// Fin du fichier vCard
$vCardData .= "END:VCARD";

// Envoyer le fichier en tant que téléchargement
header('Content-Type: text/vcard');
header("Content-Disposition: attachment; filename={$dataCarte['prenom']}_{$dataCarte['nom']}.vcf");
echo $vCardData;
?>
