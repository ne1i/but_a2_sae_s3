<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\repositories\AdherentRepository;
use ButA2SaeS3\services\AdherentValidationService;
use ButA2SaeS3\services\FormService;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;
use ButA2SaeS3\utils\HtmlUtils;

$db = new FageDB();
$repository = new AdherentRepository($db);

HttpUtils::ensure_valid_session($db);

$id = isset($_GET["id"]) ? (int)$_GET["id"] : null;
if (!$id) {
    header("Location: /adherents_benevoles");
    exit;
}

$adherent = $repository->findById($id);
if (!$adherent) {
    header("Location: /adherents_benevoles");
    exit;
}

FormService::handleFormSubmission(
    function ($data) use ($id) {
        return AdherentValidationService::validateUpdateAdherent($data, $id);
    },
    function ($dto) use ($repository, $id) {
        $existing = $repository->findByEmail($dto->email);
        if ($existing && $existing->id !== $id) {
            throw new \Exception("Cet email est déjà utilisé par un autre adhérent");
        }
        $repository->update($dto);
    },
    "L'adhérent a bien été mis à jour",
    "/edit_adherent?id={$id}",
    "adherent_update"
);

$formState = FormService::restoreFormData("adherent_update");
$formData = $formState['data'] ?? [];
$formErrors = $formState['errors'] ?? [];
$successMessage = FormService::getSuccessMessage("adherent_update");
$errorMessage = FormService::getErrorMessage("adherent_update");
?>

<?php require_once __DIR__ . "/../templates/admin_head.php"; ?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">
    <main class="p-2">
        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink("Retour aux adhérents", "/adherents_benevoles#adherents-table"); ?>
                </div>
                <?= c::Heading2("Modifier un adhérent") ?>
                <form action="/edit_adherent?id=<?= $id ?>" method="post" class="flex flex-col bg-white">
                    <div class="flex gap-4 mb-4">
                        <?= c::FormInput("prenom", "Prénom", "text", htmlspecialchars($formData['prenom'] ?? $adherent->prenom), true, "", ["container-class" => "w-1/2", "error" => $formErrors['prenom'] ?? null]) ?>
                        <?= c::FormInput("nom", "Nom", "text", htmlspecialchars($formData['nom'] ?? $adherent->nom), true, "", ["container-class" => "w-1/2", "error" => $formErrors['nom'] ?? null]) ?>
                    </div>

                    <?= c::FormInput("adresse", "Adresse", "text", htmlspecialchars($formData['adresse'] ?? $adherent->adresse), true, "mb-4", ["error" => $formErrors['adresse'] ?? null]) ?>


                    <div class="flex gap-4 mb-4">
                        <?= c::FormInput("code_postal", "Code postal", "text", htmlspecialchars($formData['code_postal'] ?? $adherent->code_postal), true, "", ["container-class" => "w-1/2", "error" => $formErrors['code_postal'] ?? null]) ?>
                        <?= c::FormInput("ville", "Ville", "text", htmlspecialchars($formData['ville'] ?? $adherent->ville), true, "", ["container-class" => "w-1/2", "error" => $formErrors['ville'] ?? null]) ?>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <?= c::FormInput("tel", "Téléphone", "tel", htmlspecialchars($formData['tel'] ?? $adherent->tel), true, "", ["container-class" => "w-1/2", "error" => $formErrors['tel'] ?? null]) ?>
                        <?= c::FormInput("email", "Email", "email", htmlspecialchars($formData['email'] ?? $adherent->email), true, "", ["container-class" => "w-1/2", "error" => $formErrors['email'] ?? null]) ?>
                    </div>

                    <?= c::FormInput("age", "Âge", "number", htmlspecialchars($formData['age'] ?? $adherent->age), true, "", ["error" => $formErrors['age'] ?? null]) ?>
                    <?= c::FormInput("profession", "Profession", "text", htmlspecialchars($formData['profession'] ?? $adherent->profession), true, "", ["error" => $formErrors['profession'] ?? null]) ?>

                    <?php if ($successMessage): ?>
                        <?= c::Message($successMessage, 'success') ?>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <?= c::Message($errorMessage, 'error') ?>
                    <?php endif; ?>
                    <?php if (!empty($formErrors['_form'] ?? null)): ?>
                        <?= c::Message($formErrors['_form'], 'error') ?>
                    <?php endif; ?>

                    <?= c::Button("Mettre à jour l'adhérent", "fage", "submit", "my-4") ?>
                </form>
            </div>
        </div>

    </main>
</body>