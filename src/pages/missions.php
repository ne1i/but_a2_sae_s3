<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\repositories\MissionRepository;
use ButA2SaeS3\services\FormService;
use ButA2SaeS3\services\MissionValidationService;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();
$missionRepository = new MissionRepository($db->getConnection());

HttpUtils::ensure_valid_session($db);


if (HttpUtils::isPost() && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    $mission = $missionRepository->findById($delete_id);

    if ($mission && $missionRepository->delete($delete_id)) {
        FormService::setSuccessMessage("La mission \"{$mission['title']}\" a bien été supprimée", "mission_delete");
        header("Location: /missions?success=1&success_form=mission_delete#missions-table");
    } else {
        FormService::setErrorMessage($mission ? "Une erreur est survenue lors de la suppression" : "Mission introuvable", "mission_delete");
        header("Location: /missions#missions-table");
    }
    exit();
}

FormService::handleFormSubmission(
    [MissionValidationService::class, 'validateAddMission'],
    function ($dto) use ($missionRepository, $db) {
        $user_id = HttpUtils::get_current_user_id($db);
        $missionRepository->add($dto, $user_id);
    },
    "La mission a bien été ajoutée",
    "/missions#mission-add-form",
    "mission_add"
);

$addState = FormService::restoreFormData("mission_add");
$formData = $addState['data'] ?? [];
$formErrors = $addState['errors'] ?? [];
$addSuccessMessage = FormService::getSuccessMessage("mission_add");
$addErrorMessage = FormService::getErrorMessage("mission_add");

$deleteSuccessMessage = FormService::getSuccessMessage("mission_delete");
$deleteErrorMessage = FormService::getErrorMessage("mission_delete");
?>

<?php require_once __DIR__ . "/../templates/admin_head.php"; ?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">
        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink(); ?>
                </div>
                <?= c::Heading2("Ajouter une mission", id: "mission-add-form") ?>
                <form id="missionAddForm" action="/missions" method="post" class="flex flex-col bg-white">
                    <?= c::FormInput("title", "Titre de la mission", "text", $formData['title'] ?? "", true, "mb-4", ["error" => $formErrors['title'] ?? null]) ?>

                    <div class="mb-4">
                        <?= c::Textarea("description", "Description", $formData['description'] ?? "", true, "", ["rows" => "4", "error" => $formErrors['description'] ?? null]) ?>
                    </div>

                    <?= c::FormInput("location", "Lieu", "text", $formData['location'] ?? "", true, "mb-4", ["error" => $formErrors['location'] ?? null]) ?>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <?= c::FormDateTime("start_date", "Date de début", "date", $formData['start_date'] ?? "", true, "", []) ?>
                        <?= c::FormDateTime("start_time", "Heure de début", "time", $formData['start_time'] ?? "", true, "", []) ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <?= c::FormDateTime("end_date", "Date de fin", "date", $formData['end_date'] ?? "", true, "", []) ?>
                        <?= c::FormDateTime("end_time", "Heure de fin", "time", $formData['end_time'] ?? "", true, "", []) ?>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <?= c::FormInput("capacity", "Capacité (participants)", "number", $formData['capacity'] ?? "", false, "", ["min" => "1", "error" => $formErrors['capacity'] ?? null]) ?>
                        </div>
                        <div class="w-1/2">
                            <?= c::FormInput("budget_cents", "Budget (en euros)", "number", $formData['budget_cents'] ?? "", false, "", ["min" => "0", "step" => "0.01", "error" => $formErrors['budget_cents'] ?? null]) ?>
                        </div>
                    </div>

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

                    <?= c::Button("Ajouter la mission", "fage", "submit", "my-4") ?>

                    <?php
                    if (Constants::is_debug()) {
                    ?>
                        <?= c::Button("Autofill (debug)", "fage", "button", "", ["id" => "autofill"]) ?>

                        <script>
                            autofill.addEventListener("click", () => {
                                const form = document.getElementById("missionAddForm");
                                const title = form?.querySelector("[name='title']");
                                if (title) title.value = randomMissionTitle();
                                const description = form?.querySelector("[name='description']");
                                if (description) description.value = randomMissionDescription();
                                const location = form?.querySelector("[name='location']");
                                if (location) location.value = randomCity();

                                const today = new Date();
                                const tomorrow = new Date(today);
                                tomorrow.setDate(tomorrow.getDate() + 1);

                                const startDate = form?.querySelector("[name='start_date']");
                                if (startDate) startDate.value = today.toISOString().split('T')[0];
                                const startTime = form?.querySelector("[name='start_time']");
                                if (startTime) startTime.value = "09:00";
                                const endDate = form?.querySelector("[name='end_date']");
                                if (endDate) endDate.value = tomorrow.toISOString().split('T')[0];
                                const endTime = form?.querySelector("[name='end_time']");
                                if (endTime) endTime.value = "17:00";

                                const capacity = form?.querySelector("[name='capacity']");
                                if (capacity) capacity.value = String(between(1, 100));
                                const budget = form?.querySelector("[name='budget_cents']");
                                if (budget) budget.value = String(between(100, 10000));
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
                <?= c::Heading2("Missions", id: "missions-table") ?>

                <div class="space-y-4">
                    <?php
                    $page = max($_GET["page"] ?? 1, 1);
                    $count = 3;

                    $filter_title = $_GET["filter-title"] ?? "";
                    $filter_location = $_GET["filter-location"] ?? "";

                    $total_count = $missionRepository->count($filter_title, $filter_location);
                    $page_count = ceil($total_count / $count);
                    ?>

                    <fieldset>
                        <form id="missionForm" method="get" action="/missions#missions-table" class="flex gap-4 flex-wrap items-end">
                            <script>
                                let missionForm = window.missionForm;
                                missionForm.addEventListener("submit", (e) => {
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

                            <?= c::FormInput("filter-title", "Filtrer le titre", "text", $filter_title, false, "border shadow-sm px-2", ["placeholder" => "Filtrer le titre"]) ?>

                            <?= c::FormInput("filter-location", "Filtrer le lieu", "text", $filter_location, false, "border shadow-sm px-2", ["placeholder" => "Filtrer le lieu"]) ?>

                            <input type="hidden" name="page" value="<?= $page ?>">
                            <?= c::Button("Filtrer", "fage", "submit") ?>
                            <?php if (!empty($filter_title) || !empty($filter_location)): ?>
                                <?= c::Button("Effacer les filtres", "gray", "link", "", ["href" => "/missions#missions-table"]) ?>
                            <?php endif; ?>
                        </form>
                    </fieldset>

                    <div>
                        <?php if ($deleteSuccessMessage): ?>
                            <?= c::Message($deleteSuccessMessage, 'success') ?>
                        <?php endif; ?>
                        <?php if ($deleteErrorMessage): ?>
                            <?= c::Message($deleteErrorMessage, 'error') ?>
                        <?php endif; ?>
                    </div>

                    <?php
                    $missionsList = $missionRepository->findAll($count, $page, $filter_title, $filter_location);
                    if (count($missionsList) > 0): ?>
                        <div class="scroll-container">
                            <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border-2 px-4 py-2 text-left">Titre</th>
                                        <th class="border-2 px-4 py-2 text-left">Lieu</th>
                                        <th class="border-2 px-4 py-2 text-left">Date début</th>
                                        <th class="border-2 px-4 py-2 text-left">Date fin</th>
                                        <th class="border-2 px-4 py-2 text-left">Capacité</th>
                                        <th class="border-2 px-4 py-2 text-left">Budget</th>
                                        <th class="border-2 px-4 py-2 text-left">Créé par</th>
                                        <th class="border-2 px-4 py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $idx = 0;

                                    foreach ($missionsList as $mission) {
                                        $start_date = date('d/m/Y H:i', $mission['start_at']);
                                        $end_date = date('d/m/Y H:i', $mission['end_at']);
                                        $budget = number_format($mission['budget_cents'] / 100, 2) . ' €';
                                        $capacity = $mission['capacity'] ?? 'Illimitée';
                                        $created_by = $mission['created_by_username'] ?? 'Inconnu';
                                    ?>
                                        <tr class="<?= $idx % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                            <td class="border-2 px-4 py-2"><?= htmlspecialchars($mission['title']) ?></td>
                                            <td class="border-2 px-4 py-2"><?= htmlspecialchars($mission['location']) ?></td>
                                            <td class="border-2 px-4 py-2"><?= $start_date ?></td>
                                            <td class="border-2 px-4 py-2"><?= $end_date ?></td>
                                            <td class="border-2 px-4 py-2"><?= $capacity ?></td>
                                            <td class="border-2 px-4 py-2"><?= $budget ?></td>
                                            <td class="border-2 px-4 py-2"><?= $created_by ?></td>
                                            <td class="border-2 px-4 py-2">
                                                <a href="/edit_mission?id=<?= $mission['id'] ?>" class="text-blue-600 underline">Modifier</a>
                                                <form method="POST" action="/missions#missions-table" style="display: inline;">
                                                    <input type="hidden" name="delete_id" value="<?= $mission['id'] ?>">
                                                    <button type="submit" class="bg-transparent border-0 underline cursor-pointer p-0 font-inherit text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette mission ?')">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php
                                        $idx++;
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <?= c::Message("Aucune mission trouvée", "warning") ?>
                    <?php endif; ?>
                    <div class="flex justify-center gap-4 items-center">
                        <?php
                        $previous = $page > 1;
                        $next = $page < $page_count;

                        $previous_page = max(1, $page  - 1);
                        $next_page = min($page_count, $page  + 1);
                        ?>
                        <?php if ($previous): ?>
                            <?= c::Button("Précédent", "fage", "submit", "min-w-[13ch] text-shadow-2xs shadow-sm", ["form" => "missionForm", "data-next-page" => $previous_page]) ?>
                        <?php else: ?>
                            <?= c::Button("Précédent", "gray", "button", "min-w-[13ch] text-shadow-2xs shadow-sm bg-gray-300 text-black", ["disabled", "form" => "missionForm", "data-next-page" => $previous_page]) ?>
                        <?php endif; ?>
                        <span class="rounded-full text-white text-shadow-2xs shadow-sm bg-fage-700 inline-block px-2"><?= $page ?></span>
                        <?php if ($next): ?>
                            <?= c::Button("Suivant", "fage", "submit", "min-w-[13ch] text-shadow-2xs shadow-sm", ["form" => "missionForm", "data-next-page" => $next_page]) ?>
                        <?php else: ?>
                            <?= c::Button("Suivant", "gray", "button", "min-w-[13ch] text-shadow-2xs shadow-sm bg-gray-300 text-black", ["disabled", "form" => "missionForm", "data-next-page" => $next_page]) ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </main>

</body>