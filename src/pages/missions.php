<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\dto\AddMissionDto;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\validation\Validators;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

if (HttpUtils::isPost()) {
    $result = Validators::validate_add_mission($_POST);

    if ($result->isValid()) {
        /** @var AddMissionDto $new_mission */
        $new_mission = $result->value();
        $user_id = HttpUtils::get_current_user_id($db);

        if ($db->add_mission(
            $new_mission->title,
            $new_mission->description,
            $new_mission->location,
            $new_mission->start_at,
            $new_mission->end_at,
            $new_mission->capacity,
            $new_mission->budget_cents,
            $user_id
        )) {
            $success = "La mission \"{$new_mission->title}\" a bien été ajoutée";
        } else {
            $error = "Une erreur est survenue lors de l'ajout";
        }
    }
} elseif (HttpUtils::isGet()) {
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $mission = $db->get_mission_by_id($delete_id);

        if ($mission) {
            if ($db->delete_mission($delete_id)) {
                $delete_success = "La mission \"{$mission['title']}\" a bien été supprimée";
            } else {
                $delete_error = "Une erreur est survenue lors de la suppression";
            }
        } else {
            $delete_error = "Mission introuvable";
        }
    }
}
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink(); ?>
                </div>
                <?= c::Heading2("Ajouter une mission") ?>
                <form action="/missions" method="post" class="flex flex-col bg-white">
                    <?= c::FormInput("title", "Titre de la mission", "text", "", true, "mb-4") ?>

                    <div class="mb-4">
                        <?= c::Textarea("description", "Description", "", true, "", ["rows" => "4"]) ?>
                    </div>

                    <?= c::FormInput("location", "Lieu", "text", "", true, "mb-4") ?>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <?= c::FormDateTime("start_date", "Date de début", "date", "", true, "", []) ?>
                        <?= c::FormDateTime("start_time", "Heure de début", "time", "", true, "", []) ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <?= c::FormDateTime("end_date", "Date de fin", "date", "", true, "", []) ?>
                        <?= c::FormDateTime("end_time", "Heure de fin", "time", "", true, "", []) ?>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <?= c::FormInput("capacity", "Capacité (participants)", "number", "", false, "", ["min" => "1"]) ?>
                        </div>
                        <div class="w-1/2">
                            <?= c::FormInput("budget_cents", "Budget (en euros)", "number", "", false, "", ["min" => "0", "step" => "0.01"]) ?>
                        </div>
                    </div>

                    <?php
                    if (isset($error)) {
                        echo c::Message($error, 'error');
                    }

                    if (isset($success)) {
                        echo c::Message($success, 'success');
                    }
                    ?>

                    <?= c::Button("Ajouter la mission", "fage", "submit", "my-4") ?>

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
                                document.querySelector("[name='title']").value = "Mission " + makeid(6);
                                document.querySelector("[name='description']").value = "Description de la mission";
                                document.querySelector("[name='location']").value = "Paris";

                                const today = new Date();
                                const tomorrow = new Date(today);
                                tomorrow.setDate(tomorrow.getDate() + 1);

                                document.querySelector("[name='start_date']").value = today.toISOString().split('T')[0];
                                document.querySelector("[name='start_time']").value = "09:00";
                                document.querySelector("[name='end_date']").value = tomorrow.toISOString().split('T')[0];
                                document.querySelector("[name='end_time']").value = "17:00";

                                document.querySelector("[name='capacity']").value = "20";
                                document.querySelector("[name='budget_cents']").value = "500.50";
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
                <?= c::Heading2("Missions", id: "missions-table") ?>
                <?php
                if (isset($delete_error)) {
                    echo c::Message($delete_error, 'error');
                }
                if (isset($delete_success)) {
                    echo c::Message($delete_success, 'success');
                }
                ?>
                <div class="space-y-4">
                    <?php
                    $page = max($_GET["page"] ?? 1, 1);
                    $count = 3;

                    $filter_title = $_GET["filter-title"] ?? "";
                    $filter_location = $_GET["filter-location"] ?? "";

                    $total_count = $db->get_missions_count($filter_title, $filter_location);
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

                                foreach ($db->get_missions($count, $page, $filter_title, $filter_location) as $mission) {
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
                                            <span class="ml-2">
                                                <a href="/missions?delete_id=<?= $mission['id'] ?>" class="text-red-600 underline" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette mission ?')">Supprimer</a>
                                            </span>
                                        </td>
                                    </tr>
                                <?php
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