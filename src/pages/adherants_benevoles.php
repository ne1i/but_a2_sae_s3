<?php
require_once __DIR__ . "/../db.php";
$db = new FageDB();
require_once __DIR__ . "/../templates/admin_cookie_check.php";
require_once __DIR__ . "/../templates/admin_head.php";


?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen flex flex-row">
    <aside class="bg-fage-200 rounded-r-4xl flex flex-row-reverse items-center justify-start">
        <ul class="mx-12 text-center">
            <a href="">
                <li class="border-1 border-x-0 text-2xl hover:bg p-4">
                    Fiches adhérents
                </li>
            </a>
            <a href="">
                <li class="text-2xl hover:bg p-4">
                    Cotisations
                </li>
            </a>
            <a href="">
                <li class="border-1 border-x-0 text-2xl hover:bg p-4">
                    Missions
                </li>
            </a>
        </ul>
    </aside>

    <main class="flex items-center mx-auto">
        <div class="bg-white p-10 shadow-lg px-14">

            <div class="flex flex-col items-center gap-8 ">

                <h1 class="text-xl text-center">Ajouter un adhérent</h1>
                <form action="/adherants_benevoles" method="post" class="flex flex-col bg-white ">
                    <label for="prenom" class="text-lg">Prénom</label>
                    <input required type="text" name="prenom" class="border-2 mb-4 rounded-full pl-2 py-1">
                    <label for="nom" class="text-lg">Nom</label>
                    <input required type="text" name="nom" class="border-2 mb-4 rounded-full pl-2 py-1">
                    <label for="ville" class="text-lg">Ville</label>
                    <input required type="text" name="ville" class="border-2 mb-4 rounded-full pl-2 py-1">
                    <label for="age" class="text-lg">Age</label>
                    <input required type="number" name="age" class="border-2 mb-4 rounded-full pl-2 py-1">
                    <label for="profession" class="text-lg">Profession</label>
                    <input required type="text" name="profession" class="border-2 mb-4 rounded-full pl-2 py-1">
                    <button type="submit" class="bg-fage-700 hover:bg-fage-800 rounded-full py-2 my-4 text-white">Ajouter l'adhérent</button>
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
    </main>
</body>