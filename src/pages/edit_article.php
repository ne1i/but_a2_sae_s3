<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\repositories\ArticleMediaRepository;
use ButA2SaeS3\repositories\ArticleRepository;
use ButA2SaeS3\repositories\DocumentRepository;
use ButA2SaeS3\services\ArticleValidationService;
use ButA2SaeS3\services\FormService;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();
$articleRepository = new ArticleRepository($db->getConnection());
$documentRepository = new DocumentRepository($db->getConnection());
$articleMediaRepository = new ArticleMediaRepository($db->getConnection());

HttpUtils::ensure_valid_session($db);

$article_id = $_GET['id'] ?? null;

if (!$article_id) {
    header('Location: /articles');
    exit;
}

$article = $articleRepository->findById((int)$article_id);

if (!$article) {
    header('Location: /articles');
    exit;
}

$action = $_POST['action'] ?? null;

if (HttpUtils::isPost() && $action === 'remove_media' && isset($_POST['document_id'])) {
    $docId = (int)$_POST['document_id'];
    if ($articleMediaRepository->detach((int)$article_id, $docId)) {
        FormService::setSuccessMessage("Le média a bien été retiré de l'article", "article_media_remove");
    } else {
        FormService::setErrorMessage("Erreur lors du retrait du média", "article_media_remove");
    }
    header("Location: /edit_article?id=" . urlencode((string)$article_id) . "&success=1&success_form=article_media_remove");
    exit;
}

if (HttpUtils::isPost() && $action === 'update_article') {
    FormService::handleFormSubmission(
        function (array $data) use ($article_id) {
            return ArticleValidationService::validateUpdateArticle($data, (int)$article_id);
        },
        function ($dto) use ($articleRepository) {
            $articleRepository->update($dto);
        },
        "L'article a bien été mis à jour",
        "/edit_article?id=" . urlencode((string)$article_id),
        "article_update"
    );
}

if (HttpUtils::isPost() && $action === 'upload_media') {
    FormService::handleFormSubmission(
        [\ButA2SaeS3\services\DocumentValidationService::class, 'validateUpload'],
        function ($dto) use ($db, $documentRepository, $articleMediaRepository, $article_id) {
            if (!isset($_FILES['media_file'])) {
                throw new \Exception("Fichier manquant");
            }

            $upload_dir = __DIR__ . "/../public/assets/uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file = $_FILES['media_file'];
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
                throw new \Exception("Erreur lors de l'enregistrement du média");
            }

            $document_id = $documentRepository->lastInsertId();
            if (!$articleMediaRepository->attach((int)$article_id, $document_id)) {
                throw new \Exception("Erreur lors de l'attachement du média à l'article");
            }
        },
        "Le média a bien été ajouté à l'article",
        "/edit_article?id=" . urlencode((string)$article_id),
        "article_media_upload"
    );
}

$articleUpdateState = FormService::restoreFormData("article_update");
$articleUpdateData = $articleUpdateState['data'] ?? [];
$articleUpdateErrors = $articleUpdateState['errors'] ?? [];
$articleUpdateSuccess = FormService::getSuccessMessage("article_update");
$articleUpdateError = FormService::getErrorMessage("article_update");

$mediaUploadState = FormService::restoreFormData("article_media_upload");
$mediaUploadData = $mediaUploadState['data'] ?? [];
$mediaUploadErrors = $mediaUploadState['errors'] ?? [];
$mediaUploadSuccess = FormService::getSuccessMessage("article_media_upload");
$mediaUploadError = FormService::getErrorMessage("article_media_upload");

$mediaRemoveSuccess = FormService::getSuccessMessage("article_media_remove");
$mediaRemoveError = FormService::getErrorMessage("article_media_remove");

$article = $articleRepository->findById((int)$article_id);
$article_media = $articleMediaRepository->listForArticle((int)$article_id);
?>

<?php require_once __DIR__ . "/../templates/admin_head.php"; ?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">



        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink("Retour aux articles", "/articles"); ?>
                </div>
                <?= c::Heading2("Modifier l'article") ?>

                <form action="/edit_article?id=<?= $article_id ?>" method="post" class="space-y-4">
                    <input type="hidden" name="action" value="update_article">

                    <?= c::FormInput("title", "Titre de l'article", "text", $articleUpdateData['title'] ?? $article['title'], true, "", ["error" => $articleUpdateErrors['title'] ?? null]) ?>

                    <div>
                        <?= c::Textarea("content", "Contenu de l'article", $articleUpdateData['content'] ?? $article['content'], true, "", ["rows" => "12", "error" => $articleUpdateErrors['content'] ?? null]) ?>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="status" value="draft" <?= ($articleUpdateData['status'] ?? $article['status']) === 'draft' ? 'checked' : '' ?> class="text-fage-600">
                            <span>Brouillon</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="status" value="published" <?= ($articleUpdateData['status'] ?? $article['status']) === 'published' ? 'checked' : '' ?> class="text-fage-600">
                            <span>Publié</span>
                        </label>
                    </div>
                    <?php if ($articleUpdateSuccess): ?>
                        <?= c::Message($articleUpdateSuccess, 'success') ?>
                    <?php endif; ?>
                    <?php if ($articleUpdateError): ?>
                        <?= c::Message($articleUpdateError, 'error') ?>
                    <?php endif; ?>
                    <?php if (!empty($articleUpdateErrors['_form'] ?? null)): ?>
                        <?= c::Message($articleUpdateErrors['_form'], 'error') ?>
                    <?php endif; ?>
                    <div class="flex mt-2 gap-4">
                        <?= c::Button("Enregistrer les modifications", "fage", "submit") ?>
                        <?= c::Button("Retour à la liste", "gray", "link", "", ["href" => "/articles"]) ?>
                        </a>
                        <?= c::Button("Voir sur le site", "blue", "button", "", ["onclick" => "window.open('/blog', '_blank')"]) ?>
                    </div>
                </form>
            </div>
        </div>


        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <?= c::Heading2("Médias associés") ?>


                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Ajouter un média</h3>
                    <form action="/edit_article?id=<?= $article_id ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="action" value="upload_media">

                        <div>
                            <label for="media_file" class="block text-sm font-medium text-gray-700 mb-1">Fichier</label>
                            <input type="file" id="media_file" name="media_file" required
                                accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>

                        <?= c::FormInput("description", "Description du média", "text", $mediaUploadData['description'] ?? "", false) ?>

                        <?php if ($mediaUploadSuccess): ?>
                            <?= c::Message($mediaUploadSuccess, 'success') ?>
                        <?php endif; ?>
                        <?php if ($mediaUploadError): ?>
                            <?= c::Message($mediaUploadError, 'error') ?>
                        <?php endif; ?>
                        <?php if (!empty($mediaUploadErrors['_form'] ?? null)): ?>
                            <?= c::Message($mediaUploadErrors['_form'], 'error') ?>
                        <?php endif; ?>

                        <?= c::Button("Télécharger le média", "fage", "submit") ?>
                    </form>
                </div>


                <?php if (!empty($article_media)): ?>
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold">Médias actuels</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($article_media as $media): ?>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <?php if (strpos($media['mime_type'], 'image/') === 0): ?>
                                        <img src="/assets/uploads/<?= htmlspecialchars($media['filename']) ?>"
                                            alt="<?= htmlspecialchars($media['original_name']) ?>"
                                            class="w-full h-32 object-cover rounded mb-2">
                                    <?php else: ?>
                                        <div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center">
                                            <span class="text-gray-500 text-sm"><?= htmlspecialchars($media['original_name']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <h4 class="font-medium text-sm truncate"><?= htmlspecialchars($media['original_name']) ?></h4>
                                    <p class="text-gray-500 text-xs mb-2">
                                        <?= number_format($media['size_bytes'] / 1024, 2) ?> KB
                                    </p>

                                    <?php if (!empty($media['description'])): ?>
                                        <p class="text-gray-700 text-sm mb-2"><?= htmlspecialchars($media['description']) ?></p>
                                    <?php endif; ?>

                                    <form method="post" action="/edit_article?id=<?= $article_id ?>" style="display: inline;" onsubmit="return confirm('Retirer ce média ?')">
                                        <input type="hidden" name="action" value="remove_media">
                                        <input type="hidden" name="document_id" value="<?= $media['id'] ?>">
                                        <button type="submit" class="bg-transparent border-0 underline cursor-pointer p-0 font-inherit text-red-600 hover:text-red-800">Retirer</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucun média associé à cet article</p>
                <?php endif; ?>
                <div>
                    <?php if ($mediaRemoveSuccess): ?>
                        <?= c::Message($mediaRemoveSuccess, 'success') ?>
                    <?php endif; ?>
                    <?php if ($mediaRemoveError): ?>
                        <?= c::Message($mediaRemoveError, 'error') ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

</body>