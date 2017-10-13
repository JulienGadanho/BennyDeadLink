<?php

session_start();

include 'class/bdd.php';
include 'class/design.php';
include 'class/check.php';

$Design = new Design();

if(isset($_SESSION['logged']))
{
    if(isset($_POST['url']) && isset($_POST['cible']))
    {
        $url = $_POST['url'];
        $cible = $_POST['cible'];
        
        if(filter_var($cible,FILTER_VALIDATE_URL) && filter_var($url,FILTER_VALIDATE_URL))
        {
            $connexion = new Bdd();
            $req = $bdd->prepare("INSERT INTO actions SET url = :url , cible = :cible");
            $req->execute(array(
                'url' => $url,
                'cible' => $cible
            ));
            $connexion->Off();
            
            $msg = $Design->Message('<strong>Action ajoutée avec succès</strong>','success');
        }
        else
        {
            $msg = $Design->Message('<strong>URL(s) invalide(s)</strong>','danger');
        }
    }
    elseif(isset($_GET['check']) && is_numeric($_GET['check']))
    {
        $connexion = new Bdd();
        $data = $bdd->query("SELECT * FROM actions WHERE id='$_GET[check]'")->fetch();
        
        if(isset($data['cible']) && isset($data['url']))
        {
            $Check = new Check();
            $Check->SetId($data['id']);
            $Check->SetCible($data['cible']);
            $Check->SetUrl($data['url']);
            $Check->UpdateStatut();
            
            $msg = $Design->Message('<strong>Action actualisée avec succès</strong>','success');
        }
        else
        {
            $msg = $Design->Message('<strong>L\'action n\'existe pas</strong>','danger');
        }
        
        $connexion->Off();
    }
    elseif(isset($_GET['delete']) && is_numeric($_GET['delete']))
    {
        $connexion = new Bdd();
        $req = $bdd->query("DELETE FROM actions WHERE id='$_GET[delete]'");
        $connexion->Off();
        
        if($req->rowCount() > 0)
        {
            $msg = $Design->Message('<strong>Action supprimée avec succès</strong>','success');
        }
        else
        {
            $msg = $Design->Message('<strong>L\'action n\'existe pas</strong>','danger');
        }
        
    }
    elseif(isset($_GET['verif']))
    {
        $msg = $Design->Message('<strong>La vérification a commencé</strong>','success');
    }
}
else
{
    if(isset($_POST['password']))
    {
        if($_POST['password'] == PANEL_PASSWORD)
        {
            $_SESSION['logged'] = 1;
            $msg = $Design->Message('<strong>Connecté avec succès</strong>','success');
        }
        else
        {
            $msg = $Design->Message('<strong>Mot de passe eronné</strong>','danger');
        }
    }
}



$Design->Header();

if(isset($_SESSION['logged']))
{
?>

<div class="jumbotron">
  <div class="container">
     <form action="" method="POST" role="form">
        <div class="form-group col-md-4">
          <label for="site">Votre site:</label>
          <input type="url" name="url" class="form-control" id="site">
        </div>
        <div class="form-group col-md-4">
          <label for="cible">Cible à checker:</label>
          <input type="url" name="cible" class="form-control" id="cible">
        </div>
        <div class="form-group col-md-4">
            <br>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
      </form>
  </div>
</div>

<div class="text-center">
    <a href="cron/refresh.php"><span class="btn btn-primary">Vérifier tout les liens</span></a><br><br>
</div>

<div class="container">
    <?php
     echo isset($msg) ? $msg : "";
    ?>
   <div class="row">
         
        <div class="col-md-12">
           
               <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th>Votre site</th>
                        <th>Cible à checker</th>
                        <th>Date d'ajout</th>
                        <th>Date du dernier crawl</th>
                        <th>Action</th>
                      </tr>
                        <?php
                        
                          $connexion = new Bdd();
                          $donnees = $bdd->query('SELECT * FROM actions ORDER BY state ASC')->fetchAll();
                          $connexion->Off();
                          
                          foreach($donnees as $row)
                          {
                            $statut_id = $row['state'] != -1 ? $row['state'] : 2;
                            $statut = array('danger','success','info');
                          ?>
                            <tr class="<?php echo $statut[$statut_id]; ?>">
                              <td>
                                  <a href="<?php echo $row['url']; ?>" target="_blank">
                                      <?php echo $row['url']; ?>
                                  </a>
                              </td>
                              <td>
                                  <a href="<?php echo $row['cible']; ?>" target="_blank">
                                      <?php echo $row['cible']; ?>
                                  </a>
                              </td>
                              <td>
                                  <?php
                                      $date = new DateTime($row['date']);
                                      echo $date->format('d-m-Y');
                                  ?>
                              </td>
                              <td>
                                  <?php echo $row['last_crawl']; ?>
                              </td>
                              <td>
                                <a href="?check=<?php echo $row['id']; ?>" title="Verifier le lien"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></a>
                                &nbsp;
                                <a href="?delete=<?php echo $row['id']; ?>" title="Supprimer l'action"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                              </td>
                            </tr>
                          <?php
                          }
                        ?>
                    </table>
            </div> 
        </div>
   </div>
  <hr>
</div>
<?php
}
else
{
?>
<br>
<div class="container">
    <?php echo isset($msg) ? $msg : ""; ?>
</div>
<div class="jumbotron">
  <div class="container">
     <form action="" method="POST">
        <div class="form-group col-md-8">
          <label for="site">Mot de passe:</label>
          <input type="password" name="password" class="form-control" id="site">
        </div>
        <div class="form-group col-md-4">
            <br>
            <button type="submit" class="btn btn-primary">Connexion</button>
        </div>
      </form>
  </div>
</div>

<?php
}

$Design->Footer();

?>