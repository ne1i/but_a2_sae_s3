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

// Pre-fill form values
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
                    <?= c::BackToLink(); ?>
                </div>
                <?= c::Heading2("Modifier la mission") ?>
                <form action="/edit_mission?id=<?= $mission_id ?>" method="post" class="flex flex-col bg-white">
                    <?= c::FormInput("title", "Titre de la mission", "text", htmlspecialchars($mission['title']), true, "mb-4") ?>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="4" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fage-500"><?= htmlspecialchars($mission['description']) ?></textarea>
                    </div>

                    <?= c::FormInput("location", "Lieu", "text", htmlspecialchars($mission['location']), true, "mb-4") ?>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" id="start_date" name="start_date" value="<?= $start_date ?>" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fage-500">
                        </div>
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Heure de début</label>
                            <input type="time" id="start_time" name="start_time" value="<?= $start_time ?>" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fage-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input type="date" id="end_date" name="end_date" value="<?= $end_date ?>" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fage-500">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Heure de fin</label>
                            <input type="time" id="end_time" name="end_time" value="<?= $end_time ?>" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-fage-500">
                        </div>
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
                        <select name="adherent_id" required class="px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Sélectionner un adhérent</option>
                            <?php
                            foreach ($db->get_adherents(1000, 1) as $adherent) {
                                echo "<option value=\"{$adherent->id}\">{$adherent->prenom} {$adherent->nom}</option>";
                            }
                            ?>
                        </select>

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

                if (isset($_POST['action']) && $_POST['action'] === 'remove_participant') {
                    $adherent_id = $_POST['adherent_id'] ?? null;

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
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Nom</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Téléphone</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Rôle</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Date d'inscription</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($participants as $participant): ?>
                                    <tr class="<?= ($participant['id'] ?? 0) % 2 == 0 ? 'bg-gray-50' : 'bg-white' ?>">
                                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($participant['first_name']) ?> <?= htmlspecialchars($participant['last_name']) ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($participant['email']) ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($participant['phone']) ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($participant['role']) ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?= date('d/m/Y H:i', strtotime($participant['registered_at'])) ?></td>
                                        <td class="border border-gray-300 px-4 py-2">
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