<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

// Handle form submissions
if (HttpUtils::isPost()) {
    if (isset($_POST['action']) && $_POST['action'] === 'add_contribution' && isset($_POST['adherents_id']) && isset($_POST['amount'])) {
        $amount_cents = (int)($_POST['amount'] * 100); // Convert to cents
        if ($db->add_contribution(
            $_POST['adherents_id'],
            $amount_cents,
            $_POST['method'],
            $_POST['reference'] ?? null,
            $_POST['notes'] ?? null
        )) {
            $success = "La cotisation a bien été enregistrée";
        } else {
            $error = "Une erreur est survenue lors de l'enregistrement de la cotisation";
        }
    }
}

// Get data for display
$page = max($_GET["page"] ?? 1, 1);
$contributions = $db->get_contributions(20, $page, $_GET["filter-adherent"] ?? "", $_GET["filter-method"] ?? "");
$expiring_contributions = $db->get_expiring_contributions(30);
$adherents = $db->get_adherents(1000, 1); // For dropdown
$total_count = $db->get_contributions_count($_GET["filter-adherent"] ?? "", $_GET["filter-method"] ?? "");
$page_count = ceil($total_count / 20);
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">
        <!-- Alert messages -->
        <?php
        if (isset($error)) {
            echo c::Message($error, 'error');
        }
        if (isset($success)) {
            echo c::Message($success, 'success');
        }
        ?>

        <!-- Expiring Contributions Alert -->
        <?php if (!empty($expiring_contributions)): ?>
            <div class="shadow-lg bg-yellow-50 border-2 border-yellow-200 p-6 rounded-2xl">
                <?= c::Heading2("⚠️ Cotisations arrivant à échéance") ?>
                <div class="scroll-container">
                    <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border-2 px-4 py-2 text-left">Adhérent</th>
                                <th class="border-2 px-4 py-2 text-left">Dernier paiement</th>
                                <th class="border-2 px-4 py-2 text-left">Total cotisations</th>
                                <th class="border-2 px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $idx = 0;
                            foreach ($expiring_contributions as $adherent): ?>
                                <tr class="<?= $idx % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                    <td class="border-2 px-4 py-2">
                                        <?= htmlspecialchars($adherent['first_name']) ?> <?= htmlspecialchars($adherent['last_name']) ?>
                                    </td>
                                    <td class="border-2 px-4 py-2">
                                        <?= $adherent['last_payment_date'] ? date('d/m/Y', strtotime($adherent['last_payment_date'])) : 'Jamais' ?>
                                    </td>
                                    <td class="border-2 px-4 py-2"><?= $adherent['total_payments'] ?></td>
                                    <td class="border-2 px-4 py-2">
                                        <a href="/contributions?action=add_contribution&adherents_id=<?= $adherent['id'] ?>&amount=20&method=cash" class="text-green-600 underline">Enregistrer cotisation</a>
                                    </td>
                                </tr>
                            <?php
                                $idx++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Add Contribution Form -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::BackToLink(); ?>
                <?= c::Heading2("Ajouter une cotisation") ?>

                <form action="/contributions" method="post" class="grid grid-cols-2 gap-4">
                    <input type="hidden" name="action" value="add_contribution">
                    <?php
                    $adherent_options = ['' => 'Sélectionner un adhérent'];
                    foreach ($adherents as $adherent) {
                        $adherent_options[$adherent->id] = htmlspecialchars($adherent->prenom) . ' ' . htmlspecialchars($adherent->nom);
                    }
                    echo c::FormSelect("adherents_id", label: "", options: $adherent_options, selected: "", class: "", attributes: ["required" => true]);
                    ?>
                    <?= c::FormInput("amount", "Montant (euros)", "number", "", true, "", ["step" => "0.01", "min" => "0"]) ?>
                    <?php
                    $method_options = [
                        'cash' => 'Espèces',
                        'card' => 'Carte bancaire',
                        'check' => 'Chèque',
                        'transfer' => 'Virement'
                    ];
                    echo c::FormSelect("method", label: "", options: $method_options, selected: "", class: "");
                    ?>
                    <?= c::FormInput("reference", "Référence", "text", "", false) ?>
                    <div class="col-span-2">
                        <?= c::Textarea("notes", "Notes", "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                    </div>
                    <div class="col-span-2">
                        <?= c::Button("Ajouter la cotisation", "fage", "submit") ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contributions History -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Historique des cotisations") ?>

                <!-- Filters -->
                <div class="mb-4">
                    <form method="get" action="/contributions" class="flex gap-4">
                        <?= c::FormInput("filter-adherent", "Filtrer par adhérent", "text", $_GET["filter-adherent"] ?? "", false, "", ["placeholder" => "Nom ou prénom"]) ?>
                        <?php
                        $filter_method_options = [
                            '' => 'Toutes les méthodes',
                            'cash' => 'Espèces',
                            'card' => 'Carte bancaire',
                            'check' => 'Chèque',
                            'transfer' => 'Virement'
                        ];
                        $selected_method = $_GET["filter-method"] ?? "";
                        echo c::FormSelect("filter-method", label: "", options: $filter_method_options, selected: $selected_method, class: "");
                        ?>
                        <?= c::Button("Filtrer", "fage", "submit") ?>
                        <?php if (!empty($_GET["filter-adherent"]) || !empty($_GET["filter-method"])): ?>
                            <?= c::Button("Effacer les filtres", "gray", "link", "inline-block", ["href" => "/contributions"]) ?>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Contributions Table -->
                <div class="scroll-container">
                    <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border-2 px-4 py-2 text-left">Adhérent</th>
                                <th class="border-2 px-4 py-2 text-left">Montant</th>
                                <th class="border-2 px-4 py-2 text-left">Méthode</th>
                                <th class="border-2 px-4 py-2 text-left">Référence</th>
                                <th class="border-2 px-4 py-2 text-left">Notes</th>
                                <th class="border-2 px-4 py-2 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $idx = 0;
                            foreach ($contributions as $contribution): ?>
                                <tr class="<?= $idx % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($contribution['first_name']) ?> <?= htmlspecialchars($contribution['last_name']) ?></td>
                                    <td class="border-2 px-4 py-2"><?= number_format($contribution['amount_cents'] / 100, 2) ?> €</td>
                                    <td class="border-2 px-4 py-2">
                                        <?php
                                        $method_labels = [
                                            'cash' => 'Espèces',
                                            'card' => 'Carte bancaire',
                                            'check' => 'Chèque',
                                            'transfer' => 'Virement'
                                        ];
                                        echo $method_labels[$contribution['method']] ?? htmlspecialchars($contribution['method']);
                                        ?>
                                    </td>
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($contribution['reference'] ?? '') ?></td>
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($contribution['notes'] ?? '') ?></td>
                                    <td class="border-2 px-4 py-2"><?= date('d/m/Y H:i', strtotime($contribution['paid_at'])) ?></td>
                                </tr>
                            <?php
                                $idx++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center gap-4 items-center mt-4">
                    <?php
                    $current_page = max($_GET["page"] ?? 1, 1);
                    $previous = $current_page > 1;
                    $next = $current_page < $page_count;

                    $previous_page = max(1, $current_page - 1);
                    $next_page = min($page_count, $current_page + 1);
                    ?>
                    <?php if ($previous): ?>
                        <?= c::Button("Précédent", "fage", "link", "", ["href" => "/contributions?page={$previous_page}&filter-adherent=" . urlencode($_GET["filter-adherent"] ?? "") . "&filter-method=" . urlencode($_GET["filter-method"] ?? "")]) ?>
                    <?php else: ?>
                        <?= c::Button("Précédent", "gray", "button", "", ["disabled"]) ?>
                    <?php endif; ?>
                    <span class="rounded-full text-white bg-fage-700 inline-block px-3 py-1"><?= $current_page ?></span>
                    <?php if ($next): ?>
                        <?= c::Button("Suivant", "fage", "link", "", ["href" => "/contributions?page={$next_page}&filter-adherent=" . urlencode($_GET["filter-adherent"] ?? "") . "&filter-method=" . urlencode($_GET["filter-method"] ?? "")]) ?>
                    <?php else: ?>
                        <?= c::Button("Suivant", "gray", "button", "", ["disabled"]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

</body>