     <?php
     // Paramètres de connexion à la DB
     $host = 'localhost';
     $user = 'root';
     $pass = '';  // Mettre le mot de passe si nécessaire
     $dbname = 'gestion_clientsDB';

     try {
         // Connexion initiale au serveur MySQL (sans DB spécifique)
         $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
         ]);

         // Création de la base de données si elle n'existe pas
         $sql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
         $pdo->exec($sql);

         // Reconnexion à la base spécifique
         $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
         ]);

         // Création de la table si elle n'existe pas
         $sql = "
             CREATE TABLE IF NOT EXISTS client (
                 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                 civilite VARCHAR(10) NOT NULL,
                 nom VARCHAR(100) NOT NULL,
                 prenom VARCHAR(100) NOT NULL,
                 tel VARCHAR(20),
                 email VARCHAR(150),
                 adresse VARCHAR(150),
                 date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
         ";
         $pdo->exec($sql);

     } catch (PDOException $e) {
         // Afficher l'erreur pour le debug (en prod, loggez-la)
         die("Erreur de connexion ou de création : " . $e->getMessage());
     }
     ?>




