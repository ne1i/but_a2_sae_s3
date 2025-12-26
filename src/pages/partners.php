<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

// Handle form submissions
if (HttpUtils::isPost()) {
    // Handle partner operations
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_partner' && isset($_POST['partner_name'])) {
            if ($db->add_partner(
                $_POST['partner_name'],
                $_POST['contact'] ?? null,
                $_POST['email'] ?? null,
                $_POST['phone'] ?? null,
                $_POST['address'] ?? null,
                $_POST['website'] ?? null,
                $_POST['notes'] ?? null
            )) {
                $success = "Le partenaire \"{$_POST['partner_name']}\" a bien été ajouté";
            } else {
                $error = "Une erreur est survenue lors de l'ajout du partenaire";
            }
        } elseif ($_POST['action'] === 'delete_partner' && isset($_POST['delete_id'])) {
            $partner = $db->get_partner_by_id($_POST['delete_id']);
            if ($partner && $db->delete_partner($_POST['delete_id'])) {
                $success = "Le partenaire \"{$partner['name']}\" a bien été supprimé";
            } else {
                $error = "Une erreur est survenue lors de la suppression du partenaire";
            }
        } elseif ($_POST['action'] === 'add_donor' && isset($_POST['donor_name'])) {
            if ($db->add_donor(
                $_POST['donor_name'],
                $_POST['contact'] ?? null,
                $_POST['email'] ?? null,
                $_POST['notes'] ?? null
            )) {
                $success = "Le donateur \"{$_POST['donor_name']}\" a bien été ajouté";
            } else {
                $error = "Une erreur est survenue lors de l'ajout du donateur";
            }
        } elseif ($_POST['action'] === 'add_donation' && isset($_POST['donor_id']) && isset($_POST['amount'])) {
            $amount_cents = (int)($_POST['amount'] * 100); // Convert to cents
            if ($db->add_donation(
                $_POST['donor_id'],
                $amount_cents,
                $_POST['method'] ?? null,
                $_POST['reference'] ?? null,
                $_POST['notes'] ?? null
            )) {
                $success = "Le don a bien été enregistré";
            } else {
                $error = "Une erreur est survenue lors de l'enregistrement du don";
            }
        } elseif ($_POST['action'] === 'add_subsidy' && isset($_POST['title'])) {
            $amount_cents = (int)($_POST['amount'] * 100); // Convert to cents
            $awarded_at = !empty($_POST['awarded_at']) ? strtotime($_POST['awarded_at']) : time();
            if ($db->add_subsidy(
                $_POST['partner_id'] ?? null,
                $_POST['title'],
                $amount_cents,
                $awarded_at,
                $_POST['conditions'] ?? null,
                $_POST['notes'] ?? null
            )) {
                $success = "La subvention \"{$_POST['title']}\" a bien été ajoutée";
            } else {
                $error = "Une erreur est survenue lors de l'ajout de la subvention";
            }
        } elseif ($_POST['action'] === 'add_contribution' && isset($_POST['adherents_id']) && isset($_POST['amount'])) {
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
}

// Get data for display
$page = max($_GET["page"] ?? 1, 1);
$partners = $db->get_partners(10, $page, $_GET["filter-partner"] ?? "");
$donors = $db->get_donors(10, $page, $_GET["filter-donor"] ?? "");
$subsidies = $db->get_subsidies(10, $page, $_GET["filter-subsidy"] ?? "");
$contributions = $db->get_contributions(10, $page, $_GET["filter-adherent"] ?? "", $_GET["filter-method"] ?? "");
$expiring_contributions = $db->get_expiring_contributions(30);
$adherents = $db->get_adherents(1000, 1); // For dropdown
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

        <!-- Partners Management -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div class="mb-4">
                <?= c::BackToLink(); ?>
            </div>
            <?= c::Heading2("Gestion des Partenaires") ?>

            <!-- Add Partner Form -->
            <div class=" mb-6">
                <h3 class="text-xl font-semibold mb-3">Ajouter un partenaire</h3>
                <form action="/partners" method="post" class="grid grid-cols-2 gap-4">
                    <input type="hidden" name="action" value="add_partner">
                    <?= c::FormInput("partner_name", "Nom du partenaire", "text", "", true) ?>
                    <?= c::FormInput("contact", "Personne à contacter", "text", "", false) ?>
                    <?= c::FormInput("email", "Email", "email", "", false) ?>
                    <?= c::FormInput("phone", "Téléphone", "tel", "", false) ?>
                    <div class="col-span-2">
                        <?= c::FormInput("address", "Adresse", "text", "", false) ?>
                    </div>
                    <?= c::FormInput("website", "Site web", "url", "", false) ?>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <div class="col-span-2">
                        <?= c::Button("Ajouter le partenaire", "fage", "submit") ?>
                    </div>
                </form>
            </div>

            <!-- Partners Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left">Nom</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Contact</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Téléphone</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partners as $partner): ?>
                            <tr class="bg-white hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($partner['name']) ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($partner['contact'] ?? '') ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($partner['email'] ?? '') ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($partner['phone'] ?? '') ?></td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <a href="/partners?action=delete_partner&delete_id=<?= $partner['id'] ?>" class="text-red-600 underline" onclick="return confirm('Supprimer ce partenaire ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Donors Management -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Donateurs") ?>

                <!-- Add Donor Form -->
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Ajouter un donateur</h3>
                    <form action="/partners" method="post" class="grid grid-cols-2 gap-4">
                        <input type="hidden" name="action" value="add_donor">
                        <?= c::FormInput("donor_name", "Nom du donateur", "text", "", true) ?>
                        <?= c::FormInput("contact", "Personne à contacter", "text", "", false) ?>
                        <?= c::FormInput("email", "Email", "email", "", false) ?>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                        </div>
                        <div class="col-span-2">
                            <?= c::Button("Ajouter le donateur", "fage", "submit") ?>
                        </div>
                    </form>
                </div>

                <!-- Donors Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left">Nom</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Contact</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Total donné</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Nb. donations</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donors as $donor): ?>
                                <tr class="bg-white hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($donor['name']) ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($donor['contact'] ?? '') ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($donor['email'] ?? '') ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= number_format(($donor['total_donated'] ?? 0) / 100, 2) ?> €</td>
                                    <td class="border border-gray-300 px-4 py-2"><?= $donor['donation_count'] ?? 0 ?></td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <!-- Add donation button could go here -->
                                        <span class="text-gray-500 text-sm">Voir donations</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Subsidies Management -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Subventions") ?>

                <!-- Add Subsidy Form -->
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Ajouter une subvention</h3>
                    <form action="/partners" method="post" class="grid grid-cols-2 gap-4">
                        <input type="hidden" name="action" value="add_subsidy">
                        <select name="partner_id" class="px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Sélectionner un partenaire</option>
                            <?php foreach ($db->get_partners(1000, 1) as $partner): ?>
                                <option value="<?= $partner['id'] ?>"><?= htmlspecialchars($partner['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= c::FormInput("title", "Titre de la subvention", "text", "", true) ?>
                        <?= c::FormInput("amount", "Montant (euros)", "number", "", true, "", ["step" => "0.01", "min" => "0"]) ?>
                        <?= c::FormInput("awarded_at", "Date d'attribution", "date", date('Y-m-d'), false) ?>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Conditions</label>
                            <textarea name="conditions" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                        </div>
                        <div class="col-span-2">
                            <?= c::Button("Ajouter la subvention", "fage", "submit") ?>
                        </div>
                    </form>
                </div>

                <!-- Subsidies Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left">Titre</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Partenaire</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Montant</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Date d'attribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subsidies as $subsidy): ?>
                                <tr class="bg-white hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($subsidy['title']) ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($subsidy['partner_name'] ?? 'Non spécifié') ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= number_format($subsidy['amount_cents'] / 100, 2) ?> €</td>
                                    <td class="border border-gray-300 px-4 py-2"><?= $subsidy['awarded_at'] ? date('d/m/Y', $subsidy['awarded_at']) : 'Non définie' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Contributions Management -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Cotisations") ?>

                <!-- Add Contribution Form -->
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Ajouter une cotisation</h3>
                    <form action="/partners" method="post" class="grid grid-cols-2 gap-4">
                        <input type="hidden" name="action" value="add_contribution">
                        <select name="adherents_id" required class="px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Sélectionner un adhérent</option>
                            <?php foreach ($adherents as $adherent): ?>
                                <option value="<?= $adherent->id ?>"><?= htmlspecialchars($adherent->prenom) ?> <?= htmlspecialchars($adherent->nom) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= c::FormInput("amount", "Montant (euros)", "number", "", true, "", ["step" => "0.01", "min" => "0"]) ?>
                        <select name="method" class="px-3 py-2 border border-gray-300 rounded-md">
                            <option value="cash">Espèces</option>
                            <option value="card">Carte bancaire</option>
                            <option value="check">Chèque</option>
                            <option value="transfer">Virement</option>
                        </select>
                        <?= c::FormInput("reference", "Référence", "text", "", false) ?>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                        </div>
                        <div class="col-span-2">
                            <?= c::Button("Ajouter la cotisation", "fage", "submit") ?>
                        </div>
                    </form>
                </div>

                <!-- Contributions Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left">Adhérent</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Montant</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Méthode</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Référence</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contributions as $contribution): ?>
                                <tr class="bg-white hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($contribution['first_name']) ?> <?= htmlspecialchars($contribution['last_name']) ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= number_format($contribution['amount_cents'] / 100, 2) ?> €</td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($contribution['method'] ?? '') ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($contribution['reference'] ?? '') ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= date('d/m/Y H:i', strtotime($contribution['paid_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

</body>