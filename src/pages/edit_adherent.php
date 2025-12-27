<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\dto\AddAdherentDto;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\validation\Validators;
use ButA2SaeS3\Components as c;
use ButA2SaeS3\utils\HtmlUtils;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

$id = $_GET["id"] ?? null;
if (!$id) {
    header("Location: /adherents_benevoles");
    exit;
}

$adherent = $db->get_adherent_by_id($id);
if (!$adherent) {
    header("Location: /adherents_benevoles");
    exit;
}
if (HttpUtils::isPost()) {
    $result = Validators::validate_add_adherent($_POST);

    if ($result->isValid()) {

        $updated_adherent = $result->value();

        $existing_adherent = $db->get_adherent_by_email($updated_adherent->email);
        if ($existing_adherent && $existing_adherent->id != $id) {
            $error = "Cet email est déjà utilisé par un autre adhérent";
        } else {
            if ($db->update_adherent($id, $updated_adherent)) {
                $success = "L'adhérent $updated_adherent->prenom $updated_adherent->nom a bien été mis à jour";
                $adherent = $db->get_adherent_by_id($id);
            } else {
                $error = "Une erreur est survenue lors de la mise à jour";
            }
        }
    }
}
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">
    <main class="p-2">
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <div class="mb-4">
                    <?= c::BackToLink("Retour aux adhérents", "/adherents_benevoles#adherents-table"); ?>
                </div>
                <?= c::Heading2("Modifier un adhérent") ?>
                <form action="/edit_adherent?id=<?= $id ?>" method="post" class="flex flex-col bg-white">
                    <div class="flex gap-4 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label for="prenom" class="text-lg">Prénom</label>
                            <input required type="text" name="prenom" class=" border-2 rounded-full pl-2 py-1 bg-[#fafafa]" value="<?= htmlspecialchars($adherent->prenom) ?>">

                        </div>

                        <div class="flex flex-col w-1/2">
                            <label for="nom" class="text-lg">Nom</label>
                            <input required type="text" name="nom" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]" value="<?= htmlspecialchars($adherent->nom) ?>">

                        </div>
                    </div>

                    <label for="adresse" class="text-lg">Adresse</label>
                    <input required type="text" name="adresse" class="border-2 mb-4 rounded-full pl-2 py-1 bg-[#fafafa]" value="<?= htmlspecialchars($adherent->adresse) ?>">


                    <div class="flex gap-4 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label for="code_postal" class="text-lg">Code postal</label>
                            <input required type="text" name="code_postal" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]" value="<?= htmlspecialchars($adherent->code_postal) ?>">

                        </div>

                        <div class="flex flex-col w-1/2">
                            <label for="ville" class="text-lg">Ville</label>
                            <input required type="text" name="ville" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]" value="<?= htmlspecialchars($adherent->ville) ?>">

                        </div>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label for="tel" class="text-lg">Téléphone</label>
                            <input required type="tel" name="tel" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]" value="<?= htmlspecialchars($adherent->tel) ?>">

                        </div>

                        <div class="flex flex-col w-1/2">
                            <label for="email" class="text-lg">Email</label>
                            <input required type="email" name="email" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]" value="<?= htmlspecialchars($adherent->email) ?>">

                        </div>
                    </div>

                    <div class="flex flex-col ">
                        <label for="age" class="text-lg">Âge</label>
                        <input required type="number" name="age" class="border-2 rounded-full pl-2 py-1 pr-2 bg-[#fafafa]" value="<?= htmlspecialchars($adherent->age) ?>">

                    </div>

                    <div class="flex flex-col ">
                        <label for="profession" class="text-lg">Profession</label>
                        <input required type="text" name="profession" class="border-2 rounded-full pl-2 py-1 bg-[#fafafa]" value="<?= htmlspecialchars($adherent->profession) ?>">

                    </div>

                    <button type="submit" class="bg-fage-700 hover:bg-fage-800 rounded-full py-2 my-4 text-white">
                        Mettre à jour l'adhérent
                    </button>

                    <?php

                    ?>

                </form>
            </div>
        </div>

    </main>
</body>