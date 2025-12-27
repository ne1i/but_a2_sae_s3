<?php

use ButA2SaeS3\dto\AddMissionDto;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\validation\Validators;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

$mission_id = $_GET['id'] ?? null;

if (!$mission_id) {
    header('Location: /missions');
    exit;
}

$mission = $db->get_mission_by_id($mission_id);

if (!$mission) {
    header('Location: /missions');
    exit;
}

if (HttpUtils::isPost()) {
    $result = Validators::validate_add_mission($_POST);

    if ($result->isValid()) {
        /** @var AddMissionDto $updated_mission */
        $updated_mission = $result->value();

        if ($db->update_mission(
            $mission_id,
            $updated_mission->title,
            $updated_mission->description,
            $updated_mission->location,
            $updated_mission->start_at,
            $updated_mission->end_at,
            $updated_mission->capacity,
            $updated_mission->budget_cents
        )) {
            $success = "La mission \"{$updated_mission->title}\" a bien été modifiée";
        } else {
            $error = "Une erreur est survenue lors de la modification";
        }
    }
}


$start_date = date('Y-m-d', $mission['start_at']);
$start_time = date('H:i', $mission['start_at']);
$end_date = date('Y-m-d', $mission['end_at']);
$end_time = date('H:i', $mission['end_at']);
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink("Retour aux missions", "/missions"); ?>
                </div>
                <?= c::Heading2("Modifier la mission") ?>
                <form action="/edit_mission?id=<?= $mission_id ?>" method="post" class="flex flex-col bg-white">
                    <?= c::FormInput("title", "Titre de la mission", "text", htmlspecialchars($mission['title']), true, "mb-4") ?>

                    <div class="mb-4">
                        <?= c::Textarea("description", "Description", htmlspecialchars($mission['description']), true, "", ["rows" => "4"]) ?>
                    </div>

                    <?= c::FormInput("location", "Lieu", "text", htmlspecialchars($mission['location']), true, "mb-4") ?>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <?= c::FormDateTime("start_date", "Date de début", "date", $start_date, true, "", []) ?>
                        <?= c::FormDateTime("start_time", "Heure de début", "time", $start_time, true, "", []) ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <?= c::FormDateTime("end_date", "Date de fin", "date", $end_date, true, "", []) ?>
                        <?= c::FormDateTime("end_time", "Heure de fin", "time", $end_time, true, "", []) ?>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <?= c::FormInput("capacity", "Capacité (participants)", "number", $mission['capacity'] ?? '', false, "", ["min" => "1"]) ?>
                        </div>
                        <div class="w-1/2">
                            <?= c::FormInput("budget_cents", "Budget (euros)", "number", $mission['budget_cents'] > 0 ? $mission['budget_cents'] / 100 : '', false, "", ["min" => "0", "step" => "0.01"]) ?>
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

                    <div class="flex gap-4">
                        <?= c::Button("Enregistrer les modifications", "fage", "submit", "my-4") ?>
                        <?= c::Button("Annuler", "gray", "link", "my-4", ["href" => "/missions"]) ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl space-y-8">
            <div>
                <?= c::Heading2("Participants à la mission") ?>

                <div class="mb-4">
                    <form action="/edit_mission?id=<?= $mission_id ?>" method="post" class="flex gap-4">
                        <?php
                        $adherent_options = ['' => 'Sélectionner un adhérent'];
                        foreach ($db->get_adherents(1000, 1) as $adherent) {
                            $adherent_options[$adherent->id] = htmlspecialchars($adherent->prenom) . ' ' . htmlspecialchars($adherent->nom);
                        }
                        echo c::FormSelect("adherent_id", label: "", options: $adherent_options, selected: "", class: "", attributes: ["required" => true]);
                        ?>

                        <input type="text" name="role" placeholder="Rôle (ex: bénévole, encadrant)"
                            class="px-3 py-2 border border-gray-300 rounded-md" required>

                        <?= c::Button("Ajouter participant", "green", "submit", "", ["name" => "action", "value" => "add_participant"]) ?>
                    </form>
                </div>

                <?php
                if (isset($_POST['action']) && $_POST['action'] === 'add_participant') {
                    $adherent_id = $_POST['adherent_id'] ?? null;
                    $role = $_POST['role'] ?? null;

                    if ($adherent_id && $role) {
                        if ($db->add_mission_participant($mission_id, $adherent_id, $role)) {
                            echo c::Message("Participant ajouté avec succès", 'success');
                        } else {
                            echo c::Message("Erreur lors de l'ajout du participant", 'error');
                        }
                    }
                }

                if (isset($_GET['action']) && $_GET['action'] === 'remove_participant') {
                    $adherent_id = $_GET['adherent_id'] ?? null;

                    if ($adherent_id) {
                        if ($db->remove_mission_participant($mission_id, $adherent_id)) {
                            echo c::Message("Participant retiré avec succès", 'success');
                        } else {
                            echo c::Message("Erreur lors du retrait du participant", 'error');
                        }
                    }
                }

                $participants = $db->get_mission_participants($mission_id);
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
                                            <a href="/edit_mission?id=<?= $mission_id ?>&action=remove_participant&adherent_id=<?= $participant['adherent_id'] ?>" class="text-red-600 underline" onclick="return confirm('Retirer ce participant ?')">Retirer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucun participant inscrit pour cette mission</p>
                <?php endif; ?>
            </div>
        </div>

    </main>

</body>