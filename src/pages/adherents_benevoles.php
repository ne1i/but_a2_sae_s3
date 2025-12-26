<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\dto\AddAdherentDto;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\validation\Validators;
use ButA2SaeS3\Components as c;
use ButA2SaeS3\utils\HtmlUtils;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";


if (HttpUtils::isPost()) {
    $result = Validators::validate_add_adherent($_POST);

    if ($result->isValid()) {

        /** @var AddAdherentDto $new_adherent */
        $new_adherent = $result->value();

        if ($db->adherent_exists($new_adherent->prenom, $new_adherent->nom, $new_adherent->email)) {
            $error = "Cet adhérant existe déjà";
        } else {
            $db->add_adherent($new_adherent);
            $success = "L'adhérant $new_adherent->prenom $new_adherent->nom a bien été ajouté";
        }
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
            <div class="shadow-sm bg-white p-10 px-14 rounded-2xl space-y-8">
                <div>
                    <div class="mb-2">

                        <?= c::BackToLink(); ?>
                    </div>
                    <?= c::Heading2("Ajouter un adhérent") ?>
                    <form action="/adherents_benevoles" method="post" class="flex flex-col bg-white">
                        <div class="flex gap-4 mb-4">
                            <div class="flex flex-col w-1/2">
                                <label for="prenom" class="text-lg">Prénom</label>
                                <input required type="text" name="prenom" class=" border-2 rounded-full pl-2 py-1 bg-[#fafafa]">

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
                        <?php
                        if (Constants::is_debug()) {
                        ?>
                            <button id="autofill" type="button" class="bg-fage-700 hover:bg-fage-800 rounded-full py-2 text-white">Autofill (debug)</button>

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
                                    document.querySelector("[name='prenom']").value = makeid(6);
                                    document.querySelector("[name='nom']").value = makeid(6);
                                    document.querySelector("[name='adresse']").value = makeid(6);
                                    document.querySelector("[name='code_postal']").value = makeid(6);
                                    document.querySelector("[name='ville']").value = makeid(6);
                                    document.querySelector("[name='tel']").value = "0102030405";
                                    document.querySelector("[name='email']").value = "test@test.com";
                                    document.querySelector("[name='age']").value = "20";
                                    document.querySelector("[name='profession']").value = "employé";

                                });
                            </script>
                        <?php
                        }
                        ?>

                    </form>
                </div>
                <div>

                    <?= c::Heading2("Adhérents", id: "adherents-table") ?>

                    <div class="space-y-4">
                        <?php
                        $page = max($_GET["page"] ?? 1, 1);
                        $count = 3;

                        $page_count = $db->adherents_count() / $count;
                        ?>

                        <fieldset>
                            <form id="adherentForm" action="/adherents_benevoles#adherents-table" class="flex gap-4 flex-wrap items-center">
                                <script>
                                    /** @type {HTMLFormElement} */
                                    let adherentForm = window.adherentForm;
                                    adherentForm.addEventListener("submit", (e) => {
                                        const btn = e.submitter;
                                        /** @type {number | undefined} */
                                        const nextPage = btn.dataset.nextPage;
                                        if (nextPage) {
                                            /** @type { HTMLFormElement} */
                                            let target = e.target;

                                            target.getElementsByTagName("input").namedItem("page").value = nextPage;
                                        }
                                    })
                                </script>
                                <label> Filtrer la ville :
                                    <select class="border shadow-sm px-2" name="filter-ville" id="filter-ville" placeholder="Filtrer la ville">
                                        <option value="paris">Paris</option>
                                    </select>
                                </label>

                                <label> Age :
                                    <input class="border shadow-sm px-2" name="filter-age" type="number" min="0" max="99" list="tickmarks" title="Filtrer par âge (eg. 18)" placeholder="18">
                                </label>

                                <label> Profession :
                                    <input class="border shadow-sm px-2" list="professions" id="filter-profession" name="filter-profession" />
                                    <datalist id="professions">
                                        <option value="Chocolat"></option>
                                        <option value="Noix de coco"></option>
                                        <option value="Menthe"></option>
                                        <option value="Fraise"></option>
                                        <option value="Vanille"></option>
                                    </datalist>
                                </label>
                                <input type="hidden" name="page" value="<?= $page ?>">
                                <button type="submit" class="bg-fage-700 hover:bg-fage-800 rounded-full py-2 px-6 text-white">Filtrer</button>
                            </form>
                        </fieldset>
                        <div class="scroll-container">

                            <table class="border shadow-sm table-auto w-full overflow-x-scroll">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-4 py-2 text-left">Nom</th>
                                        <th class="border px-4 py-2 text-left">Prénom</th>
                                        <th class="border px-4 py-2 text-left">Adresse</th>
                                        <th class="border px-4 py-2 text-left">Profession</th>
                                        <th class="border px-4 py-2 text-left">Âge</th>
                                        <th class="border px-4 py-2 text-left">Ville</th>
                                        <th class="border px-4 py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $idx = 0;

                                    foreach ($db->get_adherents($count, page: $page) as $adherent) {
                                        echo c::AdherantTableRow($adherent, alternate: $idx % 2 == 0);
                                        $idx++;
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                        <div class="flex justify-center gap-4 items-center">
                            <?php
                            $previous = $page > 1;
                            $next = $page < $page_count;
                            $class_disabled = "";

                            $previous_page = max(1, $page  - 1);
                            $next_page = min($page_count, $page  + 1);
                            ?>
                            <button
                                type="submit"
                                form="adherentForm"
                                data-next-page="<?= $previous_page ?>"
                                class="min-w-[13ch] text-center rounded-full px-4 py-2   text-shadow-2xs shadow-sm <?= $previous ? "bg-fage-500 text-white" : "bg-gray-300 text-black" ?>"
                                <?= !$previous ? "inert" : "" ?>>Précédent</button>
                            <span class="rounded-full text-white text-shadow-2xs shadow-sm bg-fage-700 inline-block px-2"><?= $page ?></span>
                            <button
                                type="submit"
                                form="adherentForm"
                                data-next-page="<?= $next_page ?>"
                                class=" min-w-[13ch] text-center rounded-full px-4 py-2 text-shadow-2xs shadow-sm <?= $next ? "bg-fage-500 text-white" : "bg-gray-300 text-black" ?>"
                                <?= !$next ? "inert" : "" ?>>Suivant</button>
                        </div>
                    </div>

                </div>
            </div>

        </main>

    </div>
</body>