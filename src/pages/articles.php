<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\repositories\ArticleRepository;
use ButA2SaeS3\services\ArticleValidationService;
use ButA2SaeS3\services\FormService;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();
$articleRepository = new ArticleRepository($db->getConnection());

HttpUtils::ensure_valid_session($db);

$action = $_POST['action'] ?? null;

if (HttpUtils::isPost() && $action === 'delete_article' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $article = $articleRepository->findById($deleteId);
    if ($article && $articleRepository->delete($deleteId)) {
        FormService::setSuccessMessage("L'article \"{$article['title']}\" a bien été supprimé", "article_delete");
        header('Location: /articles?success=1&success_form=article_delete#articles-table');
    } else {
        FormService::setErrorMessage("Une erreur est survenue lors de la suppression de l'article", "article_delete");
        header('Location: /articles#articles-table');
    }
    exit;
}

if (HttpUtils::isPost() && $action === 'publish_article' && isset($_POST['publish_id'])) {
    $publishId = (int)$_POST['publish_id'];
    $article = $articleRepository->findById($publishId);
    if ($article && $articleRepository->publish($publishId)) {
        FormService::setSuccessMessage("L'article \"{$article['title']}\" a bien été publié", "article_publish");
        header('Location: /articles?success=1&success_form=article_publish#articles-table');
    } else {
        FormService::setErrorMessage("Une erreur est survenue lors de la publication de l'article", "article_publish");
        header('Location: /articles#articles-table');
    }
    exit;
}

FormService::handleFormSubmission(
    [ArticleValidationService::class, 'validateAddArticle'],
    function ($dto) use ($db, $articleRepository) {
        $userId = HttpUtils::get_current_user_id($db);
        if (!$userId) {
            throw new \Exception("Utilisateur introuvable");
        }
        $articleRepository->add($dto, $userId);
    },
    "L'article a bien été créé",
    "/articles#articles-create",
    "article_add"
);

$articleAddState = FormService::restoreFormData("article_add");
$formData = $articleAddState['data'] ?? [];
$formErrors = $articleAddState['errors'] ?? [];
$articleAddSuccess = FormService::getSuccessMessage("article_add");
$articleAddError = FormService::getErrorMessage("article_add");

$articlePublishSuccess = FormService::getSuccessMessage("article_publish");
$articlePublishError = FormService::getErrorMessage("article_publish");
$articleDeleteSuccess = FormService::getSuccessMessage("article_delete");
$articleDeleteError = FormService::getErrorMessage("article_delete");



$page = max($_GET["page"] ?? 1, 1);
$filterTitle = $_GET["filter-title"] ?? "";
$filterStatus = $_GET["filter-status"] ?? "";
$articles = $articleRepository->list(20, $page, $filterTitle, $filterStatus);
$total_count = $articleRepository->count($filterTitle, $filterStatus);
$page_count = ceil($total_count / 20);
?>

<?php require_once __DIR__ . "/../templates/admin_head.php"; ?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">




        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div class="mb-4">

                <?= c::BackToLink(); ?>
            </div>
            <?= c::Heading2("Créer un nouvel article", id: "articles-create") ?>



            <form action="/articles" method="post" class="space-y-4">
                <input type="hidden" name="action" value="add_article">

                <?= c::FormInput("title", "Titre de l'article", "text", $formData['title'] ?? "", true, "", ["error" => $formErrors['title'] ?? null]) ?>

                <div>
                    <?= c::Textarea("content", "Contenu de l'article", $formData['content'] ?? "", true, "", ["rows" => "12", "placeholder" => "Rédigez votre article ici...", "error" => $formErrors['content'] ?? null]) ?>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="status" value="draft" <?= ($formData['status'] ?? 'draft') === 'draft' ? 'checked' : '' ?> class="text-fage-600">
                        <span>Enregistrer en brouillon</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="status" value="published" <?= ($formData['status'] ?? 'draft') === 'published' ? 'checked' : '' ?> class="text-fage-600">
                        <span>Publier immédiatement</span>
                    </label>
                </div>

                <div>
                    <?php if ($articleAddSuccess): ?>
                        <?= c::Message($articleAddSuccess, 'success') ?>
                    <?php endif; ?>
                    <?php if ($articleAddError): ?>
                        <?= c::Message($articleAddError, 'error') ?>
                    <?php endif; ?>
                    <?php if (!empty($formErrors['_form'] ?? null)): ?>
                        <?= c::Message($formErrors['_form'], 'error') ?>
                    <?php endif; ?>
                </div>

                <div class="flex gap-4">
                    <?= c::Button("Enregistrer l'article", "fage", "submit") ?>
                    <?= c::OutlineButton("Voir le site public", "blue", "link", "", attributes: ["href" => "/blog", "target" => "_blank"]) ?>
                </div>
            </form>
        </div>


        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <?= c::Heading2("Articles existants", id: "articles-table") ?>
                <div>
                    <?php if ($articlePublishSuccess): ?>
                        <?= c::Message($articlePublishSuccess, 'success') ?>
                    <?php endif; ?>
                    <?php if ($articlePublishError): ?>
                        <?= c::Message($articlePublishError, 'error') ?>
                    <?php endif; ?>
                    <?php if ($articleDeleteSuccess): ?>
                        <?= c::Message($articleDeleteSuccess, 'success') ?>
                    <?php endif; ?>
                    <?php if ($articleDeleteError): ?>
                        <?= c::Message($articleDeleteError, 'error') ?>
                    <?php endif; ?>
                </div>

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

                        <?= c::Button("Filtrer", "fage", "submit") ?>
                        <?php if (!empty($_GET["filter-title"]) || !empty($_GET["filter-status"])): ?>
                            <?= c::FormSelect("filter-status", label: "", options: $role_options, selected: $_GET["filter-status"], attributes: ["id" => "filter-status"]); ?>
                            <?= c::Button("Effacer les filtres", "gray", "link", "inline-block", ["href" => "/articles"]) ?>
                        <?php endif; ?>
                    </form>
                </div>


                <?php if (!empty($articles)): ?>
                    <div class="scroll-container">
                        <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border-2 px-4 py-2 text-left">Titre</th>
                                    <th class="border-2 px-4 py-2 text-left">Auteur</th>
                                    <th class="border-2 px-4 py-2 text-left">Statut</th>
                                    <th class="border-2 px-4 py-2 text-left">Date de création</th>
                                    <th class="border-2 px-4 py-2 text-left">Date de publication</th>
                                    <th class="border-2 px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $idx = 0;
                                foreach ($articles as $article): ?>
                                    <tr class="<?= $idx % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                        <td class="border-2 px-4 py-2 font-medium">
                                            <?= htmlspecialchars($article['title']) ?>
                                        </td>
                                        <td class="border-2 px-4 py-2">
                                            <?= htmlspecialchars($article['author_username'] ?? 'Inconnu') ?>
                                        </td>
                                        <td class="border-2 px-4 py-2">
                                            <?php if ($article['status'] === 'published'): ?>
                                                <?= c::Badge('Publié', 'fage', 'text-sm') ?>
                                            <?php else: ?>
                                                <?= c::Badge('Brouillon', 'muted', 'text-sm') ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="border-2 px-4 py-2">
                                            <?= date('d/m/Y H:i', strtotime($article['created_at'])) ?>
                                        </td>
                                        <td class="border-2 px-4 py-2">
                                            <?= $article['published_at'] ? date('d/m/Y H:i', $article['published_at']) : '-' ?>
                                        </td>
                                        <td class="border-2 px-4 py-2">
                                            <a href="/edit_article?id=<?= $article['id'] ?>" class="text-blue-600 underline">Modifier</a>
                                            <?php if ($article['status'] === 'draft'): ?>
                                                <span class="ml-2">
                                                    <form method="post" action="/articles" style="display: inline;" onsubmit="return confirm('Publier cet article ?')">
                                                        <input type="hidden" name="action" value="publish_article">
                                                        <input type="hidden" name="publish_id" value="<?= $article['id'] ?>">
                                                        <button type="submit" class="bg-transparent border-0 underline cursor-pointer p-0 font-inherit text-green-600 hover:text-green-800">Publier</button>
                                                    </form>
                                                </span>
                                            <?php endif; ?>
                                            <span class="ml-2">
                                                <form method="post" action="/articles" style="display: inline;" onsubmit="return confirm('Supprimer cet article ?')">
                                                    <input type="hidden" name="action" value="delete_article">
                                                    <input type="hidden" name="delete_id" value="<?= $article['id'] ?>">
                                                    <button type="submit" class="bg-transparent border-0 underline cursor-pointer p-0 font-inherit text-red-600 hover:text-red-800">Supprimer</button>
                                                </form>
                                            </span>
                                        </td>
                                    </tr>
                                <?php
                                    $idx++;
                                endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucun article trouvé</p>
                <?php endif; ?>


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