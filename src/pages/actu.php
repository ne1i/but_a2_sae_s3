<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ .  "/../templates/head.php";
?>

<body>
    <?php
    require_once __DIR__ .  "/../templates/header.php";
    ?>
    <main class="flex justify-center mb-6  lg:w-9/12 mx-auto">


        <aside class="z-100">

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    /** @type {HTMLButtonElement}*/
                    const button = document.getElementById("sidePanelBtn");
                    /** @type {HTMLDivElement}*/
                    const sidePanel = document.getElementById("sidePanel");

                    button.addEventListener("click", function() {
                        sidePanel.classList.toggle("-translate-x-full");
                        button.children[0].classList.toggle("rotate-180");
                    })
                })
            </script>
            <div id="sidePanel"
                class="py-10 pt-5 px-5 mr-4  bg-fage-50 rounded-l-none rounded-tr-none  rounded-2xl  mt-10 left-0 top-30 h-fit fixed -translate-x-full transition ease-out lg:hidden block max-w-8/12">
                <button id="sidePanelBtn"
                    class="absolute top-0 left-full bg-fage-100 hover:bg-fage-200 rounded-r-2xl p-2 text-center lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="transition w-8">
                        <path
                            d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z" />
                    </svg>
                </button>
                <ul class="">
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a href="/actu" class=" p-2 mb-2 inline-block">Actualités de la FAGE et de ses Fédérations
                        </a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a href="/blog" class="  p-2 mb-2 inline-block">Le Blog de la présidence </a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a href="" class="  p-2 mb-2 inline-block">Espace Multimédia </a>
                    </li>


                </ul>
            </div>
            <!-- Écran large -->
            <div
                class=" py-10 pt-5 px-5 mr-4 w-64 bg-fage-50 rounded-2xl mt-10 lg:block hidden md:items-start fixed top-1/8 left-1/10 ">
                <ul>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a href="/actu" class=" p-2 mb-2 inline-block">Actualités de la FAGE et de ses Fédérations
                        </a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a href="/blog" class="  p-2 mb-2 inline-block">Le Blog de la présidence </a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a href="" class="  p-2 mb-2 inline-block">Espace Multimédia </a>
                    </li>
                </ul>
            </div>
        </aside>



        <article class="lg:mt-8 lg:w-9/12 text-lg p-10 lg:p-0 lg:ml-[250px]">

            <h1 class="text-center text-4xl text-fage-500 font-bold text-shadow-md">
                Actualités de la FAGE et de ses Fédérations
            </h1>
            <br>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <figure class="relative w-80 m-2">
                    <p class="sr-only">Filtre par type d'actualités</p>
                    <select
                        class="block appearance-none w-full bg-white border border-blue-300 text-gray-700 py-3 px-4 pr-10 rounded-md leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                        <option disabled selected>Choisissez un type d'actualités</option>
                        <option>CD et DP</option>
                        <option>Interviews</option>
                    </select>
                    <figure class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </figure>
                </figure>

                <figure class="relative w-80 m-2">
                    <p class="sr-only">Filtre par thèmes</p>
                    <select
                        class="block appearance-none w-full bg-white border border-blue-300 text-gray-700 py-3 px-4 pr-10 rounded-md leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                        <option disabled selected>Thèmes</option>
                        <option>Enseignement supérieur</option>
                        <option>Social</option>
                        <option>Jeunesse</option>
                        <option>Innovation sociale</option>
                        <option>Formation</option>
                    </select>
                    <figure class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </figure>
                </figure>

                <figure class="relative w-80 m-2">
                    <p class="sr-only">Filtre par acteurs</p>
                    <select
                        class="block appearance-none w-full bg-white border border-blue-300 text-gray-700 py-3 px-4 pr-10 rounded-md leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                        <option disabled selected>Acteurs</option>
                        <option>Fage</option>
                        <option>Fedés Territoriales</option>
                        <option>Fédés de filiere</option>
                        <option>Membres associés</option>
                    </select>
                    <figure class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </figure>
                </figure>

                <figure class="relative w-80 m-2">
                    <p class="sr-only">Filtre par dates</p>
                    <select
                        class="block appearance-none w-full bg-white border border-blue-300 text-gray-700 py-3 px-4 pr-10 rounded-md leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                        <option disabled selected>Dates</option>
                        <option>Aujourd'hui</option>
                        <option>Cette semaine</option>
                    </select>
                    <figure class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </figure>
                </figure>
                <article class="relative w-80">
                    <button
                        class="block text-center bg-fage-500 hover:bg-fage-600 active:bg-fage-700 rounded-md px-4 text-white py-2 font-bold">
                        Rechercher
                    </button>
                </article>

            </div>
            <hr class="my-4">
            <div>
                <ul>
                    <li>
                        <a href="https://www.fage.org/news/actualites-fage-federations/2025-09-03,DP-FAGE-ICDR-2025.htm"
                            class="block">
                            <span class="font-bold text-[18px]">
                                Actualité, CP et DP, Enseignement supérieur, Social, Jeunesse, Fage, Fédés
                                territoriales, Fédés de filière
                            </span>
                            <p class="date font-light text-[13px]">
                                03/09/2025
                            </p>
                            <h3 class="text-fage-500 text-[30px]">
                                Indicateur du coût de la rentrée étudiante 2025
                            </h3>
                            <div class="flex lg:flex-row flex-col gap-4 lg:items-start items-center">
                                <img src="assets/image/article1.png"
                                    alt="Indicateur du coût de la rentrée étudiante 2025" width="230" height="141">
                                <p class="flex-1 text-[15px]">
                                    Cette nouvelle rentrée se présente comme un coup de massue supplémentaire pour le
                                    public étudiant. Les constats de ce 23ème indicateur du coût de la rentrée étudiante
                                    sont dramatiques et intimement liés à un contexte évident d’instabilité politique.
                                    Via cet indicateur à la méthode fiable et complète, la FAGE tient à mettre en avant
                                    les chiffres qui reflètent la situation de pauvreté sans appel connue par des
                                    milliers de jeunes.
                                </p>
                            </div>
                        </a>
                    </li>

                    <hr class="my-4">

                    <li>
                        <a href=https://www.fage.org/news/actualites-fage-federations/2025-06-06,resultats-cneser-2025.htm
                            class="block">
                            <span class="font-bold text-[18px]">
                                Actualité, CP et DP, Enseignement supérieur, Social, Jeunesse, Innovation sociale, Fage,
                                Fédés territoriales, Fédés de filière
                            </span>
                            <p class="date font-light text-[13px]">
                                06/06/2025
                            </p>
                            <h3 class="text-fage-500 text-[30px]">
                                Résultats des élections CNESER 2025
                            </h3>
                            <div class="flex lg:flex-row flex-col gap-4 lg:items-start items-center">
                                <img src="assets/image/article2.png"
                                    alt="Indicateur du coût de la rentrée étudiante 2025" width="230" height="141">
                                <p class="flex-1 text-[15px]">
                                    Pour le 5ème scrutin consécutif, la FAGE confirme largement sa place de 1ère ORE de
                                    France.
                                </p>
                            </div>
                        </a>
                    </li>

                    <hr class="my-4">

                    <li>
                        <a href=https://www.fage.org/news/actualites-fage-federations/2025-05-07,etats-generaux-2025.htm
                            class="block">
                            <span class="font-bold text-[18px]">
                                Actualité, CP et DP, Enseignement supérieur, Social, Jeunesse, Innovation sociale, Fage,
                                Fédés territoriales, Fédés de filière
                            </span>
                            <p class="date font-light text-[13px]">
                                07/05/2025
                            </p>
                            <h3 class="text-fage-500 text-[30px]">
                                Etats Généraux de la démocratie et de l'engagement des jeunes - 2025
                            </h3>
                            <div class="flex lg:flex-row flex-col gap-4 lg:items-start items-center">
                                <img src="assets/image/article3.png"
                                    alt="Indicateur du coût de la rentrée étudiante 2025" width="230" height="141">
                                <p class="flex-1  text-[15px]">
                                    Près de 3000 jeunes se sont expriméEs sur leur rapport à la démocratie et à
                                    l'engagement en France !
                                    <br>
                                    <br>
                                    Nous publions aujourd’hui le dossier de presse des résultats des États Généraux de
                                    la démocratie et de l’engagement des jeunes : une consultation nationale menée sur
                                    plusieurs mois auprès de près de 3000 jeunes de 16 à 30 ans.
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

        </article>




    </main>


    <?php
    require_once __DIR__ .  "/../templates/footer.php";
    ?>
</body>