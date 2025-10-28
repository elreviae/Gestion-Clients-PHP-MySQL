<?php
// Gestion clients - PHP 8.2 - CRUD - MySQL
// Affichage des résultats en tableau- Datatables JS
// Création et connexion DB => fichier - connexion.php

// Maxime DES TOUCHES - 2025 - https://github.com/elreviae ------------

include("connexion.php");

// Pour AJOUT / MODIFICATION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $civilite = $_POST['civilite'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $email = $_POST['email'] ?? '';
    $adresse = $_POST['adresse'] ?? '';

    if (!empty($_POST['id'])) {
        // --- Mise à jour ---
        $id = (int)$_POST['id'];
        $sql = "UPDATE client SET civilite=:civilite, nom=:nom, prenom=:prenom, tel=:tel, email=:email, adresse=:adresse WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        // compact — Crée un tableau à partir de variables et de leur valeur
        $stmt->execute(compact('civilite', 'nom', 'prenom', 'tel', 'email','adresse', 'id'));
    } else {
        // --- Insertion ---
        $sql = "INSERT INTO client (civilite, nom, prenom, tel, email, adresse) VALUES (:civilite, :nom, :prenom, :tel, :email, :adresse)";
        $stmt = $pdo->prepare($sql);
        // compact — Crée un tableau à partir de variables et de leur valeur
        $stmt->execute(compact('civilite', 'nom', 'prenom', 'tel', 'email', 'adresse'));
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Pour SUPPRESSION
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM client WHERE id=?")->execute([$id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Pour RÉCUPÉRATION DES DONNÉES
$clients = $pdo->query("SELECT * FROM client ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Pour  MODE ÉDITION
$editClient = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM client WHERE id=?");
    $stmt->execute([$id]);
    $editClient = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire Gestion Clients</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/dt/dt-2.3.4/datatables.min.css" rel="stylesheet" integrity="sha384-pmGS6IIcXhAVIhcnh9X/mxffzZNHbuxboycGuQQoP3pAbb0SwlSUUHn2v22bOenI" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<!-- FORMULAIRE -->
<div class="container-md">
    <h1 class="mt-4 mb-4"><i class="fa-solid fa-address-card"></i> Gestion clients</h1>
    <div class="card mb-4 p-4 shadow bg-light">
        <form method="post">

            <div class="row gx-3 gy-2 align-items-center">
                <div class="col-md-4">
                    <input class="form-control" type="hidden" name="id" value="<?= $editClient['id'] ?? '' ?>">
                    <i class="fa-solid fa-user"></i>
                    <label>Civilité :</label>
                    <select class="form-control" name="civilite" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="M." <?= (isset($editClient) && $editClient['civilite'] === 'M.') ? 'selected' : '' ?>>M.</option>
                        <option value="Mme" <?= (isset($editClient) && $editClient['civilite'] === 'Mme') ? 'selected' : '' ?>>Mme</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <i class="fa-solid fa-pen"></i>
                    <label>Prénom :</label>
                    <input class="form-control" type="text" name="prenom" value="<?= $editClient['prenom'] ?? '' ?>" required>
                </div>

                <div class="col-md-4">
                    <i class="fa-solid fa-pen"></i>
                    <label>Nom :</label>
                    <input class="form-control" type="text" name="nom" value="<?= $editClient['nom'] ?? '' ?>" required>
                </div>

                <div class="col-md-4">
                    <i class="fa-solid fa-phone"></i>
                    <label>Téléphone :</label>
                    <input class="form-control" type="text" name="tel" value="<?= $editClient['tel'] ?? '' ?>" pattern="^(?:\+33|0)[1-9](?:[ \.\-]?\d{2}){4}$">
                </div>

                <div class="col-md-4">
                    <i class="fa-solid fa-envelope"></i>
                    <label>Email :</label>
                    <input class="form-control" type="email" name="email" value="<?= $editClient['email'] ?? '' ?>">
                </div>

                <div class="col-md-4">
                    <i class="fa-solid fa-location-dot"></i>
                    <label>Adresse :</label>
                    <input class="form-control" type="text" name="adresse" value="<?= $editClient['adresse'] ?? '' ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><?= isset($editClient) ? "Mettre à jour" : "Ajouter" ?></button>
                    <a href="formulaire.php" class="btn btn-warning">Annuler</a>
                </div>

            </div>

        </form>
    </div>
</div> <!-- Fin container-md--->

    <!-- TABLEAU DES CLIENTS -->
 <div class="container-fluid mb-4 p-4 shadow bg-light">
    <table id="clients-table" class="hover display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Civilité</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Adresse</th>
                <th>Date création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['id']) ?></td>
                <td><?= htmlspecialchars($c['civilite']) ?></td>
                <td><?= htmlspecialchars($c['nom']) ?></td>
                <td><?= htmlspecialchars($c['prenom']) ?></td>
                <td><?= htmlspecialchars($c['tel']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['adresse']) ?></td>
                <td>
                    <?php
                        if (!empty($c['date_creation'])) {
                            $date = new DateTime($c['date_creation']);
                            echo htmlspecialchars($date->format('d/m/Y')); // Format : 15/01/2023
                        } else {
                            echo 'N/A';
                        }
                    ?>
                </td>
                <td>
                    <a href="?edit=<?= $c['id'] ?>" class="btn btn-primary">Modifier</a>
                    <a href="?delete=<?= $c['id'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce client ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <tbody>
    </table>
 </div>






    <footer class="footer mt-auto py-3 bg-light">
        <div class="container">
            <div class="d-flex justify-content-end">
                <div class="text-muted">
                    &copy; Maxime DES TOUCHES - <span id="year"></span> | <a target="_blank"
                        href="https://github.com/elreviae" class="text-dark"> <i class="fa-brands fa-github"></i> </a>
                </div>
            </div>
        </div>
    </footer>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.3.4/datatables.min.js" integrity="sha384-X2pTSfom8FUa+vGQ+DgTCSyBZYkC1RliOduHa0X96D060s7Q//fnOh3LcazRNHyo" crossorigin="anonymous"></script>

    <script>
        // Data Tables init
        new DataTable('#clients-table', {
            responsive: true,
            format: 'YYYY/MM/DD',
            "columnDefs": [
                    { "orderable": false, "targets": 8 } // Désactiver le tri sur la colonne Actions
                ]
        });


        // Date JS
        const d = new Date();
        let year = d.getFullYear();
        document.getElementById("year").innerHTML = year;
    </script>


</body>
</html>
