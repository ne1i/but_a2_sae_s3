<?php
require_once __DIR__ . "/../db.php";
$db = new FageDB();
require_once __DIR__ . "/../templates/admin_cookie_check.php";
require_once __DIR__ . "/../templates/admin_head.php";


$champs = [
    'prenom',
    'nom',
    'adresse',
    'code_postal',
    'ville',
    'tel',
    'email',
    'age',
    'profession'
];

$data = [];

foreach ($champs as $champ) {
    $data[$champ] = $_POST[$champ] ?? null;
}

$complet = true;
$a = 0;
foreach ($data as $valeur) {

    if (empty($valeur)) {

        echo $a;
        $complet = false;
        break;
    }
    $a += 1;
}

if ($complet) {
    if ($db->adherant_exists($data)) {
        $error = "Cet adhérant existe déjà";
    } else {
        $db->add_adherant($data);
        $success = "L'adhérant a bien été ajouté";
    }
}



?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen flex lg:flex-row flex-col">
    <aside class="bg-fage-200 lg:rounded-r-4xl flex lg:flex-row-reverse items-center lg:justify-start justify-center">
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

    <main class="m-auto">
        <div class="bg-white p-10 shadow-lg px-14 rounded-2xl">

            <div class=" ">

                <h1 class="text-3xl text-center mb-4">Ajouter un adhérent</h1>
                <form action="/adherants_benevoles" method="post" class="flex flex-col bg-white">

                    <div class="flex gap-4 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label for="prenom" class="text-lg">Prénom</label>
                            <input required type="text" name="prenom" class="border-2 rounded-full pl-2 py-1">
                        </div>

                        <div class="flex flex-col w-1/2">
                            <label for="nom" class="text-lg">Nom</label>
                            <input required type="text" name="nom" class="border-2 rounded-full pl-2 py-1">
                        </div>
                    </div>

                    <label for="adresse" class="text-lg">Adresse</label>
                    <input required type="text" name="adresse" class="border-2 mb-4 rounded-full pl-2 py-1">

                    <div class="flex gap-4 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label for="code_postal" class="text-lg">Code postal</label>
                            <input required type="text" name="code_postal" class="border-2 rounded-full pl-2 py-1">
                        </div>

                        <div class="flex flex-col w-1/2">
                            <label for="ville" class="text-lg">Ville</label>
                            <input required type="text" name="ville" class="border-2 rounded-full pl-2 py-1">
                        </div>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label for="tel" class="text-lg">Téléphone</label>
                            <input required type="tel" name="tel" class="border-2 rounded-full pl-2 py-1">
                        </div>

                        <div class="flex flex-col w-1/2">
                            <label for="email" class="text-lg">Email</label>
                            <input required type="email" name="email" class="border-2 rounded-full pl-2 py-1">
                        </div>
                    </div>

                    <div class="flex flex-col ">
                        <label for="age" class="text-lg">Âge</label>
                        <input required type="number" name="age" class="border-2 rounded-full pl-2 py-1 pr-2">
                    </div>

                    <div class="flex flex-col ">
                        <label for="profession" class="text-lg">Profession</label>
                        <input required type="text" name="profession" class="border-2 rounded-full pl-2 py-1">
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
        </div>
    </main>
</body>