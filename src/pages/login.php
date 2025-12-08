<?php

require_once __DIR__ . "/../db.php";

$db = new FageDB();

$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;
$session_id = random_bytes(32);

if (isset($username) && isset($password)) {
    if ($db->check_login_creds($username, $password)) {
        $expiration_date = time() + 24 * 60 * 60;
        setcookie("session", $session_id, $expires_or_options = $expiration_date, $path = "/", $domain = "", $secure = true, $httpsecure = true);
        $db->create_session($username, $session_id, date("Y-m-d H:i:s", $expiration_date));
        header('Location: /admin');
        die();
    }
    $error = "Identifiants invalides";
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ .  "/../templates/head.php";
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen flex items-center justify-center">
    <div class="px-4">

        <div class="bg-white p-10 shadow-lg">
            <a href="/" class="flex gap-4 items-center text-fage-700 hover:text-fage-800 ">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"
                    class="w-10 border-2 rounded-full p-1">
                    <path fill-rule="evenodd"
                        d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                </svg>
                <p class="text-xl -ml-1 -mt-1 underline">Revenir à la page d'accueil</p>
            </a>
            <div class="flex flex-col items-center gap-8 ">

                <img src="assets/image/Logo_FAGE.svg" alt="Logo FAGE" class="max-w-50 mt-8">
                <h1 class="text-xl text-center">Connexion à la page administrateur</h1>
                <form action="/login" method="post" class="flex flex-col ">
                    <label for="username" class="text-lg">Nom d'utilisateur</label>
                    <input required type="username" name="username" class="border-2 mb-4 rounded-full pl-2 py-1">
                    <label for="password" class="text-lg">Mot de passe</label>
                    <input required type="password" name="password" class="border-2 mb-4 rounded-full pl-2 py-1">
                    <button type="submit" class="bg-fage-700 hover:bg-fage-800 rounded-full py-2 my-4 text-white">Se
                        connecter</button>
                    <?php
                    if (isset($error)) {
                        echo "<span class=\"text-red-500 text-center\">";
                        echo $error;
                        echo "</span>";
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
</body>

</html>