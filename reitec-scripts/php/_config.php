<?php
// ----------------------------------------------------
session_start();
// -----------------------------------------------------
class Config
{
    // ================================
    public $dbName = '_db_reitec_info';
    public $server = 'localhost';
    public $userName = 'root';
    public $password = '';
    // ================================
    // public $dbName = 'reite2176564';
    // public $server = '127.0.0.1';
    // public $userName = 'reite2176564';
    // public $password = 'qX1@EaJzKcnYTJX';
    // ================================
    private $statusData = 1;
    private $port = 3306;

    function __construct()
    {
        $this->addFiveExtraColumn();
    }
    public function onRetrieveData($tbl, $clause)
    {
        $re = $this->onConn()->prepare("SELECT * FROM $tbl $clause");
        try {
            $re->execute();
            $re = $re->fetchAll();
            if (!empty($re)) {
                if (count($re) > 0) {
                    return $re;
                } else return array();
            } else return array();
        } catch (PDOException $e) {
            // $exc = new LogNotification([Date('d/m/Y, H:i:s')],["SHOW COLUMNS FROM $tbl"],['Failed'],[$e->getMessage()]);
            // $this->errorWritter($exc,2);
            return array();
        }
    }
    public function onRTPaginations($tbl, $clause)
    {
        // $per_page = (int) $offset;
        // $start = (int) $recordPerPage;
        $pre = $this->onConn()->prepare("SELECT * FROM $tbl $clause");
        // $pre->bindValue(':limi', (int) $start, PDO::PARAM_INT);
        // $pre->bindValue(':offset', (int) $per_page, PDO::PARAM_INT);
        // $pre->bindValue(':cat', (int) $categ, PDO::PARAM_INT);
        try {
            $pre->execute();
            $pre = $pre->fetchAll();
            if (!empty($pre)) {
                if (count($pre) > 0) {
                    return $pre;
                } else return array();
            } else return array();
        } catch (PDOException $e) {
            // $exc = new LogNotification([Date('d/m/Y, H:i:s')],["SHOW COLUMNS FROM $tbl"],['Failed'],[$e->getMessage()]);
            // $this->errorWritter($exc,2);
            return array();
        }
    }
    public function onRTPagination($offset, $recordPerPage, $tbl, $clause)
    {
        $per_page = (int) $offset;
        $start = (int) $recordPerPage;
        $pre = $this->onConn()->prepare("SELECT * FROM $tbl $clause");
        $pre->bindValue(':limi', (int) $start, PDO::PARAM_INT);
        $pre->bindValue(':offset', (int) $per_page, PDO::PARAM_INT);
        // $pre->bindValue(':cat', (int) $categ, PDO::PARAM_INT);
        try {
            $pre->execute();
            $pre = $pre->fetchAll();
            if (!empty($pre)) {
                if (count($pre) > 0) {
                    return $pre;
                } else return array();
            } else return array();
        } catch (PDOException $e) {
            // $exc = new LogNotification([Date('d/m/Y, H:i:s')],["SHOW COLUMNS FROM $tbl"],['Failed'],[$e->getMessage()]);
            // $this->errorWritter($exc,2);
            return array();
        }
    }
    public function onRetrieveAllCours()
    {
        $cnx = $this->onConn();
        if ($cnx !== null) {
            $req = $cnx->prepare('SELECT * FROM 
                tbl_cours JOIN 
                tbl_content JOIN 
                tbl_subcateg_cours JOIN 
                tbl_categ_cours ON 
                tbl_cours.idContent = tbl_content.id AND 
                tbl_cours.idCateg = tbl_categ_cours.id AND 
                tbl_categ_cours.id = tbl_subcateg_cours.idcategcours WHERE 
                tbl_cours.datastatus = ? AND 
                tbl_content.datastatus = ?');
            try {
                $req->execute([$this->statusData, $this->statusData]);
                $req = $req->fetchAll();
                if (!empty($req)) {
                    // var_export($req);
                    // $tbCours = [];
                    // for($i = 0; $i < count($req); ++$i){
                    //     $cours = new Cours($req[$i]['0'],$req[$i]['title'],$req[$i]['delai'],$req[$i]['prix'],$req[$i]['description'],
                    //     [
                    //         $req[$i]['nom'],
                    //         $req[$i]['prenom'],	
                    //         $req[$i]['photo']
                    //     ],$req[$i]['content'],$req[$i]['category'],$req[$i]['subcateg']);
                    //     array_push($tbCours,$cours);
                    // }
                    return $req;
                } else return array();
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        } else return array();
    }
    public function onRetrieveAllCours_($offset)
    {
        $cnx = $this->onConn();
        if ($cnx !== null) {
            $req = $cnx->prepare('SELECT * FROM 
                tbl_cours JOIN 
                tbl_facilitateur JOIN 
                tbl_content JOIN 
                tbl_subcateg_cours JOIN 
                tbl_categ_cours ON 
                tbl_cours.idFacilitateur = tbl_facilitateur.id AND 
                tbl_cours.idContent = tbl_content.id AND 
                tbl_cours.idCateg = tbl_categ_cours.id AND 
                tbl_categ_cours.id = tbl_subcateg_cours.idcategcours WHERE 
                tbl_cours.datastatus = ? AND 
                tbl_facilitateur.datastatus = ? AND 
                tbl_content.datastatus = ? LIMIT 4');
            try {
                $req->execute([$this->statusData, $this->statusData, $this->statusData]);
                $req = $req->fetchAll();
                if (!empty($req)) {
                    // var_export($req);
                    $tbCours = [];
                    for ($i = 0; $i < count($req); ++$i) {
                        $cours = new Cours(
                            $req[$i]['0'],
                            $req[$i]['title'],
                            $req[$i]['delai'],
                            $req[$i]['prix'],
                            $req[$i]['description'],
                            [
                                $req[$i]['nom'],
                                $req[$i]['prenom'],
                                $req[$i]['photo']
                            ],
                            $req[$i]['content'],
                            $req[$i]['category'],
                            $req[$i]['subcateg']
                        );
                        array_push($tbCours, $cours);
                    }
                    return $tbCours;
                } else return array();
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        } else return array();
    }
    public function onUpdate($cols, $values, $table, $clause)
    {
        if (is_array($cols) && is_array($values) && (count($cols) === count($values))) {
            $line = '';
            $tab = [];
            for ($i = 0; $i < count($cols); $i++) {
                $val = (is_numeric($values[$i])) ? $values[$i] : "'" . $values[$i] . "'";
                array_push($tab, $cols[$i] . "=" . $val);
            }
            $line = implode(',', $tab);
            // var_dump($line);
            $re = $this->onConn()->prepare("UPDATE $table SET $line $clause");
            try {
                $re->execute();
                return 200;
            } catch (PDOException $e) {
                // $exc = new LogNotification([Date('d/m/Y, H:i:s')],["UPDATE_ ON $table"],['Failed'],[$e->getMessage()]);
                // $this->errorWritter($exc,2);
                var_dump($e->getMessage());
                return 503;
            }
        }
        return 500;
    }
    public function onCatchingSomeCLN($cols, $vls, $clause, $tbl)
    {
        $tb = [];
        $string = '';
        if (is_array($cols) && is_array($vls) && (count($vls) === count($cols))) {
            for ($i = 0; $i < count($cols); $i++) {
                $vl = (is_numeric($vls[$i]) ? $vls[$i] : "'" . $vls[$i] . "'");
                $string .= $cols[$i] . "=" . $vl . " AND ";
                // $string .= $string;
            }
            $string = $string . " datastatus = $this->statusData";
            $req = $this->onConn()->prepare("SELECT * FROM $tbl WHERE $string $clause");
            try {
                $req->execute();
                $req = $req->fetchAll();
                if (!empty($req)) {
                    return 200;
                } else return 403;
            } catch (PDOException $e) {
                // var_dump($e->getMessage());
                return 500;
            }
        } else return 500;
    }
    public function getCoursByID($idx)
    {
        $idx = (int) $idx;
        $cnx = $this->onConn();
        if ($cnx !== null) {
            $req = $cnx->prepare('SELECT * FROM 
                tbl_cours JOIN 
                tbl_facilitateur JOIN 
                tbl_content JOIN 
                tbl_subcateg_cours JOIN 
                tbl_categ_cours ON 
                tbl_cours.idFacilitateur = tbl_facilitateur.id AND 
                tbl_cours.idContent = tbl_content.id AND 
                tbl_cours.idCateg = tbl_categ_cours.id AND 
                tbl_categ_cours.id = tbl_subcateg_cours.idcategcours WHERE 
                tbl_cours.datastatus = ? AND 
                tbl_facilitateur.datastatus = ? AND 
                tbl_content.datastatus = ? AND
                tbl_cours.id = ?');
            try {
                $req->execute([$this->statusData, $this->statusData, $this->statusData, $idx]);
                $req = $req->fetch();
                if (!empty($req)) {
                    $tbCours = [];
                    // for($i = 0; $i < count($req); ++$i){
                    $cours = new Cours(
                        $req['id'],
                        $req['title'],
                        $req['delai'],
                        $req['prix'],
                        $req['description'],
                        [
                            $req['nom'],
                            $req['prenom'],
                            $req['photo']
                        ],
                        $req['content'],
                        $req['category'],
                        $req['subcateg']
                    );
                    array_push($tbCours, $cours);
                    // }
                    return $tbCours;
                } else return array();
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        } else return array();
    }
    public function getFormarionByID($idx)
    {
        $idx = (int) $idx;
        $cnx = $this->onConn();
        if ($cnx !== null) {
            $req = $cnx->prepare('SELECT * FROM 
                tbl_formation JOIN 
                tbl_facilitateur JOIN 
                tbl_content JOIN 
                tbl_categ_cours JOIN 
                tbl_subcateg_cours ON 
                tbl_formation.idCateg = tbl_categ_cours.id AND 
                tbl_formation.idsubcateg = tbl_subcateg_cours.id AND 
                tbl_formation.idcontent = tbl_content.id AND
                tbl_formation.idfacilitateur = tbl_facilitateur.id AND
                tbl_facilitateur.datastatus = ? AND 
                tbl_content.datastatus = ? AND
                tbl_formation.id = ?');
            try {
                $req->execute([(int)$this->statusData, (int)$this->statusData, (int)$idx]);
                $req = $req->fetch();
                if (!empty($req)) {
                    $tbCours = [];
                    $cours = new Formation(
                        $req['id'],
                        $req['title'],
                        $req['datestart'],
                        $req['dateend'],
                        $req['couts'],
                        $req['synthese'],
                        [
                            $req['nom'],
                            $req['prenom'],
                            $req['photo']
                        ],
                        $req['content'],
                        $req['category'],
                        $req['subcateg']
                    );
                    array_push($tbCours, $cours);
                    return $tbCours[0];
                } else return array();
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        } else return array();
    }
    private function addFiveExtraColumn()
    {
        $conn = $this->onConn();
        $req = $conn->prepare('SHOW TABLES');
        try {
            $req->execute();
            $req = $req->fetchAll();
            if (count($req) > 0) {
                foreach ($req as $key => $table) {
                    $this->verifyAndWriteExtraColumn($conn, $table[0]);
                }
            }
        } catch (PDOException $e) {
            // $exc = new LogNotification([Date('d/m/Y, H:i:s')],["Add Missing columns"],['Failed'],[$e->getMessage()]);
            // $this->errorWritter($exc,2);
        }
    }
    private function verifyAndWriteExtraColumn($conn, $table)
    {
        $tabExtraColumn = array('createby', 'modifiedby', 'deletedby', 'createdon', 'datastatus');
        $tabColumn = $conn->prepare("SHOW COLUMNS FROM $table");
        $fictiveTable = [];

        try {
            $tabColumn->execute();
            $tabColumn = $tabColumn->fetchAll();
            $lastColumn = end($tabColumn);
            $lastColumn = $lastColumn[0];

            for ($i = 0; $i < count($tabColumn); $i++) {
                array_push($fictiveTable, $tabColumn[$i]['Field']);
            }
            foreach ($tabExtraColumn as $key => $column) {
                if (!(in_array($column, $fictiveTable, true))) {
                    $re = $conn->prepare("ALTER TABLE `$table` ADD `$column` VARCHAR(60) NOT NULL AFTER `$lastColumn`");
                    try {
                        $re->execute();
                        $lastColumn = $column;
                    } catch (PDOException $e) {
                        // $exc = new LogNotification([Date('d/m/Y, H:i:s')],["Add Missing columns"],['Failed'],[$e->getMessage()]);
                        // $this->errorWritter($exc,2);
                    }
                }
            }
        } catch (PDOException $e) {
            // $exc = new LogNotification([Date('d/m/Y, H:i:s')],["Connexion"],['Failed'],[$e->getMessage()]);
            // $this->errorWritter($exc,2);
        }
    }
    private function retrievesColumn($table, $alias)
    {
        $columnname = [];
        $tabColumn = $this->onConn()->prepare("SHOW COLUMNS FROM $table");
        try {
            $tabColumn->execute();
            $tabColumn = $tabColumn->fetchAll();
            for ($i = 0; $i < count($tabColumn); $i++) {
                array_push($columnname, $tabColumn[$i]['Field']);
            }
            return implode(",", $columnname);
        } catch (PDOException $e) {
            // $exc = new LogNotification([Date('d/m/Y, H:i:s')],["SHOW COLUMNS FROM $table"],['Failed'],[$e->getMessage()]);
            // $this->errorWritter($exc,2);
            return false;
            // die($e->getMessage());
        }
    }
    public function sendMail($im)
    {
        $from_ = 'Reitec Info | Partenaire';
        $message = $im['message'];
        $from = $im['from'];
        $subject = $im['subject'];
        $headers  = "MIME-Version: 1.0 \n";
        $headers .= "Content-type: text/html; charset=iso-8859-1 \n";
        $headers .= "From: $from_ <$from>\r\n" .
            "MIME-Version: 1.0" . "\r\n" .
            "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "Disposition-Notification-To: $from  \n";
        $message = wordwrap($message, 70, "\r\n");
        $to = $im['email'];
        // Message de Priorité haute
        // -------------------------
        $headers .= "X-Priority: 1  \n";
        $headers .= "X-MSMail-Priority: High \n";
        // 'X-Mailer: PHP/' . phpversion();
        if (@mail($to, $subject, $message, $headers)) return true;
        else return false;
    }
    public function onCatchLastElement($tbl, $clause, $sortby)
    {
        $req = $this->onConn()->prepare("SELECT MAX($sortby) FROM $tbl");
        // return $sortby;
        try {
            $req->execute();
            $req = $req->fetchAll();
            var_dump($req);
            $lstIdx = (int) $req[0][0];
            return ($lstIdx);
        } catch (PDOException $e) {
            // $exc = new LogNotification([Date('d/m/Y, H:i:s')],["GETTING LAST ELEM IN $tbl"],['Failed'],[$e->getMessage()]);
            // $this->errorWritter($exc,2);
            return 0;
        }
    }
    public function onCatchingVLS($output, $tbl, $cls)
    {
        $req = $this->onConn()->prepare("SELECT $output FROM $tbl $cls ");
        try {
            $req->execute();
            $req = $req->fetchAll();
            // var_dump($req);
            if (!empty($req) || count($req) > 0) return $req;
            else return 0;
        } catch (PDOException $e) {
            //throw $th;
            // var_dump($e->getMessage());
            return 'undefined';
        }
    }
    public function onCatchVLCOLLN($id, $output, $tbl, $cls)
    {
        $req = $this->onConn()->prepare("SELECT $output FROM $tbl WHERE id = $id $cls");
        try {
            $req->execute();
            $req = $req->fetchAll();
            if (!empty($req)) return $req;
            else return 0;
        } catch (PDOException $e) {
            //throw $th;
            return 'undefined';
        }
    }
    public function onAdd($tbValues, $clause, $accessLevel, $indentified, $table)
    {
        if (!is_array($tbValues) || count($tbValues) === 0) {
            return 500; // Erreur: pas de données à insérer
        }

        // Récupérer les colonnes de la table
        $cls = $this->retrievesColumn($table, false);
        if (empty($cls)) {
            return 500; // Erreur: pas de colonnes récupérées
        }

        // Supprimer la première colonne de la liste (supposition)
        $cls = substr($cls, strpos($cls, ',') + 1);
        // Ajouter les valeurs supplémentaires
        $tbValues[] = $indentified;
        $tbValues[] = 0;
        $tbValues[] = 0;
        $tbValues[] = date('d/m/Y, H:i:s');
        $tbValues[] = 1;
        // Préparer les valeurs avec des quotes pour l'insertion SQL
        $tabvalues = array_map(fn($value) => "'$value'", $tbValues);
        // var_dump($tbValues);
        $vls = implode(',', $tabvalues);

        try {
            // Préparer et exécuter la requête
            $req = $this->onConn()->prepare("INSERT INTO $table ($cls) VALUES ($vls)");
            $req->execute();
            return 200; // Succès
        } catch (PDOException $e) {
            // Afficher les informations pour le débogage
            // echo "Erreur lors de l'insertion : " . $e->getMessage();
            // echo "Valeurs : " . implode(', ', $tbValues);
            // echo "Colonnes : " . $cls;
            return 503; // Erreur lors de l'exécution de la requête
        }
    }

    public function onConnexion($idents, $clause, $tbl)
    {
        if (is_array($idents) && ($this->onConn()) !== null) {
            $req = $this->onConn()->prepare("SELECT * FROM $tbl WHERE email = ? AND pwd = ? AND datastatus = $this->statusData");
            try {
                $req->execute($idents);
                $req = $req->fetch();
                if (!empty($req)) {
                    // setcookie("tkn",md5('dav.me'),time()+3600,'/');
                    $_SESSION['ident-me'] = new Student(
                        $req['id'],
                        $req['nom'],
                        $req['postnom'],
                        $req['email'],
                        $req['telephone'],
                        $req['genre'],
                        $req['prenom']
                    );
                    $_SESSION['reitec-std-session'] = md5($req['email']);
                    return 200;
                } else {
                    return 403;
                }
            } catch (PDOException $e) {
                var_dump($e->getMessage());
                return 500;
            }
        } else return 500;
    }
    public function onConn()
    {
        try {
            $dsn = "mysql:host=$this->server;port=$this->port;dbname=$this->dbName;charset=utf8mb4";
            $pdo = new PDO($dsn, $this->userName, $this->password);

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            die($e->getMessage());
            return null;
        }
    }
}
