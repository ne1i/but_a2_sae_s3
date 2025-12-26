<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

// Handle form submissions
if (HttpUtils::isPost()) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_article' && isset($_POST['title']) && isset($_POST['content'])) {
            $status = $_POST['status'] ?? 'draft';
            $user_id = HttpUtils::get_current_user_id($db);

            if ($db->add_article(
                $_POST['title'],
                $_POST['content'],
                $user_id,
                $status
            )) {
                $success = "L'article \"{$_POST['title']}\" a bien été " . ($status === 'published' ? 'publié' : 'enregistré en brouillon');
            } else {
                $error = "Une erreur est survenue lors de l'ajout de l'article";
            }
        } elseif ($_POST['action'] === 'delete_article' && isset($_POST['delete_id'])) {
            $article = $db->get_article_by_id($_POST['delete_id']);
            if ($article && $db->delete_article($_POST['delete_id'])) {
                $success = "L'article \"{$article['title']}\" a bien été supprimé";
            } else {
                $error = "Une erreur est survenue lors de la suppression de l'article";
            }
        } elseif ($_POST['action'] === 'publish_article' && isset($_POST['publish_id'])) {
            $article = $db->get_article_by_id($_POST['publish_id']);
            if ($article && $db->publish_article($_POST['publish_id'])) {
                $success = "L'article \"{$article['title']}\" a bien été publié";
            } else {
                $error = "Une erreur est survenue lors de la publication de l'article";
            }
        }
    }
}

// Get data for display
$page = max($_GET["page"] ?? 1, 1);
$articles = $db->get_articles(20, $page, $_GET["filter-title"] ?? "", $_GET["filter-status"] ?? "");
$total_count = $db->get_articles_count($_GET["filter-title"] ?? "", $_GET["filter-status"] ?? "");
$page_count = ceil($total_count / 20);
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">
        <!-- Alert messages -->


        <!-- Add Article Form -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div class="mb-4">

                <?= c::BackToLink(); ?>
            </div>
            <?= c::Heading2("Créer un nouvel article") ?>

            <?php
            if (isset($error)) {
                echo c::Message($error, 'error');
            }
            if (isset($success)) {
                echo c::Message($success, 'success');
            }
            ?>

            <form action=" /articles" method="post" class="space-y-4">
                <input type="hidden" name="action" value="add_article">

                <?= c::FormInput("title", "Titre de l'article", "text", "", true) ?>

                <div>
                    <?= c::Textarea(
                        name: "content",
                        label: "Contenu de l'article",
                        class: "",
                        attributes: ["rows" => "12", "required" => "required", "placeholder" => "Rédigez votre article ici..."]
                    ) ?>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="status" value="draft" checked class="text-fage-600">
                        <span>Enregistrer en brouillon</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="status" value="published" class="text-fage-600">
                        <span>Publier immédiatement</span>
                    </label>
                </div>

                <div class="flex gap-4">
                    <?= c::Button("Enregistrer l'article", "fage", "submit") ?>
                    <?= c::OutlineButton("Voir le site public", "blue", "link", "", attributes: ["href" => "/blog", "target" => "_blank"]) ?>
                </div>
            </form>
        </div>

        <!-- Articles List -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Articles existants") ?>

                <!-- Filters -->
                <div class="mb-4">
                    <form method="get" action="/articles" class="flex gap-4 items-end">
                        <?= c::FormInput("filter-title", "Filtrer par titre", "text", $_GET["filter-title"] ?? "", false, "", ["placeholder" => "Titre de l'article"]) ?>

                        <?php
                        $role_options = [
                            "" => "Tout les status",
                            "draft" =>  "Brouillons",
                            "published" => "Publié"
                        ];
                        ?>

                        <?= c::FormSelect("filter-status", label: "", options: $role_options, selected: $_GET["filter-status"], attributes: ["id" => "filter-status"]); ?>
                        <?= c::Button("Filtrer", "fage", "submit") ?>
                        <?php if (!empty($_GET["filter-title"]) || !empty($_GET["filter-status"])): ?>
                            <?= c::Button("Effacer les filtres", "gray", "link", "inline-block", ["href" => "/articles"]) ?>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Articles Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left">Titre</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Auteur</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Statut</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Date de création</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Date de publication</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $article): ?>
                                <tr class="bg-white hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2 font-medium">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <?= htmlspecialchars($article['author_username'] ?? 'Inconnu') ?>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <?php if ($article['status'] === 'published'): ?>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-medium">Publié</span>
                                        <?php else: ?>
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-medium">Brouillon</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <?= date('d/m/Y H:i', strtotime($article['created_at'])) ?>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <?= $article['published_at'] ? date('d/m/Y H:i', $article['published_at']) : '-' ?>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="/edit_article?id=<?= $article['id'] ?>" class="text-blue-600 underline">Modifier</a>
                                        <?php if ($article['status'] === 'draft'): ?>
                                            <span class="ml-2">
                                                <a href="/articles?action=publish_article&publish_id=<?= $article['id'] ?>" class="text-green-600 underline" onclick="return confirm('Publier cet article ?')">Publier</a>
                                            </span>
                                        <?php endif; ?>
                                        <span class="ml-2">
                                            <a href="/articles?action=delete_article&delete_id=<?= $article['id'] ?>" class="text-red-600 underline" onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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
                        <?= c::Button("Précédent", "fage", "link", "", ["href" => "/articles?page={$previous_page}&filter-title=" . urlencode($_GET["filter-title"] ?? "") . "&filter-status=" . urlencode($_GET["filter-status"] ?? "")]) ?>
                    <?php else: ?>
                        <?= c::Button("Précédent", "gray", "button", "", ["disabled"]) ?>
                    <?php endif; ?>
                    <span class="rounded-full text-white bg-fage-700 inline-block px-3 py-1"><?= $current_page ?></span>
                    <?php if ($next): ?>
                        <?= c::Button("Suivant", "fage", "link", "", ["href" => "/articles?page={$next_page}&filter-title=" . urlencode($_GET["filter-title"] ?? "") . "&filter-status=" . urlencode($_GET["filter-status"] ?? "")]) ?>
                    <?php else: ?>
                        <?= c::Button("Suivant", "gray", "button", "", ["disabled"]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

</body>