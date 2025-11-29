<!DOCTYPE html>
<html lang="fr"><?php
                require_once __DIR__ .  "/../templates/head.php";
                ?>

<body>
    <?php
    require_once __DIR__ .  "/../templates/header.php";
    ?>


    <main class="lg:mt-8 lg:w-10/12  lg:p-0 lg:ml-[250px] relative flex-1">



        <div id="asso-ancien" class="scroll-mt-32 flex justify-center mb-30  lg:w-10/12 mx-auto">


            <article class=" lg:mt-8 lg:w-10/12 text-lg p-10 lg:p-0">
                <h1 class="text-4xl text-fage-500 font-bold text-shadow-md">Contactez-nous !
                </h1>
                <section class=" mt-4">
                    Si vous êtes étudiantE, lycéenNE, jeune (ou que vous nous contacter pour unE
                    étudiantE/lycéenNE/jeune), et que vous avez une question/problématique (quelque soit la thématique :
                    logement, bourses, examens, orientation...), ne remplissez pas ce formulaire mais contactez-nous à
                    <a href="mailto:mesdroits@fage.org"
                        class="text-fage-500 hover:text-fage-900 hover:underline">mesdroits@fage.org</a>.
                </section>
                <hr class="my-4">
                <h3 class="mb-4 text-red-600">Les champs marqués d'un astérisque sont obligatoires</h3>
                <h2 class="text-2xl text-fage-500 mb-6 ml-4">Formulaire de contact</h2>
                <form action="" class="flex flex-col gap-y-8">
                    <div class="flex lg:flex-row flex-col lg:items-center gap-4">

                        <label for="nom" class="lg:text-right w-46">Nom <strong class="text-red-600">*</strong></label>
                        <input name="nom" id="nom" class="border border-fage-200 resize-none h-8 p-2 w-full lg:max-w-90"
                            required></input>
                    </div>
                    <div class="flex lg:flex-row flex-col lg:items-center gap-4">
                        <label for="nom" class="lg:text-right w-46">Prénom <strong
                                class="text-red-600">*</strong></label>
                        <input name="prenom" id="prenom"
                            class="border border-fage-200 resize-none h-8 p-2 w-full lg:max-w-90" required></input>
                    </div>
                    <div class="flex lg:flex-row flex-col lg:items-center gap-4">

                        <label for="email" class="lg:text-right w-46">E-mail <strong
                                class="text-red-600">*</strong></label>
                        <input name="email" id="email"
                            class="border border-fage-200 resize-none h-8 p-2 w-full lg:max-w-90" type="email"
                            required></input>
                    </div>
                    <div class="flex lg:flex-row flex-col lg:items-center gap-4">

                        <label for="telephone" class="lg:text-right w-46">Télephone</label>
                        <input name="telephone" id="telephone"
                            class="border border-fage-200 resize-none h-8 p-2 w-full lg:max-w-90" type="tel"></input>
                    </div>
                    <div class="flex lg:flex-row flex-col lg:items-center gap-4">

                        <label for="etablissement" class="lg:text-right w-46">Établissement / Université /
                            Société</label>
                        <input name="etablissement" id="etablissement"
                            class="border border-fage-200 resize-none h-8 p-2 w-full lg:max-w-90"></input>
                    </div>
                    <div class="flex lg:flex-row flex-col lg:items-center gap-4">

                        <label for="objet" class="lg:text-right w-46">Objet de votre message <strong
                                class="text-red-600">*</strong></label>
                        <input name="objet" id="objet"
                            class="border border-fage-200 resize-none h-8 p-2 w-full lg:max-w-90" required></input>
                    </div>
                    <div class="flex lg:flex-row flex-col  gap-4">

                        <label for="message" class="lg:text-right w-46">Votre message <strong
                                class="text-red-600">*</strong></label>
                        <input name="message" id="message" class="border border-fage-200 h-24 p-2 w-full lg:max-w-90"
                            required></input>
                    </div>
                    <button
                        class="lg:ml-auto bg-fage-400 hover:bg-fage-300 active:bg-fage-600 rounded-lg px-2 lg:py-1 py-10 shadow-md">Envoyer</button>
                </form>




    </main>

    <?php
    require_once __DIR__ .  "/../templates/footer.php";
    ?>
</body>