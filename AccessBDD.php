<?php
include_once("ConnexionPDO.php");

/**
 * Classe de construction des requêtes SQL à envoyer à la BDD
 */
class AccessBDD {
	
    public $login="root";
    public $mdp="";
    public $bd="mediatek86";
    public $serveur="localhost";
    public $port="3306";	
    public $conn = null;

    /**
     * constructeur : demande de connexion à la BDD
     */
    public function __construct(){
        try{
            $this->conn = new ConnexionPDO($this->login, $this->mdp, $this->bd, $this->serveur, $this->port);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * récupération de toutes les lignes d'une table
     * @param string $table nom de la table
     * @return lignes de la requete
     */
    public function selectAll($table){
        if($this->conn != null){
            switch ($table) {
                case "livre" :
                    return $this->selectAllLivres();
                case "dvd" :
                    return $this->selectAllDvd();
                case "revue" :
                    return $this->selectAllRevues();
                case "exemplaire" :
                    return $this->selectAllExemplairesRevue();
                case "commandeLivre" :
                    return $this->selectAllcommandelivre();
                case "commande" :
                    return $this->selectAllcommande();
                case "commanderevue" :
                    return $this->selectAllcommanderevue();
                case "exemplairedate" :
                    return $this->selectdateachatexemplaire();
                case "commandedvd" :
                    return $this->selectallcommandedvd();
                case "utilisateur" :
                    return $this->selectservice();
                case "recherlivre" :
                    return $this->selectlivrecorrespondant();
                
                default:
                    // cas d'un select portant sur une table simple, avec tri sur le libellé
                    return $this->selectAllTableSimple($table);
            }			
        }else{
            return null;
        }
    }
     
    
    // Méthode selectservice
    public function selectservice($champ) {
        print_r($champs);
    //$requete = "SELECT s.service  FROM utilisateur u  join service s on u.idservice = s.id WHERE login ='mimi' and pwd='pwd'";
    
    $requete = "SELECT s.service  FROM utilisateur u  join service s on u.idservice = s.id WHERE";
        foreach ($champs as $key => $value){
                $requete .= ":$key,";
            }
        $requete = rtrim($requete, 'AND '); 
        echo"LA REQUETE  : ".$requete."</br>";
    return $this->conn->query($requete,$params );
}

   
     public function selectallcommandedvd(){
          
        $req="select c.dateCommande , c.montant , cd.nbExemplaire, cd.idLivreDvd , c.id,s.etape from commandedocument cd Join commande c on cd.id = c.id join dvd V on V.id=cd.idLivreDvd  ";
        $req .= "Join Suivi s on cd.idetape= s.id";
        return $this->conn->query($req);
    }
    public function selectdateachatexemplaire($id){
        $param = array(
                "numero" => $id
        );
        $req ="select dateAchat from exemplaire  where numero =:numero ";
        return $this->conn->query($req,$param);
    }
    
    public function selectAllcommanderevue(){
          
        $req="select c.id ,c.dateCommande , c.montant , a.dateFinAbonnement , a.idRevue  from   abonnement a join commande c on c.id= a.id ";
        return $this->conn->query($req);
    }
    public function selectAllcommandelivre(){
        $req="select c.dateCommande , c.montant , cd.nbExemplaire, cd.idLivreDvd , c.id,s.etape from commandedocument cd Join commande c on cd.id = c.id  ";
        $req .= "Join Suivi s on cd.idetape= s.id join livre l on cd.idLivreDvd= l.id";
        //$req.="join livre l on cd.idLivreDvd= l.id";
        return $this->conn->query($req);
                
    }
    
    public function selectAllcommande(){
        $req="select c.id , c.dateCommande, c.montant from commande c ";
        return $this->conn->query($req);
    }

    /**
     * récupération d'une ligne d'une table
     * @param string $table nom de la table
     * @param string $id id de la ligne à récupérer
     * @return ligne de la requete correspondant à l'id
     */	
    public function selectOne($table, $id){
        if($this->conn != null){
            switch($table){
                case "exemplaire" :
                    return $this->selectAllExemplairesRevue($id);
                default:
                    // cas d'un select portant sur une table simple			
                    $param = array(
                        "id" => $id
                    );
                    return $this->conn->query("select * from $table where id=:id;", $param);					
            }				
        }else{
                return null;
        }
    }

    /**
     * récupération de toutes les lignes de d'une table simple (sans jointure) avec tri sur le libellé
     * @param type $table
     * @return lignes de la requete
     */
    public function selectAllTableSimple($table){
        $req = "select * from $table order by libelle;";		
        return $this->conn->query($req);		
    }

    /**
     * récupération de toutes les lignes de la table Livre et les tables associées
     * @return lignes de la requete
     */
    public function selectAllLivres(){
        $req = "Select l.id, l.ISBN, l.auteur, d.titre, d.image, l.collection, ";
        $req .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $req .= "from livre l join document d on l.id=d.id ";
        $req .= "join genre g on g.id=d.idGenre ";
        $req .= "join public p on p.id=d.idPublic ";
        $req .= "join rayon r on r.id=d.idRayon ";
        $req .= "order by titre ";		
        return $this->conn->query($req);
    }	

    /**
     * récupération de toutes les lignes de la table DVD et les tables associées
     * @return lignes de la requete
     */
    public function selectAllDvd(){
        $req = "Select l.id, l.duree, l.realisateur, d.titre, d.image, l.synopsis, ";
        $req .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $req .= "from dvd l join document d on l.id=d.id ";
        $req .= "join genre g on g.id=d.idGenre ";
        $req .= "join public p on p.id=d.idPublic ";
        $req .= "join rayon r on r.id=d.idRayon ";
        $req .= "order by titre ";	
        return $this->conn->query($req);
    }	

    /**
     * récupération de toutes les lignes de la table Revue et les tables associées
     * @return lignes de la requete
     */
    public function selectAllRevues(){
        $req = "Select l.id, l.periodicite, d.titre, d.image, l.delaiMiseADispo, ";
        $req .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $req .= "from revue l join document d on l.id=d.id ";
        $req .= "join genre g on g.id=d.idGenre ";
        $req .= "join public p on p.id=d.idPublic ";
        $req .= "join rayon r on r.id=d.idRayon ";
        $req .= "order by titre ";
        return $this->conn->query($req);
    }	

    /**
     * récupération de tous les exemplaires d'une revue
     * @param string $id id de la revue
     * @return lignes de la requete
     */
    public function selectAllExemplairesRevue($id){
        $param = array(
                "id" => $id
        );
        $req = "Select e.id, e.numero, e.dateAchat, e.photo, e.idEtat ";
        $req .= "from exemplaire e join document d on e.id=d.id ";
        $req .= "where e.id = :id ";
        $req .= "order by e.dateAchat DESC";		
        return $this->conn->query($req, $param);
    }		

    /**
     * suppresion d'une ou plusieurs lignes dans une table
     * @param string $table nom de la table
     * @param array $champs nom et valeur de chaque champs
     * @return true si la suppression a fonctionné
     */	
    public function delete($table, $champs)
    {
        
        echo"le champ delete : ";
        print_r($champs);
      if ($this->conn != null) {
        // Construction de la requête
        $requete = "DELETE FROM $table WHERE ";
        $params = array();

        foreach ($champs as $key => $value) {
            $requete .= "$table.$key=:$key AND ";
            $params[":$key"] = $value;
        }
         echo"LA valeur  : ".$value."</br>";
                
        $requete = rtrim($requete, 'AND '); // Supprimer le dernier "AND"
        echo"LA REQUETE  : ".$requete."</br>";
        

         return $this->conn->execute($requete, $params);
        
       } else {
        return null;
     }
    }


    /**
     * ajout d'une ligne dans une table
     * @param string $table nom de la table
     * @param array $champs nom et valeur de chaque champs de la ligne
     * @return true si l'ajout a fonctionné
     */	
    public function insertOne($table, $champs){
        if($this->conn != null && $champs != null){
            // construction de la requête
            $requete = "insert into $table (";
            foreach ($champs as $key => $value){
                $requete .= "$key,";
            }
            // (enlève la dernière virgule)
            $requete = substr($requete, 0, strlen($requete)-1);
            $requete .= ") values (";
            foreach ($champs as $key => $value){
                $requete .= ":$key,";
            }
            // (enlève la dernière virgule)
            $requete = substr($requete, 0, strlen($requete)-1);
            $requete .= ");";	
            return $this->conn->execute($requete, $champs);		
        }else{
            return null;
        }
    }

    /**
     * modification d'une ligne dans une table
     * @param string $table nom de la table
     * @param string $id id de la ligne à modifier
     * @param array $param nom et valeur de chaque champs de la ligne
     * @return true si la modification a fonctionné
     */	
    public function updateOne($table, $id, $champs){
        if($this->conn != null && $champs != null){
            // construction de la requête
            $requete = "update $table set ";
            foreach ($champs as $key => $value){
                $requete .= "$key=:$key,";
            }
            // (enlève la dernière virgule)
            $requete = substr($requete, 0, strlen($requete)-1);				
            $champs["id"] = $id;
            $requete .= " where id=:id;";		
            echo "la requete ".$requete ;
            return $this->conn->execute($requete, $champs);		
        }else{
            return null;
        }
    }

}