<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\repositories\AdherentRepository;
use ButA2SaeS3\services\AdherentValidationService;
use ButA2SaeS3\services\FormService;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();
$repository = new AdherentRepository($db);

HttpUtils::ensure_valid_session($db);

if (HttpUtils::isPost() && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    $adherent = $repository->findById($delete_id);

    if ($adherent && $repository->delete($delete_id)) {
        FormService::setSuccessMessage("L'adhérent {$adherent->getFullName()} a bien été supprimé", "adherent_delete");
        header("Location: /adherents_benevoles?success=1&success_form=adherent_delete#adherents-table");
    } else {
        FormService::setErrorMessage($adherent ? "Une erreur est survenue lors de la suppression" : "Adhérent introuvable", "adherent_delete");
        header("Location: /adherents_benevoles#adherents-table");
    }
    exit();
}

FormService::handleFormSubmission(
    [AdherentValidationService::class, 'validateAddAdherent'],
    function ($dto) use ($repository) {
        if ($repository->exists($dto->prenom, $dto->nom, $dto->email)) {
            throw new \Exception("Cet adhérent existe déjà");
        }
        $repository->save($dto);
    },
    "L'adhérent a bien été ajouté",
    "/adherents_benevoles#adherent-add-form",
    "adherent_add"
);

$addState = FormService::restoreFormData("adherent_add");
$formData = $addState['data'] ?? [];
$formErrors = $addState['errors'] ?? [];
$addSuccessMessage = FormService::getSuccessMessage("adherent_add");
$addErrorMessage = FormService::getErrorMessage("adherent_add");

$deleteSuccessMessage = FormService::getSuccessMessage("adherent_delete");
$deleteErrorMessage = FormService::getErrorMessage("adherent_delete");
?>

<?php require_once __DIR__ . "/../templates/admin_head.php"; ?>


<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">


    <main class="p-2 space-y-8">
        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl ">
            <div>
                <div class="mb-4">

                    <?= c::BackToLink(); ?>
                </div>
                <?= c::Heading2("Ajouter un adhérent", id: "adherent-add-form") ?>
                <form action="/adherents_benevoles" method="post" class="flex flex-col bg-white">
                    <div class="flex gap-4 mb-4">
                        <?= c::FormInput("prenom", "Prénom", "text", $formData['prenom'] ?? "", true, "", ["container-class" => "w-1/2", "error" => $formErrors['prenom'] ?? null]) ?>
                        <?= c::FormInput("nom", "Nom", "text", $formData['nom'] ?? "", true, "", ["container-class" => "w-1/2", "error" => $formErrors['nom'] ?? null]) ?>
                    </div>

                    <?= c::FormInput("adresse", "Adresse", "text", $formData['adresse'] ?? "", true, "mb-4", ["error" => $formErrors['adresse'] ?? null]) ?>

                    <div class="flex gap-4 mb-4">
                        <?= c::FormInput("code_postal", "Code postal", "text", $formData['code_postal'] ?? "", true, "", ["container-class" => "w-1/2", "error" => $formErrors['code_postal'] ?? null]) ?>
                        <?= c::FormInput("ville", "Ville", "text", $formData['ville'] ?? "", true, "", ["container-class" => "w-1/2", "error" => $formErrors['ville'] ?? null]) ?>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <?= c::FormInput("tel", "Téléphone", "tel", $formData['tel'] ?? "", true, "", ["container-class" => "w-1/2", "error" => $formErrors['tel'] ?? null]) ?>
                        <?= c::FormInput("email", "Email", "email", $formData['email'] ?? "", true, "", ["container-class" => "w-1/2", "error" => $formErrors['email'] ?? null]) ?>
                    </div>

                    <?= c::FormInput("age", "Âge", "number", $formData['age'] ?? "", true, "", ["error" => $formErrors['age'] ?? null]) ?>
                    <?= c::FormInput("profession", "Profession", "text", $formData['profession'] ?? "", true, "", ["error" => $formErrors['profession'] ?? null]) ?>

                    <div>
                        <?php if ($addSuccessMessage): ?>
                            <?= c::Message($addSuccessMessage, 'success') ?>
                        <?php endif; ?>
                        <?php if ($addErrorMessage): ?>
                            <?= c::Message($addErrorMessage, 'error') ?>
                        <?php endif; ?>
                        <?php if (!empty($formErrors['_form'] ?? null)): ?>
                            <?= c::Message($formErrors['_form'], 'error') ?>
                        <?php endif; ?>
                    </div>

                    <?= c::Button("Ajouter l'adhérent", "fage", "submit", "my-4") ?>

                    <?php
                    if (Constants::is_debug()) {
                    ?>
                        <?= c::Button("Autofill (debug)", "fage", "button", "", ["id" => "autofill"]) ?>

                        <script>
                            autofill.addEventListener("click", () => {
                                document.querySelector("[name='prenom']").value = randomPrenom();
                                document.querySelector("[name='nom']").value = randomNom();
                                document.querySelector("[name='adresse']").value = randomAddress();
                                document.querySelector("[name='code_postal']").value = randomCodePostal();
                                document.querySelector("[name='ville']").value = randomCity();
                                document.querySelector("[name='tel']").value = randomTel();
                                document.querySelector("[name='email']").value = randomEmail();
                                document.querySelector("[name='age']").value = randomAge();
                                document.querySelector("[name='profession']").value = randomProfession();

                            });
                        </script>
                    <?php
                    }
                    ?>

                </form>
            </div>
        </div>

        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl space-y-8">

            <div>

                <?= c::Heading2("Adhérents", id: "adherents-table") ?>
                <div>
                    <?php if ($deleteSuccessMessage): ?>
                        <?= c::Message($deleteSuccessMessage, 'success') ?>
                    <?php endif; ?>
                    <?php if ($deleteErrorMessage): ?>
                        <?= c::Message($deleteErrorMessage, 'error') ?>
                    <?php endif; ?>
                </div>

                <div class="space-y-4">
                    <?php
                    $page = max((int)($_GET["page"] ?? 1), 1);
                    $count = 3;

                    $filter_ville = trim($_GET["filter-ville"] ?? "");
                    $filter_age = trim($_GET["filter-age"] ?? "");
                    $filter_profession = trim($_GET["filter-profession"] ?? "");

                    $cities = $repository->getDistinctCities();

                    $total_count = $repository->count([
                        'ville' => $filter_ville,
                        'age' => $filter_age,
                        'profession' => $filter_profession
                    ]);
                    $page_count = max(1, (int)ceil($total_count / $count));
                    ?>

                    <fieldset>
                        <form id="adherentForm" method="get" action="/adherents_benevoles#adherents-table" class="flex gap-4 flex-wrap items-end">
                            <script>
                                let adherentForm = window.adherentForm;
                                adherentForm.addEventListener("submit", (e) => {
                                    const btn = e.submitter;

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
                                $professions = $repository->getDistinctProfessions();
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

                    <?php if ($total_count > 0): ?>
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

                                    $adherents = $repository->findAll($count, $page, [
                                        'ville' => $filter_ville,
                                        'age' => $filter_age,
                                        'profession' => $filter_profession
                                    ]);

                                    foreach ($adherents as $adherent) {
                                        echo c::AdherantTableRow($adherent, alternate: $idx % 2 == 0);
                                        $idx++;
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <?= c::Message("Aucun adhérent trouvé", "error") ?>
                    <?php endif; ?>
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