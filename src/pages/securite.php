<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);

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
    <div id="create-user" class="bg-white p-8 m-6 rounded-2xl">
        <div class="mb-4">

            <?= c::BackToLink(); ?>
        </div>
        <h1 class="text-3xl mb-4">Créer un nouvel utilisateur</h1>
        <div>
            <form action="/securite" method="post" class="flex flex-col ">
                <?= c::FormInput("username", "Nom d'utilisateur", "text", "", true, "mb-4") ?>
                <?= c::FormInput("password", "Mot de passe", "password", "", true, "mb-4") ?>
                <?= c::FormInput("password-confirm", "Confirmer le mot de passe", "password", "", true, "mb-4") ?>

                <label for="role" class="text-lg">Rôle de l'utilisateur (droits)</label>
                <?php
                $role_options = [
                    'admin' => 'Administrateur (tout les droits)',
                    'responsable-pole' => 'Responsable de pôle'
                ];
                echo c::FormSelect("role", label: "", options: $role_options, selected: "", class: "mb-4", attributes: ["id" => "role", "required"]);
                ?>

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

                <?= c::Button("Créer le compte", "fage", "submit", "my-4") ?>
                <?php
                if (isset($error)) {
                    echo c::Message($error, 'error');
                }

                if (isset($success)) {
                    echo c::Message($success, 'success');
                }
                if (Constants::is_debug()) {
                ?>
                    <?= c::Button("Autofill (debug)", "fage", "button", "", ["id" => "autofill"]) ?>

                    <script>
                        function makeid(length) {
                            var result = '';
                            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                            var charactersLength = characters.length;
                            for (var i = 0; i < length; i++) {
                                result += characters.charAt(Math.floor(Math.random() * charactersLength));
                            }
                            return result;
                        }

                        autofill.addEventListener("click", () => {
                            document.querySelector("[name='username']").value = makeid(6);
                            const pwd = makeid(6)
                            document.querySelector("[name='password']").value = pwd;
                            document.querySelector("[name='password-confirm']").value = pwd;

                        });
                    </script>
                <?php
                }
                ?>
            </form>
        </div>
    </div>


    </html>