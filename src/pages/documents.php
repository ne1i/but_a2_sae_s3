<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\repositories\DocumentRepository;
use ButA2SaeS3\services\DocumentValidationService;
use ButA2SaeS3\services\FormService;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();
$documentRepository = new DocumentRepository($db->getConnection());

HttpUtils::ensure_valid_session($db);

if (HttpUtils::isPost() && isset($_POST['action']) && $_POST['action'] === 'delete_document' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $document = $documentRepository->findById($id);
    if ($document && $documentRepository->delete($id)) {
        $_SESSION['success_message'] = "Le document \"{$document['original_name']}\" a bien été supprimé";
        header("Location: /documents?success=1#documents-list");
    } else {
        $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression du document";
        header("Location: /documents#documents-list");
    }
    exit;
}

FormService::handleFormSubmission(
    [DocumentValidationService::class, 'validateUpload'],
    function ($dto) use ($db, $documentRepository) {
        if (!isset($_FILES['document_file'])) {
            throw new \Exception("Fichier manquant");
        }

        $upload_dir = __DIR__ . "/../public/assets/documents/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file = $_FILES['document_file'];
        $filename = time() . '_' . basename($file['name']);
        $target_path = $upload_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            throw new \Exception("Erreur lors du téléchargement du fichier");
        }

        $user_id = HttpUtils::get_current_user_id($db);
        if (!$user_id) {
            throw new \Exception("Utilisateur introuvable");
        }

        $ok = $documentRepository->upload(
            $filename,
            $file['name'],
            $file['type'],
            (int)$file['size'],
            $user_id,
            $target_path,
            $dto->description !== '' ? $dto->description : null
        );

        if (!$ok) {
            throw new \Exception("Erreur lors de l'enregistrement du document");
        }
    },
    "Le document a bien été téléchargé",
    "/documents#documents-upload"
);

$formState = FormService::restoreFormData();
$formData = $formState['data'] ?? [];
$formErrors = $formState['errors'] ?? [];
$successMessage = FormService::getSuccessMessage();
$errorMessage = FormService::getErrorMessage();


$page = max($_GET["page"] ?? 1, 1);
$filterFilename = $_GET["filter-filename"] ?? "";
$documents = $documentRepository->list(20, $page, $filterFilename);
$total_count = $documentRepository->count($filterFilename);
$page_count = ceil($total_count / 20);
?>

<?php require_once __DIR__ . "/../templates/admin_head.php"; ?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">

        <?php if ($successMessage): ?>
            <?= c::Message($successMessage, 'success') ?>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <?= c::Message($errorMessage, 'error') ?>
        <?php endif; ?>


        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink(); ?>
                </div>
                <?= c::Heading2("Télécharger un document", id: "documents-upload") ?>

                <form action="/documents" method="post" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="upload_document">

                    <div>
                        <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">Fichier</label>
                        <input type="file" id="document_file" name="document_file" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <?= c::Textarea("description", "Description", $formData['description'] ?? "", false, "", ["rows" => "3", "placeholder" => "Description du document (optionnel)"]) ?>
                    </div>

                    <?= c::Button("Télécharger le document", "fage", "submit") ?>
                </form>
            </div>
        </div>


        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <?= c::Heading2("Documents existants", id: "documents-list") ?>


                <div class="mb-4">
                    <form method="get" action="/documents" class="flex gap-4">
                        <?= c::FormInput("filter-filename", "Filtrer par nom", "text", $_GET["filter-filename"] ?? "", false, "", ["placeholder" => "Nom du fichier"]) ?>
                        <?= c::Button("Filtrer", "fage", "submit") ?>
                        <?php if (!empty($_GET["filter-filename"])): ?>
                            <?= c::Button("Effacer les filtres", "gray", "link", "inline-block", ["href" => "/documents"]) ?>
                        <?php endif; ?>
                    </form>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $document): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <?php if (strpos($document['mime_type'], 'image/') === 0): ?>
                                    <img src="/assets/documents/<?= htmlspecialchars($document['filename']) ?>"
                                        alt="<?= htmlspecialchars($document['original_name']) ?>"
                                        class="w-full h-32 object-cover rounded mb-2">
                                <?php else: ?>
                                    <div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center">
                                        <span class="text-gray-500 text-sm">📄</span>
                                    </div>
                                <?php endif; ?>

                                <h4 class="font-medium text-sm truncate mb-1" title="<?= htmlspecialchars($document['original_name']) ?>">
                                    <?= htmlspecialchars($document['original_name']) ?>
                                </h4>
                                <p class="text-gray-500 text-xs mb-2">
                                    <?= number_format($document['size_bytes'] / 1024, 2) ?> KB
                                </p>

                                <?php if (!empty($document['description'])): ?>
                                    <p class="text-gray-700 text-xs mb-2"><?= htmlspecialchars($document['description']) ?></p>
                                <?php endif; ?>

                                <p class="text-gray-500 text-xs mb-2">
                                    Par <?= htmlspecialchars($document['uploader_username'] ?? 'Inconnu') ?>
                                    le <?= date('d/m/Y', strtotime($document['uploaded_at'])) ?>
                                </p>

                                <div class="flex gap-2">
                                    <a href="/assets/documents/<?= htmlspecialchars($document['filename']) ?>" target="_blank" class="flex-1 text-center bg-blue-500 hover:bg-blue-700 text-white rounded px-2 py-1 text-sm">Voir</a>
                                    <form method="post" action="/documents" style="display: inline;" onsubmit="return confirm('Supprimer ce document ?')">
                                        <input type="hidden" name="action" value="delete_document">
                                        <input type="hidden" name="delete_id" value="<?= $document['id'] ?>">
                                        <button type="submit" class="bg-transparent border-0 underline cursor-pointer p-0 font-inherit text-red-600 hover:text-red-800">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 italic">Aucun document trouvé</p>
                    <?php endif; ?>
                </div>


                <div class="flex justify-center gap-4 items-center mt-6">
                    <?php
                    $current_page = max($_GET["page"] ?? 1, 1);
                    $previous = $current_page > 1;
                    $next = $current_page < $page_count;

                    $previous_page = max(1, $current_page - 1);
                    $next_page = min($page_count, $current_page + 1);
                    ?>
                    <?php if ($previous): ?>
                        <?= c::Button("Précédent", "fage", "link", "", ["href" => "/documents?page={$previous_page}&filter-filename=" . urlencode($_GET["filter-filename"] ?? "")]) ?>
                    <?php else: ?>
                        <?= c::Button("Précédent", "gray", "button", "", ["disabled"]) ?>
                    <?php endif; ?>
                    <span class="rounded-full text-white bg-fage-700 inline-block px-3 py-1"><?= $current_page ?></span>
                    <?php if ($next): ?>
                        <?= c::Button("Suivant", "fage", "link", "", ["href" => "/documents?page={$next_page}&filter-filename=" . urlencode($_GET["filter-filename"] ?? "")]) ?>
                    <?php else: ?>
                        <?= c::Button("Suivant", "gray", "button", "", ["disabled"]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

</body>