<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

$article_id = $_GET['id'] ?? null;

if (!$article_id) {
    header('Location: /articles');
    exit;
}

$article = $db->get_article_by_id($article_id);

if (!$article) {
    header('Location: /articles');
    exit;
}


if (HttpUtils::isPost()) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_article' && isset($_POST['title']) && isset($_POST['content'])) {
            $status = isset($_POST['status']) ? $_POST['status'] : null;

            if ($db->update_article(
                $article_id,
                $_POST['title'],
                $_POST['content'],
                $status
            )) {
                $success = "L'article \"{$_POST['title']}\" a bien été mis à jour";


                $article = $db->get_article_by_id($article_id);
            } else {
                $error = "Une erreur est survenue lors de la mise à jour de l'article";
            }
        } elseif ($_POST['action'] === 'upload_media' && isset($_FILES['media_file'])) {
            $upload_dir = __DIR__ . "/../public/assets/uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file = $_FILES['media_file'];
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
                    $document_id = $db->get_last_insert_id();

                    if ($db->attach_media_to_article($article_id, $document_id)) {
                        $success = "Le média a bien été ajouté à l'article";
                    } else {
                        $error = "Erreur lors de l'attachement du média à l'article";
                    }
                } else {
                    $error = "Erreur lors de l'enregistrement du média";
                }
            } else {
                $error = "Erreur lors du téléchargement du fichier";
            }
        } elseif ($_POST['action'] === 'remove_media' && isset($_POST['document_id'])) {
            if ($db->detach_media_from_article($article_id, $_POST['document_id'])) {
                $success = "Le média a bien été retiré de l'article";
            } else {
                $error = "Erreur lors du retrait du média";
            }
        }
    }
}


$article_media = $db->get_article_media($article_id);
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">



        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink("Retour aux articles", "/articles"); ?>
                </div>
                <?= c::Heading2("Modifier l'article") ?>

                <form action="/edit_article?id=<?= $article_id ?>" method="post" class="space-y-4">
                    <input type="hidden" name="action" value="update_article">

                    <?= c::FormInput("title", "Titre de l'article", "text", htmlspecialchars($article['title']), true) ?>

                    <div>
                        <?= c::Textarea("content", "Contenu de l'article", htmlspecialchars($article['content']), true, "", ["rows" => "12"]) ?>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="status" value="draft" <?= $article['status'] === 'draft' ? 'checked' : '' ?> class="text-fage-600">
                            <span>Brouillon</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="status" value="published" <?= $article['status'] === 'published' ? 'checked' : '' ?> class="text-fage-600">
                            <span>Publié</span>
                        </label>
                    </div>
                    <?php
                    if (isset($error)) {
                        echo c::Message($error, 'error');
                    }
                    if (isset($success)) {
                        echo c::Message($success, 'success');
                    }
                    ?>
                    <div class="flex mt-2 gap-4">
                        <?= c::Button("Enregistrer les modifications", "fage", "submit") ?>
                        <?= c::Button("Retour à la liste", "gray", "link", "", ["href" => "/articles"]) ?>
                        </a>
                        <?= c::Button("Voir sur le site", "blue", "button", "", ["onclick" => "window.open('/blog', '_blank')"]) ?>
                    </div>
                </form>
            </div>
        </div>


        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
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

                        <?= c::FormInput("description", "Description du média", "text", "", false) ?>

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

                                    <a href="/edit_article?id=<?= $article_id ?>&action=remove_media&document_id=<?= $media['id'] ?>" class="text-red-600 underline" onclick="return confirm('Retirer ce média ?')">Retirer</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucun média associé à cet article</p>
                <?php endif; ?>
            </div>
        </div>

    </main>

</body>