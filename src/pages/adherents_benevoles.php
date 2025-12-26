<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\dto\AddAdherentDto;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\validation\Validators;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";


if (HttpUtils::isPost()) {
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $adherent = $db->get_adherent_by_id($delete_id);

        if ($adherent) {
            if ($db->delete_adherent($delete_id)) {
                $delete_success = "L'adhérent {$adherent->prenom} {$adherent->nom} a bien été supprimé";
            } else {
                $delete_error = "Une erreur est survenue lors de la suppression";
            }
        } else {
            $delete_error = "Adhérent introuvable";
        }
    } else {
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
}
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">


    <main class="p-2 space-y-8">
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl ">
            <div>
                <div class="mb-4">

                    <?= c::BackToLink(); ?>
                </div>
                <?= c::Heading2("Ajouter un adhérent") ?>
                <form action="/adherents_benevoles" method="post" class="flex flex-col bg-white">
                    <div class="flex gap-4 mb-4">
                        <?= c::FormInput("prenom", "Prénom", "text", "", true, "", ["container-class" => "w-1/2"]) ?>
                        <?= c::FormInput("nom", "Nom", "text", "", true, "", ["container-class" => "w-1/2"]) ?>
                    </div>

                    <?= c::FormInput("adresse", "Adresse", "text", "", true, "mb-4") ?>

                    <div class="flex gap-4 mb-4">
                        <?= c::FormInput("code_postal", "Code postal", "text", "", true, "", ["container-class" => "w-1/2"]) ?>
                        <?= c::FormInput("ville", "Ville", "text", "", true, "", ["container-class" => "w-1/2"]) ?>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <?= c::FormInput("tel", "Téléphone", "tel", "", true, "", ["container-class" => "w-1/2"]) ?>
                        <?= c::FormInput("email", "Email", "email", "", true, "", ["container-class" => "w-1/2"]) ?>
                    </div>

                    <?= c::FormInput("age", "Âge", "number", "", true) ?>
                    <?= c::FormInput("profession", "Profession", "text", "", true) ?>

                    <?php
                    if (isset($error)) {
                        echo c::Message($error, 'error');
                    }

                    if (isset($success)) {
                        echo c::Message($success, 'success');
                    }
                    ?>

                    <?= c::Button("Ajouter l'adhérent", "fage", "submit", "my-4") ?>

                    <?php
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
        </div>

        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl space-y-8">

            <div>

                <?= c::Heading2("Adhérents", id: "adherents-table") ?>

                <div class="space-y-4">
                    <?php
                    $page = max($_GET["page"] ?? 1, 1);
                    $count = 3;

                    $filter_ville = $_GET["filter-ville"] ?? "";
                    $filter_age = $_GET["filter-age"] ?? "";
                    $filter_profession = $_GET["filter-profession"] ?? "";

                    $cities = $db->get_distinct_cities();

                    $total_count = $db->get_adherents_count($filter_ville, $filter_age, $filter_profession);
                    $page_count = ceil($total_count / $count);
                    ?>

                    <fieldset>
                        <form id="adherentForm" method="get" action="/adherents_benevoles#adherents-table" class="flex gap-4 flex-wrap items-end">
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
                                    } else {
                                        /** @type { HTMLFormElement} */
                                        let target = e.target;
                                        target.getElementsByTagName("input").namedItem("page").value = 1;
                                    }
                                })
                            </script>
                            <?= c::FormInput("filter-ville", "Filtrer la ville", "text", $filter_ville, false, "border-2 shadow-sm px-2", ["list" => "cities", "id" => "filter-ville", "placeholder" => "Filtrer la ville"]) ?>
                            <datalist id="cities">
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?= htmlspecialchars($city) ?>">
                                    <?php endforeach; ?>
                            </datalist>

                            <?= c::FormInput("filter-age", "Age min.", "number", $filter_age, false, "border-2 shadow-sm px-2", ["min" => "0", "max" => "99", "title" => "Filtrer par âge minimum (eg. 18)", "placeholder" => "18"]) ?>

                            <?= c::FormInput("filter-profession", "Profession", "text", $filter_profession, false, "border-2 shadow-sm px-2", ["list" => "professions", "id" => "filter-profession"]) ?>
                            <datalist id="professions">
                                <?php
                                $professions = $db->get_distinct_professions();
                                foreach ($professions as $profession): ?>
                                    <option value="<?= htmlspecialchars($profession) ?>">
                                    <?php endforeach; ?>
                            </datalist>

                            <input type="hidden" name="page" value="<?= $page ?>">
                            <?= c::Button("Filtrer", "fage", "submit") ?>
                            <?php if (!empty($filter_ville) || !empty($filter_age) || !empty($filter_profession)): ?>
                                <?= c::Button("Effacer les filtres", "gray", "link", "", ["href" => "/adherents_benevoles#adherents-table"]) ?>
                            <?php endif; ?>
                        </form>
                    </fieldset>

                    <?php
                    if (isset($delete_error)) {
                        echo c::Message($delete_error, 'error');
                    }
                    if (isset($delete_success)) {
                        echo c::Message($delete_success, 'success');
                    }
                    ?>

                    <div class="scroll-container">
                        <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border-2 px-4 py-2 text-left">Nom</th>
                                    <th class="border-2 px-4 py-2 text-left">Prénom</th>
                                    <th class="border-2 px-4 py-2 text-left">Adresse</th>
                                    <th class="border-2 px-4 py-2 text-left">Profession</th>
                                    <th class="border-2 px-4 py-2 text-left">Âge</th>
                                    <th class="border-2 px-4 py-2 text-left">Ville</th>
                                    <th class="border-2 px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $idx = 0;

                                foreach ($db->get_adherents($count, $page, $filter_ville, $filter_age, $filter_profession) as $adherent) {
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
                        <?php if ($previous): ?>
                            <?= c::Button("Précédent", "fage", "submit", "min-w-[13ch] text-shadow-2xs shadow-sm", ["form" => "adherentForm", "data-next-page" => $previous_page]) ?>
                        <?php else: ?>
                            <?= c::Button("Précédent", "gray", "button", "min-w-[13ch] text-shadow-2xs shadow-sm bg-gray-300 text-black", ["disabled", "form" => "adherentForm", "data-next-page" => $previous_page]) ?>
                        <?php endif; ?>
                        <span class="rounded-full text-white text-shadow-2xs shadow-sm bg-fage-700 inline-block px-2"><?= $page ?></span>
                        <?php if ($next): ?>
                            <?= c::Button("Suivant", "fage", "submit", "min-w-[13ch] text-shadow-2xs shadow-sm", ["form" => "adherentForm", "data-next-page" => $next_page]) ?>
                        <?php else: ?>
                            <?= c::Button("Suivant", "gray", "button", "min-w-[13ch] text-shadow-2xs shadow-sm bg-gray-300 text-black", ["disabled", "form" => "adherentForm", "data-next-page" => $next_page]) ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </main>


</body>