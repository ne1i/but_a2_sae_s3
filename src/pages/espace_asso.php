<!DOCTYPE html>
<html lang="fr"><?php
                require_once __DIR__ .  "/../templates/head.php";
                ?>

<body>
    <?php
    require_once __DIR__ .  "/../templates/header.php";
    ?>



    <main class="lg:mt-8 lg:w-10/12  lg:p-0 lg:ml-[250px] relative flex-1">

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
                        <a href="#asso-ancien" class="  p-2 mb-2 inline-block text-xl">Créer son association
                            d'anciens</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a href="#financer-projet" class="  p-2 mb-2 inline-block text-xl">Financer ses projets</a>
                    </li>


                </ul>
            </div>

            <div
                class=" py-10 pt-5 px-5 mr-4 w-64 bg-fage-50 rounded-2xl mt-10 lg:block hidden md:items-start fixed top-1/8 left-1/10 ">
                <ul>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a href="#asso-ancien" class=" p-2 mb-2 inline-block">Créer son association
                            d'anciens</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a href="#financer-projet" class="  p-2 mb-2 inline-block">Financer ses projets</a>
                    </li>
                </ul>
            </div>
        </aside>

        <div id="asso-ancien" class="scroll-mt-32 flex justify-center mb-30  lg:w-10/12 mx-auto">


            <article class=" lg:mt-8 lg:w-10/12 text-lg p-10 lg:p-0">
                <h1 class="text-4xl text-fage-500 font-bold text-shadow-md  ml-8">Votre asso d'anciens : créez-la,
                    rejoignez-la !
                </h1>
                <section class=" mt-4">
                    Les associations d'anciens étudiants se développent activement dans les établissements
                    d'Enseignement Supérieur. A l'heure où l'insertion professionnelle est une réelle problématique,
                    la
                    création et le développement de ce type de structure deviennent indispensables.
                </section>
                <hr class="my-4">
                <div class="flex flex-col-reverse lg:flex-row items-center">
                    <a href="https://www.fage.org/ressources/documents/1/801,FAGE_Kit-Associations-et-Reseaux-d-a.pdf"
                        class="lg:mr-8 lg:my-0 my-6">
                        <img src="assets/image/kit.png" alt="Kit de mobilisation" width="1000"
                            class="object-cover w-100 lg:w-auto">
                    </a>
                    <section>
                        Au-delà de la simple perspective de création d'un réseau d'entraide, cela permet de défendre
                        des
                        valeurs communes, de défendre, promouvoir, et de s'identifier autour de l'établissement, du
                        diplôme.
                        Tout en mettant en relation les anciens et actuels étudiants/élèves, cela assure la
                        promotion et
                        l'image des diplômes, et cursus préparés, aux yeux des professionnels.
                        <hr class="border-0 size-4">
                        Il paraissait essentiel de mettre en place un
                        <a href="https://www.fage.org/ressources/documents/1/801,FAGE_Kit-Associations-et-Reseaux-d-a.pdf"
                            class="text-fage-600 hover:text-fage-900 hover:underline">
                            kit de mobilisation
                        </a>,
                        <strong class="font-bold">permettant de donner les
                            clefs de
                            base pour la création et le fonctionnement de ces associations.</strong>
                        <hr class="border-0 size-4">
                        L'axe de travail de cette campagne est de faire prendre conscience et de sensibiliser les
                        associatifs étudiants, mais surtout plus largement les étudiants à l'utilité de ce type de
                        structure, et de leur donner les bases et les moyens de créer et développer une association
                        d'anciens étudiants / élèves.
                    </section>
                </div>
                <section class="bg-fage-100 rounded-2xl p-6 mt-6">

                    <h2 class="text-2xl text-fage-600 font-bold">
                        Ce que comprend le kit de la campagne
                    </h2>
                    <ul class="list-disc pl-6 py-2">
                        <li class="mb-4">
                            Un <strong class="font-bold">kit de mobilisation en téléchargement</strong> relatant les
                            moyens
                            et les éléments de base à
                            utiliser et
                            mettre en place pour la création et le développement d'association d'anciens étudiants /
                            élèves dans
                            votre établissement.
                        </li>
                        <li>
                            Une affiche (à imprimer) permettant de communiquer et de faire la promotion des
                            associations
                            d'anciens étudiants.
                        </li>
                    </ul>
                </section>
            </article>
        </div>

        <div id="financer-projet" class="scroll-mt-32 flex justify-center lg:w-10/12 mx-auto mb-10">

            <article class="lg:mt-8 lg:w-10/12 text-lg p-10 lg:p-0">
                <h1 class="text-4xl text-fage-500 font-bold text-shadow-md ml-8">Financer ses projets associatifs
                </h1>
                <section class=" mt-4">
                    <h2 class="mb-4 text-2xl text-fage-400">
                        Le FIRF : un fonds pour les projets du réseau de la FAGE
                    </h2>
                    <h3 class="text-xl text-fage-400 mb-4">
                        Vous cherchez à financer vos projets et vous faites partie du grand réseau de la FAGE ?
                    </h3>

                    <p>Le <strong class="font-bold">Fonds des Initiatives du Réseau de la FAGE</strong> (FIRF) a
                        pour
                        but de favoriser l'essaimage
                        d'innovations sociales des associations étudiantes.</p>
                    <p>Pour qu'une association du réseau de la FAGE puisse bénéficier d'une aide financière du FIRF,
                        elle doit :</p>
                    <ul class="list-disc ml-8 my-6">
                        <li>Pouvoir justifier de son adhésion à un membre actif du collège A ou du collège B</li>
                        <li>Présenter un <strong class="font-bold">projet innovant et ayant une plus-value
                                sociale</strong> dans un des domaines d'actions
                            de la FAGE : innovation sociale, culture, citoyenneté, économie sociale et solidaire,
                            transition écologique, lutte contre les discriminations et les inégalités ou encore
                            citoyenneté.</li>
                    </ul>
                    <p>Ces actions doivent toucher un maximum d'étudiant.e.s, et contribuer au rayonnement de la
                        structure demandeuse, de la FAGE et de l'ensemble de son réseau. Elles doivent en respecter
                        les
                        valeurs et en aucun cas lui porter préjudice.</p>
                    <p>Le FIRF vise à soutenir les projets des associations étudiantes, notamment pour les
                        accompagner
                        dans la réalisation de leurs projets. C'est aussi un moyen de se former à un dépôt de
                        demande de
                        subvention dans le cadre d'un appel à projets ou d'une demande de financement privé. À ce
                        titre,
                        l'équipe du FIRF se tient à disposition des bénévoles préparant une candidature pour les
                        accompagner dans leur montée en compétences.</p>
                </section>
                <hr class="my-4">

                <section class="bg-fage-100 rounded-2xl p-6 mt-6 ">
                    <h2 class="text-2xl text-fage-600 font-bold mb-4">
                        Le fonctionnement du FIRF
                    </h2>
                    <p>Le FIRF est alloué par une commission composée de :</p>
                    <ul class="list-disc pl-8 py-6">
                        <li>2 membres du Bureau National de la FAGE, dont le.la trésorier.ère</li>
                        <li>1 membre de fédération adhérente au <strong class="font-bold">collège A</strong> -
                            administrativement e financièrement à jour
                            auprès de la FAGE - élu.e par le Conseil d'Administration de la FAGE</li>
                        <li>1 membre de fédération adhérente au <strong class="font-bold">collège B</strong> -
                            administrativement et financièrement à jour
                            auprès de la FAGE - élu.e par le Conseil d'Administration de la FAGE.</li>
                    </ul>
                    <p class="mb-4">Ces deux dernier.ère.s sont élu.e.s pour une durée d'un an, en même temps que
                        les
                        membres des
                        autres commissions de la FAGE, lors du premier Conseil d'Administration du mandat. Ces deux
                        membres ne pourront pas statuer sur les éventuels dossiers proposés par les associations
                        adhérentes à la fédération à laquelle ils appartiennent.</p>
                    <p><strong class="font-bold">Les associations peuvent candidater au FIRF pendant l'ensemble de
                            l'année civile.</strong>La date de dépôt de dossier doit être antérieure à 1 mois à la
                        réalisation du projet. La commission se réunit au moins 2 fois par semestre pour traiter les
                        dossiers et allouer les aides financières aux associations. Quand, suite à la candidature
                        d'une
                        association, la commission lui demande des
                        documents complémentaires, cette dernière dispose d'une semaine pour les envoyer à la
                        commission. Si ce délai n'est pas respecté, la commission se réserve le droit de réallouer
                        l'aide financière à d'autres projets.</p>
                    <p>L'aide financière allouée par le FIRF pour un projet ne peut pas dépasser 1000€ (dans la
                        limite
                        de 50% du projet) et doit être cofinancé, qu'il soit porté par une ou plusieurs
                        associations.
                        Une association peut proposer deux dossiers par an au maximum et ne pourra pas se faire
                        financer
                        plusieurs fois un même projet. Les dossiers devront être accompagnés de :</p>
                    <ul class="list-disc ml-8 my-6">
                        <li>Une présentation détaillée du projet</li>
                        <li>Un bilan d'activité de la structure</li>
                        <li>Un budget prévisionnel détaillé et équilibré du projet</li>
                        <li>Un RIB</li>
                        <li>Tous les devis correspondants aux dépenses relatives au projet de l'association ; et -
                            Tout
                            élément jugé opportun par la structure (comme le bilan d'une éventuelle édition
                            précédente,
                            le plan de communication, etc.)</li>
                    </ul>
                    <p class=""><strong class="font-bold">Candidature au FIRF FAGE - 2023 :</strong> <a class="text-fage-500 break-all hover:underline hover:text-fage-900
                            " target="_blank"
                            href="https://forms.office.com/e/gafLKnTfag">https://forms.office.com/e/gafLKnTfag</a>
                    </p>
                    <p>Si vous souhaitez plus d'informations, vous pouvez contacter : <a
                            class="text-fage-500 hover:underline hover:text-fage-900"
                            href="mailto:tresorerie@fage.org">tresorerie@fage.org</a></p>

                </section>
                <div class="relative w-full overflow-hidden pt-[56.25%] mt-10 rounded-lg shadow-md">
                    <iframe src="https://www.fage.org/ressources/videos/1/8041-Presentation-FIRF.mp4"
                        class="absolute top-0 left-0 w-full h-full" frameborder="0" allowfullscreen>
                    </iframe>
                </div>
            </article>
        </div>


    </main>

    <?php
    require_once __DIR__ .  "/../templates/footer.php";
    ?>
</body>