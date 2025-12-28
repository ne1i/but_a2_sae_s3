<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\repositories\AdherentRepository;
use ButA2SaeS3\repositories\MissionRepository;
use ButA2SaeS3\services\FormService;
use ButA2SaeS3\services\MissionValidationService;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();
$missionRepository = new MissionRepository($db->getConnection());
$adherentRepository = new AdherentRepository($db);

HttpUtils::ensure_valid_session($db);

$mission_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$mission_id) {
    header('Location: /missions');
    die();
}

$mission = $missionRepository->findById($mission_id);

if (!$mission) {
    header('Location: /missions');
    die();
}

if (HttpUtils::isPost() && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_participant') {
        $adherent_id = (int)($_POST['adherent_id'] ?? 0);
        $role = trim($_POST['role'] ?? '');
        if ($adherent_id && $role) {
            $missionRepository->addParticipant($mission_id, $adherent_id, $role);
            FormService::setSuccessMessage("Participant ajouté avec succès", "mission_participant_add");
            header("Location: /edit_mission?id={$mission_id}&success=1&success_form=mission_participant_add");
            exit();
        } else {
            FormService::setErrorMessage("Adhérent ou rôle manquant", "mission_participant_add");
            header("Location: /edit_mission?id={$mission_id}");
            exit();
        }
    }

    if ($_POST['action'] === 'remove_participant') {
        $adherent_id = (int)($_POST['adherent_id'] ?? 0);
        if ($adherent_id) {
            $missionRepository->removeParticipant($mission_id, $adherent_id);
            FormService::setSuccessMessage("Participant retiré avec succès", "mission_participant_remove");
            header("Location: /edit_mission?id={$mission_id}&success=1&success_form=mission_participant_remove");
            exit();
        } else {
            FormService::setErrorMessage("Adhérent introuvable", "mission_participant_remove");
            header("Location: /edit_mission?id={$mission_id}");
            exit();
        }
    }
}

FormService::handleFormSubmission(
    function ($data) use ($mission_id) {
        return MissionValidationService::validateUpdateMission($data, $mission_id);
    },
    function ($dto) use ($missionRepository) {
        $missionRepository->update($dto);
    },
    "La mission a bien été modifiée",
    "/edit_mission?id={$mission_id}",
    "mission_update"
);


$missionUpdateState = FormService::restoreFormData("mission_update");
$formData = $missionUpdateState['data'] ?? [];
$formErrors = $missionUpdateState['errors'] ?? [];
$missionUpdateSuccess = FormService::getSuccessMessage("mission_update");
$missionUpdateError = FormService::getErrorMessage("mission_update");

$participantAddSuccess = FormService::getSuccessMessage("mission_participant_add");
$participantAddError = FormService::getErrorMessage("mission_participant_add");
$participantRemoveSuccess = FormService::getSuccessMessage("mission_participant_remove");
$participantRemoveError = FormService::getErrorMessage("mission_participant_remove");

$currentMission = $missionRepository->findById($mission_id);

$start_date = date('Y-m-d', $currentMission['start_at']);
$start_time = date('H:i', $currentMission['start_at']);
$end_date = date('Y-m-d', $currentMission['end_at']);
$end_time = date('H:i', $currentMission['end_at']);

?>

<?php require_once __DIR__ . "/../templates/admin_head.php"; ?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">
        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink("Retour aux missions", "/missions"); ?>
                </div>
                <?= c::Heading2("Modifier la mission") ?>
                <form action="/edit_mission?id=<?= $mission_id ?>" method="post" class="flex flex-col bg-white">
                    <?= c::FormInput("title", "Titre de la mission", "text", htmlspecialchars($formData['title'] ?? $currentMission['title']), true, "mb-4", ["error" => $formErrors['title'] ?? null]) ?>

                    <div class="mb-4">
                        <?= c::Textarea("description", "Description", htmlspecialchars($formData['description'] ?? $currentMission['description']), true, "", ["rows" => "4", "error" => $formErrors['description'] ?? null]) ?>
                    </div>

                    <?= c::FormInput("location", "Lieu", "text", htmlspecialchars($formData['location'] ?? $currentMission['location']), true, "mb-4", ["error" => $formErrors['location'] ?? null]) ?>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <?= c::FormDateTime("start_date", "Date de début", "date", $formData['start_date'] ?? $start_date, true, "", []) ?>
                        <?= c::FormDateTime("start_time", "Heure de début", "time", $formData['start_time'] ?? $start_time, true, "", []) ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <?= c::FormDateTime("end_date", "Date de fin", "date", $formData['end_date'] ?? $end_date, true, "", []) ?>
                        <?= c::FormDateTime("end_time", "Heure de fin", "time", $formData['end_time'] ?? $end_time, true, "", []) ?>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <?= c::FormInput("capacity", "Capacité (participants)", "number", $formData['capacity'] ?? ($currentMission['capacity'] ?? ''), false, "", ["min" => "1", "error" => $formErrors['capacity'] ?? null]) ?>
                        </div>
                        <div class="w-1/2">
                            <?= c::FormInput("budget_cents", "Budget (euros)", "number", $formData['budget_cents'] ?? ($currentMission['budget_cents'] > 0 ? $currentMission['budget_cents'] / 100 : ''), false, "", ["min" => "0", "step" => "0.01", "error" => $formErrors['budget_cents'] ?? null]) ?>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <?php if ($missionUpdateSuccess): ?>
                            <?= c::Message($missionUpdateSuccess, 'success') ?>
                        <?php endif; ?>
                        <?php if ($missionUpdateError): ?>
                            <?= c::Message($missionUpdateError, 'error') ?>
                        <?php endif; ?>
                        <?php if (!empty($formErrors['_form'] ?? null)): ?>
                            <?= c::Message($formErrors['_form'], 'error') ?>
                        <?php endif; ?>
                        <?= c::Button("Enregistrer les modifications", "fage", "submit", "my-4") ?>
                        <?= c::Button("Annuler", "gray", "link", "my-4", ["href" => "/missions"]) ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl space-y-8">
            <div>
                <?= c::Heading2("Participants à la mission") ?>

                <div class="mb-4">
                    <form action="/edit_mission?id=<?= $mission_id ?>" method="post" class="flex gap-4">
                        <?php
                        $adherent_options = ['' => 'Sélectionner un adhérent'];
                        foreach ($adherentRepository->findAll(1000, 1) as $adherent) {
                            $adherent_options[$adherent->id] = htmlspecialchars($adherent->prenom) . ' ' . htmlspecialchars($adherent->nom);
                        }
                        echo c::FormSelect("adherent_id", label: "", options: $adherent_options, selected: "", class: "", attributes: ["required" => true]);
                        ?>

                        <input type="text" name="role" placeholder="Rôle (ex: bénévole, encadrant)"
                            class="px-3 py-2 border border-gray-300 rounded-md" required>

                        <input type="hidden" name="action" value="add_participant">
                        <?php if ($participantAddSuccess): ?>
                            <?= c::Message($participantAddSuccess, 'success') ?>
                        <?php endif; ?>
                        <?php if ($participantAddError): ?>
                            <?= c::Message($participantAddError, 'error') ?>
                        <?php endif; ?>
                        <?= c::Button("Ajouter participant", "green", "submit") ?>
                    </form>
                </div>

                <?php
                $participants = $missionRepository->participants($mission_id);
                ?>

                <?php if (!empty($participants)): ?>
                    <div class="scroll-container">
                        <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border-2 px-4 py-2 text-left">Nom</th>
                                    <th class="border-2 px-4 py-2 text-left">Email</th>
                                    <th class="border-2 px-4 py-2 text-left">Téléphone</th>
                                    <th class="border-2 px-4 py-2 text-left">Rôle</th>
                                    <th class="border-2 px-4 py-2 text-left">Date d'inscription</th>
                                    <th class="border-2 px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($participants as $participant): ?>
                                    <tr class="<?= ($participant['id'] ?? 0) % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                        <td class="border-2 px-4 py-2"><?= htmlspecialchars($participant['first_name']) ?> <?= htmlspecialchars($participant['last_name']) ?></td>
                                        <td class="border-2 px-4 py-2"><?= htmlspecialchars($participant['email']) ?></td>
                                        <td class="border-2 px-4 py-2"><?= htmlspecialchars($participant['phone']) ?></td>
                                        <td class="border-2 px-4 py-2"><?= htmlspecialchars($participant['role']) ?></td>
                                        <td class="border-2 px-4 py-2"><?= date('d/m/Y H:i', strtotime($participant['registered_at'])) ?></td>
                                        <td class="border-2 px-4 py-2">
                                            <form method="POST" action="/edit_mission?id=<?= $mission_id ?>" style="display: inline;">
                                                <input type="hidden" name="action" value="remove_participant">
                                                <input type="hidden" name="adherent_id" value="<?= $participant['adherent_id'] ?>">
                                                <button type="submit" class="bg-transparent border-0 underline cursor-pointer p-0 font-inherit text-red-600 hover:text-red-800" onclick="return confirm('Retirer ce participant ?')">Retirer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucun participant inscrit pour cette mission</p>
                <?php endif; ?>
                <div>
                    <?php if ($participantRemoveSuccess): ?>
                        <?= c::Message($participantRemoveSuccess, 'success') ?>
                    <?php endif; ?>
                    <?php if ($participantRemoveError): ?>
                        <?= c::Message($participantRemoveError, 'error') ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

</body>