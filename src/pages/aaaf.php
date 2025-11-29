<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ .  "/../templates/head.php";
?>

<body>

    <?php
    require_once __DIR__ .  "/../templates/header.php";
    ?>

    <main class="lg:mt-8 lg:w-10/12 lg:p-0 lg:ml-[250px] relative flex-1">

        <aside>
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
                class="py-10 pt-5 px-5 mr-4 bg-fage-50 rounded-l-none rounded-tr-none rounded-2xl mt-10 left-0 top-30 h-fit fixed -translate-x-full transition ease-out lg:hidden block max-w-8/12">
                <button id="sidePanelBtn"
                    class="absolute top-0 left-full bg-fage-100 hover:bg-fage-200 rounded-r-2xl p-2 text-center lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="transition w-8">
                        <path
                            d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z" />
                    </svg>
                </button>
                <ul>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-2 mb-2 inline-block text-xl" href="/presentation">Présentation</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-2 mb-2 inline-block text-xl" href="/organisation">Organisation</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-2 mb-2 inline-block text-xl"
                            href="https://www.fage.org/les-assos-etudiantes/federations-fage/federations-annuaire/"
                            target="_blank">Les associations membres</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-2 mb-2 inline-block text-xl" href="/aaaf">L'AAAF</a>
                    </li>
                </ul>
            </div>
            <!-- Écran large -->
            <div
                class="py-10 pt-5 px-5 mr-4 w-64 bg-fage-50 rounded-2xl mt-10 lg:block hidden md:items-start fixed top-1/8 left-1/10">
                <ul>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-1 mb-2 inline-block" href="#presentation">Présentation de l'AAAF</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-1 mb-2 inline-block" href="#activites">Nos activités</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-1 mb-2 inline-block" href="#ag">L'Assemblée Générale</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-1 mb-2 inline-block" href="#bureau">Le Bureau de l'AAAF</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-1 mb-2 inline-block" href="#contacts">Nous contacter</a>
                    </li>
                </ul>
            </div>
        </aside>

        <div id="aaaf" class="scroll-mt-32 flex justify-center mb-30 lg:w-10/12 mx-auto">
            <article class="lg:mt-8 lg:w-10/12 text-lg p-10 lg:p-0">
                <h1 class="text-4xl text-fage-500 font-bold m-4 p-4">L'Association des Anciens et Amis de la FAGE (AAAF)
                </h1>
                <section class="scroll-mt-30" id="presentation">
                    <h2 class="text-2xl text-fage-600 font-bold m-1 p-1">Présentation</h2>
                    <div class="flex flex-wrap gap-4 items-center m-1 p-1">
                        <img src="assets/image/logo-aaaf.png" alt="Logo de l'AAAF" height="51.75" width="157.5">
                        <p class="m-0.5 p-0.5 max-w-3xl">L’AAAF est l’Association des Anciens et Amis de la FAGE. Son but
                            est de <strong class="text-fage-500">fédérer et de rassembler d’anciens associatifs du réseau de
                                la FAGE</strong> afin de garder le contact et de partager les expériences. Peuvent s’y
                            inscrire toutes personnes passées par la FAGE et ses fédérations et ne disposant plus d’un
                            mandat étudiant.</p>
                    </div>
                    <p class="m-0.5 p-0.5">L’AAAF est avant tout un réseau d’anciens. S’il ne joue aucun rôle dans la
                        politique de la Fédération, l’association se fait fort de partager ses expériences avec les plus
                        jeunes actuellement en poste.</p>
                    <p class="m-0.5 p-0.5">C’est le cas notamment à l’occasion du Congrès national où, depuis plusieurs
                        années, une table ronde sur l’histoire de la FAGE est organisée sous l’égide de l’AAAF.</p>
                </section>
                <hr class="m-4 text-fage-500">
                <section class="scroll-mt-30" id="activites">
                    <h2 class="text-2xl text-fage-600 font-bold m-1 p-1">Nos activités</h2>
                    <p class="m-0.5 p-0.5">Le projet de l’AAAF pour l’année à venir s’inscrit dans une démarche de mise en
                        réseau et de soutien au développement.</p>
                    <ul class="list-decimal list-inside m-0.5 p-0.5">
                        <li><strong class="text-fage-500">Relancer une dynamique de fonctionnement et l’attrait de
                                l’AAAF</strong>
                            <ul class="list-disc list-inside m-1 p-1">
                                <li>Développer le nombre d’adhérents en lancant un appel auprès de tous les anciens de la
                                    FAGE, afin d’éditer à terme un annuaire et de contribuer au sentiment d’appartenance à
                                    la FAGE.</li>
                                <li>Créer une soirée annuelle nationale de rencontre des anciens de la FAGE et préparer
                                    activement le 30ème anniversaire de la FAGE.</li>
                                <li>Développer une page Internet active de l’AAAF sur le site de la FAGE, une newsletter des
                                    anciens et l’utilisation de la Newsletter FAGE pour communiquer auprès des « futurs »
                                    anciens.</li>
                                <li>Créer un réseau d’anciens identifié et efficient pouvant orienter et appuyer, tant par
                                    le conseil que par la recommandation, ses membres.</li>
                                <li>Contribuer, par l’information à ses membres,à la présence des anciens de la FAGE au sein
                                    des syndicats et associations professionnelles, structures de l’ESS, mouvements citoyens
                                    et politiques.</li>
                            </ul>
                        </li>
                        <li><strong class="text-fage-500">Une AAAF, soutien au développement de la FAGE et ses élus</strong>
                            <ul class="list-disc list-inside m-1 p-1">
                                <li>Accompagner les élus sortants de FAGE en organisant un «tutorat» de qualité.</li>
                                <li>Etre disponible aux différentes sollicitations des élus du bureau de la FAGE et les
                                    faire profiter des différentes expertises présentes au sein du réseau des anciens.</li>
                                <li>Promouvoir la présence des anciens associatifs au sein des Alumnis.</li>
                                <li>Soutenir les projets de la FAGE à travers une campagne de dons et/ou crowdfunding auprès
                                    des anciens.</li>
                                <li>Faciliter les échanges avec les associations d’anciens de fédérations locales existantes
                                    et favoriser dans la mesure du possible la création d’associations d’anciens des
                                    fédérations locales.</li>
                            </ul>
                        </li>
                    </ul>
                </section>
                <hr class="m-4 text-fage-500">
                <section class="scroll-mt-30" id="ag">
                    <h2 class="text-2xl text-fage-600 font-bold m-1 p-1">L'Assemblée Générale</h2>
                    <div class="flex flex-wrap gap-4 items-center m-1 p-1">
                        <img src="assets/image/ag-aaaf.jpeg" alt="Photo de l'Assemblée Générale de l'AAAF" height="210"
                            width="280">
                        <div class="max-w-2xl">
                            <p class="m-0.5 p-0.5">L’Assemblée Générale de l’AAAF se réunit une fois par an. Cette année,
                                elle a eu lieu en janvier à Paris.</p>
                            <p class="m-0.5 p-0.5">Ce fut l’occasion pour la quarantaine de membres présents d’établir le
                                bilan de l’année écoulée et de fixer le cap des prochaines actions de l’AAAF. A cette
                                occasion, un nouveau bureau porté par Auréliano Boccasile a été élu.</p>
                            <p class="m-0.5 p-0.5">Les adhérents ont également pu rappeler leur attachement à l’association
                                et leur envie de soutenir des projets permettant de favoriser la mise en réseau et le
                                sentiment d’amitié entre ses membres, tout en offrant la possibilité d’être une oreille
                                bienveillante auprès des futures générations de jeunes militants.</p>
                        </div>
                    </div>
                </section>
                <hr class="m-4 text-fage-500">
                <section class="scroll-mt-30" id="bureau">
                    <h2 class="text-2xl text-fage-600 font-bold m-1 p-1">Le Bureau de l'AAAF</h2>
                    <img src="assets/image/bureau-aaaf.png"
                        alt="Photo de la liste des membres du bureau de l'AAAF depuis 2019">
                </section>
                <hr class="m-4 text-fage-500">
                <section class="scroll-mt-30 bg-fage-100 rounded-xl self-start w-fit h-fit" id="contacts">
                    <h2 class="text-2xl text-fage-600 font-bold m-1 p-1">Contacts</h2>
                    <ul class="p-1 m-1">
                        <li>aaaf.contact@gmail.com</li>
                        <li><a href="https://www.facebook.com/groups/4775882590/?fref=ts" target="_blank"
                                class="flex flex-wrap gap-2 items-center">Facebook <img alt="lien externe" class="w-3 h-3"
                                    height="3" src="assets/image/lien-externe.png" width="3" /></a></li>
                        <li><a href="https://www.linkedin.com/groups/8226940" target="_blank"
                                class="flex flex-wrap gap-2 items-center">LinkedIn <img alt="lien externe" class="w-3 h-3"
                                    height="3" src="assets/image/lien-externe.png" width="3" /></a></li>
                    </ul>

                </section>
            </article>
        </div>

    </main>

    <?php
    require_once __DIR__ .  "/../templates/footer.php";
    ?>
</body>