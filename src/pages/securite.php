<?php

require_once __DIR__ . "/../db.php";

$db = new FageDB();
require_once __DIR__ . "/../templates/admin_cookie_check.php";

$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;
$password_confirm = $_POST["password-confirm"] ?? null;
$role = $_POST["role"] ?? null;
$poles_array = $_POST["poles"] ?? [];

if (isset($username) && isset($password) && isset($role)) {
    if ($username === "" || $password === "" || $password_confirm === "" || $role === "" || ($role === "responsable-pole" && $poles_array === [])) {
        $error = "Identifiants incorrects (champ(s) vide(s))";
    } elseif ($db->exists($username, "users", "username")) {
        $error = "Un compte avec cet identifiant existe déjà";
    } elseif ($password !== $password_confirm) {
        $error = "Les mots de passe ne correspondent pas";
    } else {
        $db->add_user($username, $password, $role, $poles_array);
        $success = "Le compte {$username} a été créé avec succès";
    }
}

require_once __DIR__ . "/../templates/admin_head.php";
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen items-center justify-center">
    <div id="create-user" class="bg-white p-8 flex flex-col gap-12 m-6 rounded-2xl">
        <a href="/admin" class="flex gap-4 items-center text-fage-700 hover:text-fage-800 ">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"
                class="w-10 border-2 rounded-full p-1">
                <path fill-rule="evenodd"
                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
            </svg>
            <p class="text-xl -ml-1 -mt-1 underline">Revenir au back-office</p>
        </a>
        <h1 class="text-3xl">Créer un nouvel utilisateur</h1>
        <form action="/securite" method="post" class="flex flex-col ">
            <label for="username" class="text-lg">Nom d'utilisateur</label>
            <input required type="username" name="username" class="border-2 mb-4 rounded-full pl-2 py-1">
            <label for="password" class="text-lg">Mot de passe</label>
            <input required type="password" name="password" class="border-2 mb-4 rounded-full pl-2 py-1">
            <label for="password-confirm" class="text-lg">Confirmer le mot de passe</label>
            <input required type="password" name="password-confirm" class="border-2 mb-4 rounded-full pl-2 py-1">
            <label for="role" class="text-lg">Rôle de l'utilisateur (droits)</label>

            <select required id="role" name="role" class="border-2 p-1 m-2 mb-4 mx-0">
                <option value="admin">Administrateur (tout les droits)</option>
                <option value="responsable-pole">Responsable de pôle</option>

            </select>

            <div id="choix-poles" class="hidden flex-col justify-start gap-2">
                <label>
                    <input type="checkbox" name="poles[]" value="responsable-benevoles">
                    Pôle Bénévoles
                </label>

                <label>
                    <input type="checkbox" name="poles[]" value="responsable-communication">
                    Pôle Communication
                </label>

                <label>
                    <input type="checkbox" name="poles[]" value="responsable-partenariats">
                    Pôle Partenariats
                </label>

                <label>
                    <input type="checkbox" name="poles[]" value="responsable-tresorerie">
                    Pôle Trésorerie
                </label>

                <label>
                    <input type="checkbox" name="poles[]" value="responsable-direction">
                    Pôle Direction
                </label>
            </div>

            <script>
                document.getElementById("role").addEventListener("change", function() {
                    const bloc = document.getElementById("choix-poles");

                    if (this.value === "responsable-pole") {
                        bloc.style.display = "flex";
                    } else {
                        bloc.style.display = "none";
                    }
                });
            </script>

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