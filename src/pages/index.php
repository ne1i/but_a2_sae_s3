<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ .  "/../templates/head.php";
?>

<body class="bg-fage-50">
    <?php
    require_once __DIR__ .  "/../templates/header.php";
    ?>

    <main class="mb-6 ">
        <div class="flex justify-center items-center flex-col relative pt-20 pb-40">
            <img src="assets/image/background.jpg" alt="Image de fond"
                class="absolute left-0 top-0 -z-10 opacity-20 h-full object-cover w-full bg-gradient-to-t from-white to-transparent" />

            <h1 class="text-fage-500 lg:text-7xl text-5xl font-bold text-center mb-6 text-shadow-md max-w-11/12">
                FAGE,
                <br class="lg:hidden block">
                l'association
                <br class="lg:hidden block">
                pour les
                <br class="lg:hidden block">
                étudiants
            </h1>
            <h2 class="text-2xl mx-auto text-center text-fage-900 mb-14">
                Fédération des associations
                <br class="lg:hidden block">
                Générales Étudiantes
            </h2>

            <a href="https://www.helloasso.com/associations/federation-des-associations-generales-etudiantes-fage/formulaires/1"
                class="mb-12 bg-gradient-to-bl from-fage-400 to-fage-700 text-white p-8 font-bold rounded-full text-3xl hover:bg-gradient-to-br active:bg-gradient-to-tr shadow-xl hover:-translate-y-1 active:translate-y-1 transition-all"
                target="_blank">Faire un don</a>

            <a class="bg-amber-400 p-6 rounded-xl font-bold shadow-md hover:bg-amber-500 active:bg-amber-600 text-xl">Besoin
                de
                discuter ?</a>
        </div>

        <section class="flex items-center justify-around mt-10 m-4 ">
            <div class=" flex flex-col lg:flex-row p-4 rounded-2xl bg-fage-100" carousel>
                <div class="relative w-full max-w-2xl overflow-hidden ">
                    <div class="flex transition-transform duration-700" carousel-images>
                        <img src="assets/image/actu1.png" alt="Image carousel 1"
                            class="w-full h-96 object-contain flex-shrink-0">
                        <img src="assets/image/actu2.jpeg" alt="Image carousel 2"
                            class="w-full h-96 object-contain flex-shrink-0">
                        <img src="assets/image/actu3.png" alt="Image carousel 3"
                            class="w-full h-96 object-contain flex-shrink-0">
                    </div>
                    <button carousel-previous
                        class="absolute left-3 top-1/2 -translate-y-1/2 bg-white hover:bg-white text-gray-700 p-2 w-8 h-8 rounded-full shadow leading-none"><svg
                            class="rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                            <path
                                d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z" />
                        </svg></button>
                    <button carousel-next
                        class="absolute right-3 top-1/2 -translate-y-1/2 bg-white hover:bg-white text-gray-700 p-2 w-8 h-8 rounded-full shadow leading-none"><svg
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                            <path
                                d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z" />
                        </svg></button>
                </div>
                <div class=" lg:ml-8 flex flex-wrap max-w-2xl lg:w-80 h-96 bg-fage-200 rounded-xl p-6 lg:mt-0 mt-6"
                    carousel-titles>
                    <h4 class="text-2xl hidden">Élections CNESER
                        2025-2027</h4>
                    <h4 class="text-2xl hidden">États généraux de la
                        démocratie et l'engagement des jeunes - 2025</h4>
                    <h4 class="text-2xl hidden">BOUGE TON CROUS 2024</h4>
                </div>
            </div>

        </section>

        <script src="carousel.js"></script>

        <section class="px-2 my-10">
            <h2 class="text-center text-4xl text-fage-500 font-bold text-shadow-md">Les aides mises
                en place par la FAGE</h2>

            <div class="flex flex-wrap justify-around gap-4 mt-12">
                <div
                    class="max-w-sm rounded overflow-hidden shadow-lg transition-all hover:shadow-xl hover:-translate-y-2 bg-white">
                    <a><img class="w-full" src="assets/image/aide1.png" alt="Sunset in the mountains"></a>
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2"><a class="underline text-fage-600 hover:text-fage-900">Une
                                question
                                ?
                                Connaitre et défendre tes
                                droits</a></div>
                        <p class="text-gray-700 text-base">
                            Parcoursup, Master, bourses, logement, santé...
                            Nous avons des droits, défendons-les ! Toute
                            l'année, la FAGE t'accompagne et répond à tes
                            questions.

                            Parcours la foire aux questions ou contacte-nous
                            via <a href="mailto:mesdroits@fage.org"
                                class="text-fage-600 underline hover:text-fage-900">mesdroits@fage.org</a>.
                        </p>
                    </div>

                </div>
                <div
                    class="max-w-sm rounded overflow-hidden shadow-lg transition-all hover:shadow-xl hover:-translate-y-2 bg-white">
                    <a href="https://sos-parcoursup.fr/" target="_blank"><img class="w-full"
                            src="assets/image/aide2.jpeg" alt="Sunset in the mountains"></a>
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2"><a href="https://sos-parcoursup.fr/" target="_blank"
                                class="underline text-fage-600 hover:text-fage-900">SOS-Parcoursup</a>
                        </div>
                        <p class="text-gray-700 text-base">
                            Afin que tu puisses avoir toutes les cartes en
                            main pour choisir ton affectation et
                            connaître tes droits, nous avons créé la
                            plateforme SOS-Parcoursup !

                            Si tu rencontres un problème avec Parcoursup,
                            contacte-nous !
                        </p>
                    </div>

                </div>
                <div
                    class="max-w-sm rounded overflow-hidden shadow-lg transition-all hover:shadow-xl hover:-translate-y-2 bg-white">
                    <a href="https://sos-monmaster.fr/" target="_blank"><img class="w-full" src="assets/image/aide3.png"
                            alt="Sunset in the mountains"></a>
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2"><a href="https://sos-monmaster.fr/" target="_blank"
                                class="underline text-fage-600 hover:text-fage-900">SOS-MonMaster</a>
                        </div>
                        <p class="text-gray-700 text-base">
                            Afin que tu puisses avoir toutes les
                            informations pour choisir ton master et
                            connaître tes
                            droits, nous avons créé la plateforme
                            SOS-MonMaster !

                            Si tu rencontres un problème avec Mon Master,
                            contacte-nous !
                        </p>
                    </div>

                </div>

            </div>
        </section>
    </main>

    <?php
    require_once __DIR__ .  "/../templates/footer.php";
    ?>
</body>