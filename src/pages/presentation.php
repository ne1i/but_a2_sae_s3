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
                    const button = document.getElementById("sidePanelBtn");
                    const sidePanel = document.getElementById("sidePanel");

                    button.addEventListener("click", function() {
                        sidePanel.classList.toggle("-translate-x-full");
                        button.children[0].classList.toggle("rotate-180");
                    });
                });
            </script>

            <!-- MOBILE -->
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
                    <li class="border-b text-fage-600 hover:text-fage-900">
                        <a href="#presentation" class="p-2 mb-2 inline-block text-xl">Qui sommes-nous ?</a>
                    </li>
                    <li class="border-b text-fage-600 hover:text-fage-900">
                        <a href="#instances" class="p-2 mb-2 inline-block text-xl">La FAGE dans les instances</a>
                    </li>
                </ul>
            </div>

            <!-- DESKTOP -->
            <div
                class="py-10 pt-5 px-5 mr-4 w-64 bg-fage-50 rounded-2xl mt-10 lg:block hidden md:items-start fixed top-1/8 left-1/10">
                <ul>
                    <li class="border-b text-fage-600 hover:text-fage-900">
                        <a href="#presentation" class="p-2 mb-2 inline-block">Qui sommes-nous ?</a>
                    </li>
                    <li class="border-b text-fage-600 hover:text-fage-900">
                        <a href="#instances" class="p-2 mb-2 inline-block">La FAGE dans les instances</a>
                    </li>
                </ul>
            </div>

        </aside>

        <div id="presentation" class="scroll-mt-32 flex justify-center mb-30 lg:w-10/12 mx-auto">
            <article class="lg:mt-8 lg:w-10/12 text-lg p-10 lg:p-0">

                <h1 class="text-4xl text-fage-500 font-bold m-4 p-4">Qui sommes-nous ?</h1>

                <p class="m-0.5 p-0.5">
                    La FAGE, Fédération des Associations Générales Etudiantes, est une association ayant
                    pour but d’améliorer les conditions de vie et d’études des jeunes.
                </p>

                <hr class="m-4 text-fage-500">

                <section class="bg-fage-100 rounded-xl p-2 self-start w-fit h-fit">
                    <h2 class="text-2xl text-fage-600 font-bold m-1 p-1">La FAGE en bref.</h2>
                    <iframe
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                        class="rounded-xl w-full aspect-video"
                        referrerpolicy="strict-origin-when-cross-origin"
                        src="https://www.youtube.com/embed/G6Egva74tiI?si=anIUM15Bd4bL5QiF"
                        title="YouTube video player">
                    </iframe>
                </section>

                <p class="m-0.5 p-0.5">La Fédération des Associations Générales Etudiantes – FAGE – est <strong class="text-fage-500">la première organisation étudiante représentative en France</strong>. Fondée
                    en 1989, elle assoit son fonctionnement sur la démocratie participative. Elle regroupe près de 2 000
                    associations et syndicats, via des fédérations territoriales et de filière, soit environ 300 000 jeunes.

                </p>

                <p class="m-0.5 p-0.5">
                    La FAGE a pour but d’<strong class="text-fage-500">améliorer les conditions de vie et
                        d’études</strong> des jeunes. C’est pourquoi elle déploie des activités dans le champ de la <strong class="text-fage-500">représentation</strong> et de la <strong class="text-fage-500">défense des
                        droits</strong>. En inventant, en expérimentant et en gérant des services et des œuvres répondant
                    aux besoins sociaux non ou mal-satisfaits, elle est également actrice de l’<strong class="text-fage-500">innovation sociale</strong>.

                </p>

                <p class="m-0.5 p-0.5">
                    La FAGE est <strong class="text-fage-500">indépendante</strong> des partis
                    politiques, des syndicats professionnels et des mutuelles étudiantes. Militante et pluraliste, elle base
                    ses actions sur des <strong class="text-fage-500">valeurs humanistes, républicaines et
                        européennes</strong>. Partie prenante de l’économie sociale et solidaire, non lucrative, elle est
                    par ailleurs agréée jeunesse et éducation populaire par l’Etat.
                </p>

                <p class="m-0.5 p-0.5">
                    Pour leur donner le pouvoir d'agir, la FAGE forme chaque année des milliers de jeunes
                    bénévoles et volontaires. À travers elle, les jeunes trouvent un formidable outil citoyen pour débattre,
                    entreprendre des projets et prendre des responsabilités dans la société.
                </p>

                <hr class="m-4 text-fage-500">

                <section class="flex flex-wrap gap-3 items-center">
                    <div class="bg-fage-100 rounded-xl self-start w-fit h-fit">
                        <a class="flex flex-wrap gap-2 items-center m-1 p-1"
                            href="https://www.fage.org/ressources/documents/3/5850-FAGE-rapport-2018-BD.pdf">
                            Rapport d'activité
                            <img src="assets/image/lien-externe.png" alt="icône lien externe" class="w-4 h-4">
                        </a>
                    </div>

                    <div class="bg-fage-100 rounded-xl self-start w-fit h-fit">
                        <a class="flex flex-wrap gap-2 items-center m-1 p-1"
                            href="https://www.fage.org/ressources/documents/2/2058,Projet-educatif_A5X3-V2.pdf">
                            Projet éducatif
                            <img src="assets/image/lien-externe.png" alt="icône lien externe" class="w-4 h-4">
                        </a>
                    </div>
                </section>

            </article>
        </div>

        <div id="instances" class="scroll-mt-32 flex justify-center lg:w-10/12 mx-auto mb-10">

            <article class="lg:mt-8 lg:w-10/12 text-lg p-10 lg:p-0">

                <h1 class="text-4xl text-fage-500 font-bold m-4 p-4">La FAGE dans les instances</h1>

                <p class="m-0.5 p-0.5">
                    La FAGE est la principale interlocutrice des pouvoirs publics et de la société civile
                    lorsqu'il s'agit des études supérieures, de la vie étudiante et de l'engagement des jeunes. Organisation
                    étudiante représentative aux termes de la loi n°89-486 loi du 10 juillet 1989, la FAGE siège dans
                    plusieurs instances régionales, nationales et internationales. Elle est partie prenante de nombreux
                    collectifs interassociatifs.
                </p>

                <hr class="m-4 text-fage-500">

                <section>

                    <h2 class="text-2xl text-fage-600 font-bold m-1 p-1">Les instances françaises de droit public</h2>
                    <ul class="list-disc list-inside">
                        <li><a href="https://www.lecese.fr/" target="_blank">Le Conseil Economique, Social et
                                Environnemental (CESE)</a></li>
                        <li><a href="https://www.lescrous.fr/les-crous/le-cnous/" target="_blank">Centre National des
                                Oeuvres Universitaires et Scolaires (Cnous)</a></li>
                        <li><a href="https://www.enseignementsup-recherche.gouv.fr/fr/le-conseil-national-de-l-enseignement-superieur-et-de-la-recherche-cneser-87955" target="_blank">Conseil National de l'Enseingement Supérieur Et de la Recherche (CNESER)</a>
                        </li>
                        <li><a href="https://www.education.gouv.fr/les-organismes-consultatifs-9314#:~:text=Le%20Conseil%20sup%C3%A9rieur%20de%20l'%C3%A9ducation%20(CSE)%20est%20une,%C3%89tat%20dans%20l'action%20%C3%A9ducative." target="_blank">Conseil Supérieur de l'Education (CSE)</a></li>
                        <li><a href="https://www.ove-national.education.fr/" target="_blank">L'Obervatoire de la Vie
                                Etudiante (OVE)</a></li>
                        <li><a href="https://www.onisep.fr/" target="_blank">L'Office National d'Information Sur les
                                Enseignements et les Professions (ONISEP)</a></li>
                        <li><a href="https://www.campusfrance.org/fr/conseil-d-orientation-de-campus-france-retour-sur-10-ans-d-actions" target="_blank">Le conseil d'orientation de l'agence campus france</a></li>
                        <li><a href="https://www.diplomatie.gouv.fr/fr/politique-etrangere-de-la-france/societe-civile-et-volontariat/le-conseil-national-pour-le-developpement-et-la-solidarite-internationale-cndsi/#sommaire_2" target="_blank">Le Conseil National pour le Développement et la Solidarité Internationale
                                (CNDSI)</a></li>
                        <li><a href="https://www.jeunes.gouv.fr/COJ" target="_blank">Le Conseil d'Orientation des politiques
                                de Jeunesse (COJ)</a></li>
                        <li><a href="https://www.defense.gouv.fr/sga/commission-armees-jeunesse-caj" target="_blank">La
                                Commission Armées-Jeunesse (CAJ)</a></li>
                        <li><a href="https://fr.wikipedia.org/wiki/Conseil_sup%C3%A9rieur_de_l%27%C3%A9ducation_routi%C3%A8re" target="_blank">Le Conseil Supérieur de l'Education Routière (CSER)</a></li>
                    </ul>

                </section>

                <section>

                    <h2 class="text-2xl text-fage-600 font-bold m-1 p-1">Les instances françaises de droit privé</h2>
                    <ul class="list-disc list-inside">
                        <li><a href="https://www.cnajep.asso.fr/" target="_blank">Le Comité pour les relations Nationales et
                                internationales des Associations de Jeunesse et d'Education Populaire (CNAJEP)</a></li>
                        <li><a href="https://www.ucpa.com/" target="_blank">L'UCPA</a></li>
                        <li><a href="https://www.pactedupouvoirdevivre.fr/" target="_blank">Le pacte du pouvoir de vivre</a>
                        </li>
                        <li><a href="https://www.fsef-sante-etudes.fr/" target="_blank">La Fondation Santé des Etudiants de
                                France (FSEF)</a></li>
                    </ul>

                </section>

                <section>

                    <h2 class="text-2xl text-fage-600 font-bold m-1 p-1">Les instances internationales</h2>
                    <ul class="list-disc list-inside">
                        <li><a href="https://esu-online.org/" target="_blank">L'European Students' Union (ESU)</a></li>
                        <li><a href="https://mednetstudents.wordpress.com/" target="_blank">Le Réseau Méditerranéen des
                                Représentants Etudiants (MedNet)</a></li>
                    </ul>

                </section>

            </article>
        </div>

    </main>
    <?php
    require_once __DIR__ .  "/../templates/footer.php";
    ?>
</body>