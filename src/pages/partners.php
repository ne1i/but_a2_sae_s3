<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\repositories\AdherentRepository;
use ButA2SaeS3\repositories\ContributionRepository;
use ButA2SaeS3\repositories\DonorRepository;
use ButA2SaeS3\repositories\PartnerRepository;
use ButA2SaeS3\repositories\SubsidyRepository;
use ButA2SaeS3\services\ContributionValidationService;
use ButA2SaeS3\services\FormService;
use ButA2SaeS3\services\PartnersValidationService;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();
$partnerRepository = new PartnerRepository($db->getConnection());
$donorRepository = new DonorRepository($db->getConnection());
$subsidyRepository = new SubsidyRepository($db->getConnection());
$contributionRepository = new ContributionRepository($db->getConnection());
$adherentRepository = new AdherentRepository($db);

HttpUtils::ensure_valid_session($db);

$action = $_POST['action'] ?? null;

if (HttpUtils::isPost() && $action === 'delete_partner' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $partner = $partnerRepository->findById($id);
    if ($partner && $partnerRepository->delete($id)) {
        FormService::setSuccessMessage("Le partenaire \"{$partner['name']}\" a bien été supprimé", "partner_delete");
        header("Location: /partners?success=1&success_form=partner_delete#partners-table");
    } else {
        FormService::setErrorMessage("Une erreur est survenue lors de la suppression du partenaire", "partner_delete");
        header("Location: /partners#partners-table");
    }
    exit;
}

if (HttpUtils::isPost() && $action === 'add_partner') {
    FormService::handleFormSubmission(
        [PartnersValidationService::class, 'validateAddPartner'],
        function ($dto) use ($partnerRepository) {
            $partnerRepository->add($dto);
        },
        "Le partenaire a bien été ajouté",
        "/partners#partners-table",
        "partner_add"
    );
}

if (HttpUtils::isPost() && $action === 'add_donor') {
    FormService::handleFormSubmission(
        [PartnersValidationService::class, 'validateAddDonor'],
        function ($dto) use ($donorRepository) {
            $donorRepository->add($dto);
        },
        "Le donateur a bien été ajouté",
        "/partners#donors-table",
        "donor_add"
    );
}

if (HttpUtils::isPost() && $action === 'add_subsidy') {
    FormService::handleFormSubmission(
        [PartnersValidationService::class, 'validateAddSubsidy'],
        function ($dto) use ($subsidyRepository) {
            $subsidyRepository->add($dto);
        },
        "La subvention a bien été ajoutée",
        "/partners#subsidies-table",
        "subsidy_add"
    );
}

if (HttpUtils::isPost() && $action === 'add_contribution') {
    FormService::handleFormSubmission(
        [ContributionValidationService::class, 'validateAddContribution'],
        function ($dto) use ($contributionRepository) {
            $contributionRepository->add($dto->adherents_id, $dto->amount_cents, $dto->method, $dto->reference, $dto->notes);
        },
        "La cotisation a bien été enregistrée",
        "/partners#contributions-table",
        "partner_contribution_add"
    );
}

$partnerAddState = FormService::restoreFormData("partner_add");
$partnerAddData = $partnerAddState['data'] ?? [];
$partnerAddErrors = $partnerAddState['errors'] ?? [];
$partnerAddSuccess = FormService::getSuccessMessage("partner_add");
$partnerAddError = FormService::getErrorMessage("partner_add");

$partnerDeleteSuccess = FormService::getSuccessMessage("partner_delete");
$partnerDeleteError = FormService::getErrorMessage("partner_delete");

$donorAddState = FormService::restoreFormData("donor_add");
$donorAddData = $donorAddState['data'] ?? [];
$donorAddErrors = $donorAddState['errors'] ?? [];
$donorAddSuccess = FormService::getSuccessMessage("donor_add");
$donorAddError = FormService::getErrorMessage("donor_add");

$subsidyAddState = FormService::restoreFormData("subsidy_add");
$subsidyAddData = $subsidyAddState['data'] ?? [];
$subsidyAddErrors = $subsidyAddState['errors'] ?? [];
$subsidyAddSuccess = FormService::getSuccessMessage("subsidy_add");
$subsidyAddError = FormService::getErrorMessage("subsidy_add");

$contributionAddState = FormService::restoreFormData("partner_contribution_add");
$contributionAddData = $contributionAddState['data'] ?? [];
$contributionAddErrors = $contributionAddState['errors'] ?? [];
$contributionAddSuccess = FormService::getSuccessMessage("partner_contribution_add");
$contributionAddError = FormService::getErrorMessage("partner_contribution_add");

$page = max($_GET["page"] ?? 1, 1);
$partners = $partnerRepository->list(10, $page, $_GET["filter-partner"] ?? "");
$donors = $donorRepository->list(10, $page, $_GET["filter-donor"] ?? "");
$subsidies = $subsidyRepository->list(10, $page, $_GET["filter-subsidy"] ?? "");
$contributions = $contributionRepository->list(10, $page, $_GET["filter-adherent"] ?? "", $_GET["filter-method"] ?? "");
$expiring_contributions = $contributionRepository->expiring(30);
$adherents = $adherentRepository->findAll(1000, 1);
?>

<?php require_once __DIR__ . "/../templates/admin_head.php"; ?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">

        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div class="mb-4">
                <?= c::BackToLink(); ?>
            </div>
            <?= c::Heading2("Gestion des Partenaires", id: "partners-table") ?>

            <div class=" mb-6">
                <h3 class="text-xl font-semibold mb-3">Ajouter un partenaire</h3>
                <form id="partnerForm" action="/partners" method="post" class="grid grid-cols-2 gap-4">
                    <input type="hidden" name="action" value="add_partner">
                    <?= c::FormInput("partner_name", "Nom du partenaire", "text", $partnerAddData['partner_name'] ?? "", true, "", ["error" => $partnerAddErrors['partner_name'] ?? null]) ?>
                    <?= c::FormInput("contact", "Personne à contacter", "text", $partnerAddData['contact'] ?? "", false) ?>
                    <?= c::FormInput("email", "Email", "email", $partnerAddData['email'] ?? "", false, "", ["error" => $partnerAddErrors['email'] ?? null]) ?>
                    <?= c::FormInput("phone", "Téléphone", "tel", $partnerAddData['phone'] ?? "", false) ?>
                    <div class="col-span-2">
                        <?= c::FormInput("address", "Adresse", "text", $partnerAddData['address'] ?? "", false) ?>
                    </div>
                    <?= c::FormInput("website", "Site web", "url", $partnerAddData['website'] ?? "", false, "", ["error" => $partnerAddErrors['website'] ?? null]) ?>
                    <div class="col-span-2">
                        <?= c::Textarea("notes", "Notes", $partnerAddData['notes'] ?? "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                    </div>

                    <div>
                        <?php if ($partnerAddSuccess): ?>
                            <?= c::Message($partnerAddSuccess, 'success') ?>
                        <?php endif; ?>
                        <?php if ($partnerAddError): ?>
                            <?= c::Message($partnerAddError, 'error') ?>
                        <?php endif; ?>
                        <?php if (!empty($partnerAddErrors['_form'] ?? null)): ?>
                            <?= c::Message($partnerAddErrors['_form'], 'error') ?>
                        <?php endif; ?>
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
            </div>

            <div>
                <?php if ($partnerDeleteSuccess): ?>
                    <?= c::Message($partnerDeleteSuccess, 'success') ?>
                <?php endif; ?>
                <?php if ($partnerDeleteError): ?>
                    <?= c::Message($partnerDeleteError, 'error') ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($partners)): ?>
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
                                        <form method="post" action="/partners" style="display: inline;" onsubmit="return confirm('Supprimer ce partenaire ?')">
                                            <input type="hidden" name="action" value="delete_partner">
                                            <input type="hidden" name="delete_id" value="<?= $partner['id'] ?>">
                                            <button type="submit" class="bg-transparent border-0 underline cursor-pointer p-0 font-inherit text-red-600 hover:text-red-800">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                                $idx++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 italic">Aucun partenaire trouvé</p>
            <?php endif; ?>


        </div>

        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Donateurs", id: "donors-table") ?>

                <!-- Add Donor Form -->
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Ajouter un donateur</h3>
                    <form id="donorForm" action="/partners" method="post" class="grid grid-cols-2 gap-4">
                        <input type="hidden" name="action" value="add_donor">
                        <?= c::FormInput("donor_name", "Nom du donateur", "text", $donorAddData['donor_name'] ?? "", true, "", ["error" => $donorAddErrors['donor_name'] ?? null]) ?>
                        <?= c::FormInput("contact", "Personne à contacter", "text", $donorAddData['contact'] ?? "", false) ?>
                        <?= c::FormInput("email", "Email", "email", $donorAddData['email'] ?? "", false, "", ["error" => $donorAddErrors['email'] ?? null]) ?>
                        <div class="col-span-2">
                            <?= c::Textarea("notes", "Notes", $donorAddData['notes'] ?? "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                        </div>
                        <div>
                            <?php if ($donorAddSuccess): ?>
                                <?= c::Message($donorAddSuccess, 'success') ?>
                            <?php endif; ?>
                            <?php if ($donorAddError): ?>
                                <?= c::Message($donorAddError, 'error') ?>
                            <?php endif; ?>
                            <?php if (!empty($donorAddErrors['_form'] ?? null)): ?>
                                <?= c::Message($donorAddErrors['_form'], 'error') ?>
                            <?php endif; ?>
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
                </div>

                <!-- Donors Table -->
                <?php if (!empty($donors)): ?>
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
                                            <span class="text-gray-500 text-sm">Voir donations</span>
                                        </td>
                                    </tr>
                                <?php
                                    $idx++;
                                endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucun donateur trouvé</p>
                <?php endif; ?>
            </div>
        </div>


        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Subventions", id: "subsidies-table") ?>


                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Ajouter une subvention</h3>
                    <form id="subsidyForm" action="/partners" method="post" class="grid grid-cols-2 gap-4">
                        <input type="hidden" name="action" value="add_subsidy">
                        <?php
                        $partner_options = ['' => ''];
                        foreach ($partnerRepository->listAll() as $partner) {
                            $partner_options[$partner['id']] = $partner['name'];
                        }
                        echo c::FormSelect(
                            "partner_id",
                            label: "Sélectionner un partenaire",
                            options: $partner_options,
                            selected: $subsidyAddData['partner_id'] ?? "",
                            class: ""
                        );
                        ?>
                        <?= c::FormInput("title", "Titre de la subvention", "text", $subsidyAddData['title'] ?? "", true, "", ["error" => $subsidyAddErrors['title'] ?? null]) ?>
                        <?= c::FormInput("amount", "Montant (euros)", "number", $subsidyAddData['amount'] ?? "", true, "", ["step" => "0.01", "min" => "0", "error" => $subsidyAddErrors['amount'] ?? null]) ?>
                        <?= c::FormInput("awarded_at", "Date d'attribution", "date", $subsidyAddData['awarded_at'] ?? date('Y-m-d'), false, "", ["error" => $subsidyAddErrors['awarded_at'] ?? null]) ?>
                        <div class="col-span-2">
                            <?= c::Textarea("conditions", "Conditions", $subsidyAddData['conditions'] ?? "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                        </div>
                        <div class="col-span-2">
                            <?= c::Textarea("notes", "Notes", $subsidyAddData['notes'] ?? "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                        </div>
                        <div>
                            <?php if ($subsidyAddSuccess): ?>
                                <?= c::Message($subsidyAddSuccess, 'success') ?>
                            <?php endif; ?>
                            <?php if ($subsidyAddError): ?>
                                <?= c::Message($subsidyAddError, 'error') ?>
                            <?php endif; ?>
                            <?php if (!empty($subsidyAddErrors['_form'] ?? null)): ?>
                                <?= c::Message($subsidyAddErrors['_form'], 'error') ?>
                            <?php endif; ?>
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
                </div>


                <?php if (!empty($subsidies)): ?>
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
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucune subvention trouvée</p>
                <?php endif; ?>
            </div>
        </div>


        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Cotisations", id: "contributions-table") ?>


                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Ajouter une cotisation</h3>
                    <form id="contributionForm" action="/partners" method="post" class="grid grid-cols-2 gap-4">
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
                            selected: $contributionAddData['adherents_id'] ?? "",
                            class: "",
                            attributes: ["required" => true, "placeholder" => "Sélectionner un adhérent", "error" => $contributionAddErrors['adherents_id'] ?? null]
                        );
                        ?>
                        <?= c::FormInput("amount", "Montant (euros)", "number", $contributionAddData['amount'] ?? "", true, "", ["step" => "0.01", "min" => "0", "error" => $contributionAddErrors['amount'] ?? null]) ?>
                        <?php
                        $method_options = [
                            "" => "",
                            "cash" => "Espèces",
                            "card" => "Carte bancaire",
                            "check" => "Chèque",
                            "transfer" => "Virement"
                        ];
                        echo c::FormSelect("method", label: "Sélectionner une méthode", options: $method_options, selected: $contributionAddData['method'] ?? "", class: "", attributes: ["error" => $contributionAddErrors['method'] ?? null]);
                        ?>
                        <?= c::FormInput("reference", "Référence", "text", $contributionAddData['reference'] ?? "", false) ?>
                        <div class="col-span-2">
                            <?= c::Textarea("notes", "Notes", $contributionAddData['notes'] ?? "", false, "", ["rows" => "2", "container-class" => ""]) ?>
                        </div>
                        <div>
                            <?php if ($contributionAddSuccess): ?>
                                <?= c::Message($contributionAddSuccess, 'success') ?>
                            <?php endif; ?>
                            <?php if ($contributionAddError): ?>
                                <?= c::Message($contributionAddError, 'error') ?>
                            <?php endif; ?>
                            <?php if (!empty($contributionAddErrors['_form'] ?? null)): ?>
                                <?= c::Message($contributionAddErrors['_form'], 'error') ?>
                            <?php endif; ?>
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
                </div>


                <?php if (!empty($contributions)): ?>
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
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucune cotisation trouvée</p>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <?php
    if (Constants::is_debug()) {
    ?>
        <script>
            document.getElementById("autofill-partner")?.addEventListener("click", () => {
                const form = document.getElementById("partnerForm");
                const partnerName = form?.querySelector("[name='partner_name']");
                if (partnerName) partnerName.value = randomPartnerName();
                const contact = form?.querySelector("[name='contact']");
                if (contact) contact.value = randomPrenom() + " " + randomNom();
                const email = form?.querySelector("[name='email']");
                if (email) email.value = randomEmail();
                const phone = form?.querySelector("[name='phone']");
                if (phone) phone.value = randomTel();
                const address = form?.querySelector("[name='address']");
                if (address) address.value = randomAddress();
                const website = form?.querySelector("[name='website']");
                if (website) website.value = "https://example.com";
                const notes = form?.querySelector("[name='notes']");
                if (notes) notes.value = "Notes de test";
            });

            document.getElementById("autofill-donor")?.addEventListener("click", () => {
                const form = document.getElementById("donorForm");
                const donorName = form?.querySelector("[name='donor_name']");
                if (donorName) donorName.value = randomDonorName();
                const contact = form?.querySelector("[name='contact']");
                if (contact) contact.value = randomPrenom() + " " + randomNom();
                const email = form?.querySelector("[name='email']");
                if (email) email.value = randomEmail();
                const notes = form?.querySelector("[name='notes']");
                if (notes) notes.value = "Notes de test";
            });

            document.getElementById("autofill-subsidy")?.addEventListener("click", () => {
                const form = document.getElementById("subsidyForm");
                const select = form?.querySelector("[name='partner_id']");
                if (select && select.options.length > 1) {
                    select.selectedIndex = between(1, select.options.length - 1);
                }
                const title = form?.querySelector("[name='title']");
                if (title) title.value = randomSubsidyTitle();
                const amount = form?.querySelector("[name='amount']");
                if (amount) amount.value = String(between(100, 10000));
                const today = new Date();
                const awardedAt = form?.querySelector("[name='awarded_at']");
                if (awardedAt) awardedAt.value = today.toISOString().split('T')[0];
                const conditions = form?.querySelector("[name='conditions']");
                if (conditions) conditions.value = "Conditions de test";
                const notes = form?.querySelector("[name='notes']");
                if (notes) notes.value = "Notes de test";
            });

            document.getElementById("autofill-contribution")?.addEventListener("click", () => {
                const form = document.getElementById("contributionForm");
                const select = form?.querySelector("[name='adherents_id']");
                if (select && select.options.length > 1) {
                    select.selectedIndex = between(1, select.options.length - 1);
                }
                const amount = form?.querySelector("[name='amount']");
                if (amount) amount.value = String(between(20, 100));
                const methodSelect = form?.querySelector("[name='method']");
                if (methodSelect && methodSelect.options.length > 1) {
                    methodSelect.selectedIndex = between(1, methodSelect.options.length - 1);
                }
                const reference = form?.querySelector("[name='reference']");
                if (reference) reference.value = randomReference();
                const notes = form?.querySelector("[name='notes']");
                if (notes) notes.value = "Notes de test";
            });
        </script>
    <?php
    }
    ?>

</body>