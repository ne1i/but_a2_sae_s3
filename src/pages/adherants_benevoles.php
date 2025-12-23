<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\validation\Validators;

$db = new FageDB();

require_once __DIR__ . "/../templates/admin_cookie_check.php";
require_once __DIR__ . "/../templates/admin_head.php";



$result = Validators::validate_add_adherent($_POST);

if ($result->isValid()) {
    if ($db->adherant_exists($data)) {
        $error = "Cet adhérant existe déjà";
    } else {
        $db->add_adherant($data);
        $success = "L'adhérant a bien été ajouté";
    }
}
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <div id="wrapper" class="flex lg:flex-row flex-col gap-2 lg:mx-0 mx-2 lg:mt-2 mt-0 ">
        <aside class="bg-fage-200 shadow-sm flex lg:justify-start items-start py-2 lg:rounded-r-2xl rounded-b-2xl">
            <ul class="text-center text-2xl px-2 lg:grow-0 grow space-y-2 py-4">
                <li class="hover:bg-fage-700/50 hover:bg border shadow-sm border-fage-900 rounded-3xl ">
                    <a class="block p-1 px-4" href="#">
                        Fiches adhérents
                    </a>
                </li>
                <li class="hover:bg-fage-700/50 border shadow-sm border-fage-900 rounded-3xl hover:bg">
                    <a class="block p-1 px-4" href="#">
                        Cotisations
                    </a>
                </li>
                <li class="hover:bg-fage-700/50 border shadow-sm border-fage-900 rounded-3xl">
                    <a class="block p-1 px-4" href="#">
                        Missions
                    </a>
                </li>
            </ul>
        </aside>

        <main class="lg:mr-2 mr-0 grow">
            <div class="shadow-sm bg-white p-10 px-14 rounded-2xl">
                <h1 class=" text-3xl text-shadow-2xs mb-4">Ajouter un adhérent</h1>
                <form action="/adherants_benevoles" method="post" class="flex flex-col bg-white">
                    <div class="flex gap-4 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label for="prenom" class="text-lg">Prénom</label>
                            <input required type="text" name="prenom" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]">
                        </div>

                        <div class="flex flex-col w-1/2">
                            <label for="nom" class="text-lg">Nom</label>
                            <input required type="text" name="nom" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]">
                        </div>
                    </div>

                    <label for="adresse" class="text-lg">Adresse</label>
                    <input required type="text" name="adresse" class="border-2 mb-4 rounded-full pl-2 py-1 bg-[#fafafa]">

                    <div class="flex gap-4 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label for="code_postal" class="text-lg">Code postal</label>
                            <input required type="text" name="code_postal" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]">
                        </div>

                        <div class="flex flex-col w-1/2">
                            <label for="ville" class="text-lg">Ville</label>
                            <input required type="text" name="ville" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]">
                        </div>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label for="tel" class="text-lg">Téléphone</label>
                            <input required type="tel" name="tel" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]">
                        </div>

                        <div class="flex flex-col w-1/2">
                            <label for="email" class="text-lg">Email</label>
                            <input required type="email" name="email" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]">
                        </div>
                    </div>

                    <div class="flex flex-col ">
                        <label for="age" class="text-lg">Âge</label>
                        <input required type="number" name="age" class="border-2 rounded-full pl-2 py-1 pr-2 bg-[#fafafa]">
                    </div>

                    <div class="flex flex-col ">
                        <label for="profession" class="text-lg">Profession</label>
                        <input required type="text" name="profession" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]">
                    </div>

                    <button type="submit" class="bg-fage-700 hover:bg-fage-800 rounded-full py-2 my-4 text-white">
                        Ajouter l'adhérent
                    </button>

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
        </main>

    </div>
</body>