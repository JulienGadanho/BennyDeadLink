<?php

include dirname(__FILE__) .'/../class/bdd.php';
include dirname(__FILE__) .'/../class/check.php';

header('location: ../index.php?verif');

$connexion = new Bdd();
$donnees = $bdd->query("SELECT * FROM actions")->fetchAll();

$Check = new Check();

foreach($donnees as $row)
{
    $Check->SetId($row['id']);
    $Check->SetCible($row['cible']);
    $Check->SetUrl($row['url']);
    $Check->UpdateStatut();
}

$connexion->Off();