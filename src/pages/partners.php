<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

if (HttpUtils::isPost()) {
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
                $add_partner_success = "Le partenaire \"{$_POST['partner_name']}\" a bien été ajouté";
            } else {
                $add_partner_error = "Une erreur est survenue lors de l'ajout du partenaire";
            }
        } elseif ($_POST['action'] === 'delete_partner' && isset($_POST['delete_id'])) {
            $partner = $db->get_partner_by_id($_POST['delete_id']);
            if ($partner && $db->delete_partner($_POST['delete_id'])) {
                $delete_partner_success = "Le partenaire \"{$partner['name']}\" a bien été supprimé";
            } else {
                $delete_partner_error = "Une erreur est survenue lors de la suppression du partenaire";
            }
        } elseif ($_POST['action'] === 'add_donor' && isset($_POST['donor_name'])) {
            if ($db->add_donor(
                $_POST['donor_name'],
                $_POST['contact'] ?? null,
                $_POST['email'] ?? null,
                $_POST['notes'] ?? null
            )) {
                $donator_add_success = "Le donateur \"{$_POST['donor_name']}\" a bien été ajouté";
            } else {
                $donator_add_error = "Une erreur est survenue lors de l'ajout du donateur";
            }
        } elseif ($_POST['action'] === 'add_donation' && isset($_POST['donor_id']) && isset($_POST['amount'])) {
            $amount_cents = (int)($_POST['amount'] * 100);
            if ($db->add_donation(
                $_POST['donor_id'],
                $amount_cents,
                $_POST['method'] ?? null,
                $_POST['reference'] ?? null,
                $_POST['notes'] ?? null
            )) {
                $don_success = "Le don a bien été enregistré";
            } else {
                $don_error = "Une erreur est survenue lors de l'enregistrement du don";
            }
        } elseif ($_POST['action'] === 'add_subsidy' && isset($_POST['title'])) {
            $amount_cents = (int)($_POST['amount'] * 100);
            $awarded_at = !empty($_POST['awarded_at']) ? strtotime($_POST['awarded_at']) : time();
            if ($db->add_subsidy(
                $_POST['partner_id'] ?? null,
                $_POST['title'],
                $amount_cents,
                $awarded_at,
                $_POST['conditions'] ?? null,
                $_POST['notes'] ?? null
            )) {
                $subvention_success = "La subvention \"{$_POST['title']}\" a bien été ajoutée";
            } else {
                $subvention_error = "Une erreur est survenue lors de l'ajout de la subvention";
            }
        } elseif ($_POST['action'] === 'add_contribution' && isset($_POST['adherents_id']) && isset($_POST['amount'])) {
            $amount_cents = (int)($_POST['amount'] * 100);
            if ($db->add_contribution(
                $_POST['adherents_id'],
                $amount_cents,
                $_POST['method'],
                $_POST['reference'] ?? null,
                $_POST['notes'] ?? null
            )) {
                $cotisation_success = "La cotisation a bien été enregistrée";
            } else {
                $cotisation_error = "Une erreur est survenue lors de l'enregistrement de la cotisation";
            }
        }
    }
}

$page = max($_GET["page"] ?? 1, 1);
$partners = $db->get_partners(10, $page, $_GET["filter-partner"] ?? "");
$donors = $db->get_donors(10, $page, $_GET["filter-donor"] ?? "");
$subsidies = $db->get_subsidies(10, $page, $_GET["filter-subsidy"] ?? "");
$contributions = $db->get_contributions(10, $page, $_GET["filter-adherent"] ?? "", $_GET["filter-method"] ?? "");
$expiring_contributions = $db->get_expiring_contributions(30);
$adherents = $db->get_adherents(1000, 1);
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">


        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div class="mb-4">
                <?= c::BackToLink(); ?>
            </div>
            <?= c::Heading2("Gestion des Partenaires") ?>

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
                        <?= c::Textarea("notes", "Notes", "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                    </div>



                    <div class="col-span-2">

                        <?= c::Button("Ajouter le partenaire", "fage", "submit") ?>
                        <?php
                        if (Constants::is_debug()) {
                        ?>
                            <?= c::Button("Autofill (debug)", "fage", "button", "", ["id" => "autofill-partner"]) ?>
                        <?php
                        }
                        ?>
                    </div>
                </form>

                <?php
                if (isset($add_partner_success)) {
                    echo c::Message($add_partner_success, 'success');
                }
                if (isset($add_partner_error)) {
                    echo c::Message($add_partner_error, 'error');
                }
                if (isset($delete_partner_success)) {
                    echo c::Message($delete_partner_success, 'success');
                }
                if (isset($delete_partner_error)) {
                    echo c::Message($delete_partner_error, 'error');
                }
                ?>
            </div>

            <div class="scroll-container">
                <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border-2 px-4 py-2 text-left">Nom</th>
                            <th class="border-2 px-4 py-2 text-left">Contact</th>
                            <th class="border-2 px-4 py-2 text-left">Email</th>
                            <th class="border-2 px-4 py-2 text-left">Téléphone</th>
                            <th class="border-2 px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $idx = 0;
                        foreach ($partners as $partner): ?>
                            <tr class="<?= $idx % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                <td class="border-2 px-4 py-2"><?= htmlspecialchars($partner['name']) ?></td>
                                <td class="border-2 px-4 py-2"><?= htmlspecialchars($partner['contact'] ?? '') ?></td>
                                <td class="border-2 px-4 py-2"><?= htmlspecialchars($partner['email'] ?? '') ?></td>
                                <td class="border-2 px-4 py-2"><?= htmlspecialchars($partner['phone'] ?? '') ?></td>
                                <td class="border-2 px-4 py-2">
                                    <a href="/partners?action=delete_partner&delete_id=<?= $partner['id'] ?>" class="text-red-600 underline" onclick="return confirm('Supprimer ce partenaire ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php
                            $idx++;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

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
                            <?= c::Textarea("notes", "Notes", "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                        </div>
                        <div class="col-span-2">
                            <?= c::Button("Ajouter le donateur", "fage", "submit") ?>
                            <?php
                            if (Constants::is_debug()) {
                            ?>
                                <?= c::Button("Autofill (debug)", "fage", "button", "", ["id" => "autofill-donor"]) ?>
                            <?php
                            }
                            ?>
                        </div>
                    </form>

                    <?php
                    if (isset($donator_add_success)) {
                        echo c::Message($donator_add_success, 'success');
                    }
                    if (isset($donator_add_error)) {
                        echo c::Message($donator_add_error, 'error');
                    }
                    if (isset($don_success)) {
                        echo c::Message($don_success, 'success');
                    }
                    if (isset($don_error)) {
                        echo c::Message($don_error, 'error');
                    }
                    ?>
                </div>

                <!-- Donors Table -->
                <div class="scroll-container">
                    <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border-2 px-4 py-2 text-left">Nom</th>
                                <th class="border-2 px-4 py-2 text-left">Contact</th>
                                <th class="border-2 px-4 py-2 text-left">Email</th>
                                <th class="border-2 px-4 py-2 text-left">Total donné</th>
                                <th class="border-2 px-4 py-2 text-left">Nb. donations</th>
                                <th class="border-2 px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $idx = 0;
                            foreach ($donors as $donor): ?>
                                <tr class="<?= $idx % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($donor['name']) ?></td>
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($donor['contact'] ?? '') ?></td>
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($donor['email'] ?? '') ?></td>
                                    <td class="border-2 px-4 py-2"><?= number_format(($donor['total_donated'] ?? 0) / 100, 2) ?> €</td>
                                    <td class="border-2 px-4 py-2"><?= $donor['donation_count'] ?? 0 ?></td>
                                    <td class="border-2 px-4 py-2">
                                        <!-- Add donation button could go here -->
                                        <span class="text-gray-500 text-sm">Voir donations</span>
                                    </td>
                                </tr>
                            <?php
                                $idx++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Subventions") ?>


                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Ajouter une subvention</h3>
                    <form action="/partners" method="post" class="grid grid-cols-2 gap-4">
                        <input type="hidden" name="action" value="add_subsidy">
                        <?php
                        $partner_options = ['' => ''];
                        foreach ($db->get_partners(1000, 1) as $partner) {
                            $partner_options[$partner['id']] = $partner['name'];
                        }
                        echo c::FormSelect(
                            "partner_id",
                            label: "Sélectionner un partenaire",
                            options: $partner_options,
                            selected: "",
                            class: ""
                        );
                        ?>
                        <?= c::FormInput("title", "Titre de la subvention", "text", "", true) ?>
                        <?= c::FormInput("amount", "Montant (euros)", "number", "", true, "", ["step" => "0.01", "min" => "0"]) ?>
                        <?= c::FormInput("awarded_at", "Date d'attribution", "date", date('Y-m-d'), false) ?>
                        <div class="col-span-2">
                            <?= c::Textarea("conditions", "Conditions", "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                        </div>
                        <div class="col-span-2">
                            <?= c::Textarea("notes", "Notes", "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                        </div>
                        <div class="col-span-2">
                            <?= c::Button("Ajouter la subvention", "fage", "submit") ?>
                            <?php
                            if (Constants::is_debug()) {
                            ?>
                                <?= c::Button("Autofill (debug)", "fage", "button", "", ["id" => "autofill-subsidy"]) ?>
                            <?php
                            }
                            ?>
                        </div>
                    </form>

                    <?php
                    if (isset($subvention_success)) {
                        echo c::Message($subvention_success, 'success');
                    }
                    if (isset($subvention_error)) {
                        echo c::Message($subvention_error, 'error');
                    }
                    ?>
                </div>


                <div class="scroll-container">
                    <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border-2 px-4 py-2 text-left">Titre</th>
                                <th class="border-2 px-4 py-2 text-left">Partenaire</th>
                                <th class="border-2 px-4 py-2 text-left">Montant</th>
                                <th class="border-2 px-4 py-2 text-left">Date d'attribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $idx = 0;
                            foreach ($subsidies as $subsidy): ?>
                                <tr class="<?= $idx % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($subsidy['title']) ?></td>
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($subsidy['partner_name'] ?? 'Non spécifié') ?></td>
                                    <td class="border-2 px-4 py-2"><?= number_format($subsidy['amount_cents'] / 100, 2) ?> €</td>
                                    <td class="border-2 px-4 py-2"><?= $subsidy['awarded_at'] ? date('d/m/Y', $subsidy['awarded_at']) : 'Non définie' ?></td>
                                </tr>
                            <?php
                                $idx++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Cotisations") ?>


                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Ajouter une cotisation</h3>
                    <form action="/partners" method="post" class="grid grid-cols-2 gap-4">
                        <input type="hidden" name="action" value="add_contribution">
                        <?php
                        $adherent_options = ['' => ''];
                        foreach ($adherents as $adherent) {
                            $adherent_options[$adherent->id] = htmlspecialchars($adherent->prenom) . ' ' . htmlspecialchars($adherent->nom);
                        }
                        echo c::FormSelect(
                            "adherents_id",
                            label: "Sélectionner un adhérent",
                            options: $adherent_options,
                            selected: "",
                            class: "",
                            attributes: ["required" => true, "placeholder" => "Sélectionner un adhérent"]
                        );
                        ?>
                        <?= c::FormInput("amount", "Montant (euros)", "number", "", true, "", ["step" => "0.01", "min" => "0"]) ?>
                        <?php
                        $method_options = [
                            "" => "",
                            "cash" => "Espèces",
                            "card" => "Carte bancaire",
                            "check" => "Chèque",
                            "transfer" => "Virement"
                        ];
                        echo c::FormSelect("method", label: "Sélectionner une méthode", options: $method_options, selected: "", class: "");
                        ?>
                        <?= c::FormInput("reference", "Référence", "text", "", false) ?>
                        <div class="col-span-2">
                            <?= c::Textarea("notes", "Notes", "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                        </div>
                        <div class="col-span-2">
                            <?= c::Button("Ajouter la cotisation", "fage", "submit") ?>
                            <?php
                            if (Constants::is_debug()) {
                            ?>
                                <?= c::Button("Autofill (debug)", "fage", "button", "", ["id" => "autofill-contribution"]) ?>
                            <?php
                            }
                            ?>
                        </div>
                    </form>

                    <?php
                    if (isset($cotisation_success)) {
                        echo c::Message($cotisation_success, 'success');
                    }
                    if (isset($cotisation_error)) {
                        echo c::Message($cotisation_error, 'error');
                    }
                    ?>
                </div>


                <div class="scroll-container">
                    <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border-2 px-4 py-2 text-left">Adhérent</th>
                                <th class="border-2 px-4 py-2 text-left">Montant</th>
                                <th class="border-2 px-4 py-2 text-left">Méthode</th>
                                <th class="border-2 px-4 py-2 text-left">Référence</th>
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
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($contribution['method'] ?? '') ?></td>
                                    <td class="border-2 px-4 py-2"><?= htmlspecialchars($contribution['reference'] ?? '') ?></td>
                                    <td class="border-2 px-4 py-2"><?= date('d/m/Y H:i', strtotime($contribution['paid_at'])) ?></td>
                                </tr>
                            <?php
                                $idx++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <?php
    if (Constants::is_debug()) {
    ?>
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

            document.getElementById("autofill-partner")?.addEventListener("click", () => {
                document.querySelector("[name='partner_name']").value = "Partenaire " + makeid(6);
                document.querySelector("[name='contact']").value = makeid(6);
                document.querySelector("[name='email']").value = "test@test.com";
                document.querySelector("[name='phone']").value = "0102030405";
                document.querySelector("[name='address']").value = makeid(6);
                document.querySelector("[name='website']").value = "https://example.com";
                document.querySelector("[name='notes']").value = "Notes de test";
            });

            document.getElementById("autofill-donor")?.addEventListener("click", () => {
                document.querySelector("[name='donor_name']").value = "Donateur " + makeid(6);
                document.querySelector("[name='contact']").value = makeid(6);
                document.querySelector("[name='email']").value = "test@test.com";
                document.querySelector("[name='notes']").value = "Notes de test";
            });

            document.getElementById("autofill-subsidy")?.addEventListener("click", () => {
                const select = document.querySelector("[name='partner_id']");
                if (select && select.options.length > 1) {
                    select.selectedIndex = 1;
                }
                document.querySelector("[name='title']").value = "Subvention " + makeid(6);
                document.querySelector("[name='amount']").value = "1000";
                const today = new Date();
                document.querySelector("[name='awarded_at']").value = today.toISOString().split('T')[0];
                document.querySelector("[name='conditions']").value = "Conditions de test";
                document.querySelector("[name='notes']").value = "Notes de test";
            });

            document.getElementById("autofill-contribution")?.addEventListener("click", () => {
                const select = document.querySelector("[name='adherents_id']");
                if (select && select.options.length > 1) {
                    select.selectedIndex = 1;
                }
                document.querySelector("[name='amount']").value = "20";
                const methodSelect = document.querySelector("[name='method']");
                if (methodSelect && methodSelect.options.length > 1) {
                    methodSelect.selectedIndex = 1;
                }
                document.querySelector("[name='reference']").value = makeid(6);
                document.querySelector("[name='notes']").value = "Notes de test";
            });
        </script>
    <?php
    }
    ?>

</body>