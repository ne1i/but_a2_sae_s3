<?php

require_once __DIR__ . "/../templates/admin_cookie_check.php";
require_once __DIR__ . "/../templates/admin_head.php";
require_once __DIR__ . "/../db.php";

$db = new FageDB();

$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;
$password_confirm = $_POST["password-confirm"] ?? null;

if (isset($username) && isset($password)) {
    if ($db->exists($username, "users", "username")) {
        $error = "Un compte avec cet identifiant existe déjà";
    } elseif ($password !== $password_confirm) {
        $error = "Les mots de passes ne correspondent pas";
    } elseif ($username === "" || $password === "" || $password_confirm === "") {
        $error = "Identifiants incorrects (champ(s) vide(s)";
    } elseif (!isset($error)) {
        $db->add_user($username, $password);
        $success = "Le compte {$username} à été créé avec succès";
    }
}



?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen flex flex-col items-center justify-center">
    <div id="create-user" class="bg-white p-8 flex flex-col gap-12">
        <h1 class="text-3xl">Créer un nouvel utilisateur</h1>
        <form action="/securite" method="post" class="flex flex-col ">
            <label for="username" class="text-lg">Nom d'utilisateur</label>
            <input type="username" name="username" class="border-2 mb-4 rounded-full pl-2 py-1">
            <label for="password" class="text-lg">Mot de passe</label>
            <input type="password" name="password" class="border-2 mb-4 rounded-full pl-2 py-1">
            <label for="password-confirm" class="text-lg">Confirmer le mot de passe</label>
            <input type="password" name="password-confirm" class="border-2 mb-4 rounded-full pl-2 py-1">
            <button type="submit" class="bg-fage-700 hover:bg-fage-800 rounded-full py-2 my-4 text-white">Créer le compte</button>
            <?php
            if (isset($error)) {
                echo "<span class=\"text-red-500 text-center\">";
                echo $error;
                echo "</span>";
            }

            if (isset($success)) {
                echo "<span class=\"text-green-500 text-center\">";
                echo $success;
                echo "</span>";
            }
            ?>
        </form>
    </div>

    </html>