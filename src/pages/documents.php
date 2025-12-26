<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

// Handle form submissions
if (HttpUtils::isPost()) {
    if (isset($_POST['action']) && $_POST['action'] === 'upload_document' && isset($_FILES['document_file'])) {
        $upload_dir = __DIR__ . "/../public/assets/documents/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file = $_FILES['document_file'];
        $filename = time() . '_' . basename($file['name']);
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $user_id = HttpUtils::get_current_user_id($db);

            if ($db->upload_document(
                $filename,
                $file['name'],
                $file['type'],
                $file['size'],
                $user_id,
                $target_path,
                $_POST['description'] ?? null
            )) {
                $success = "Le document \"{$file['name']}\" a bien été téléchargé";
            } else {
                $error = "Erreur lors de l'enregistrement du document";
            }
        } else {
            $error = "Erreur lors du téléchargement du fichier";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_document' && isset($_POST['delete_id'])) {
        $document = $db->get_document_by_id($_POST['delete_id']);
        if ($document && $db->delete_document($_POST['delete_id'])) {
            $success = "Le document \"{$document['original_name']}\" a bien été supprimé";
        } else {
            $error = "Une erreur est survenue lors de la suppression du document";
        }
    }
}

// Get data for display
$page = max($_GET["page"] ?? 1, 1);
$documents = $db->get_documents(20, $page, $_GET["filter-filename"] ?? "");
$total_count = $db->get_documents_count($_GET["filter-filename"] ?? "");
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

        <!-- Upload Document Form -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink(); ?>
                </div>
                <?= c::Heading2("Télécharger un document") ?>

                <form action="/documents" method="post" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="upload_document">

                    <div>
                        <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">Fichier</label>
                        <input type="file" id="document_file" name="document_file" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            placeholder="Description du document (optionnel)"></textarea>
                    </div>

                    <?= c::Button("Télécharger le document", "fage", "submit") ?>
                </form>
            </div>
        </div>

        <!-- Documents List -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Documents existants") ?>

                <!-- Filters -->
                <div class="mb-4">
                    <form method="get" action="/documents" class="flex gap-4">
                        <?= c::FormInput("filter-filename", "Filtrer par nom", "text", $_GET["filter-filename"] ?? "", false, "", ["placeholder" => "Nom du fichier"]) ?>
                        <?= c::Button("Filtrer", "fage", "submit") ?>
                        <?php if (!empty($_GET["filter-filename"])): ?>
                            <?= c::Button("Effacer les filtres", "gray", "link", "inline-block", ["href" => "/documents"]) ?>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Documents Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                                <a href="/documents?action=delete_document&delete_id=<?= $document['id'] ?>" class="text-red-600 underline" onclick="return confirm('Supprimer ce document ?')">Supprimer</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
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