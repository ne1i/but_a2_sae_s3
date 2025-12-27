<!DOCTYPE html>
<html lang="fr"><?php
                require_once __DIR__ .  "/../templates/head.php";
                ?>

<body>
    <?php
    require_once __DIR__ .  "/../templates/header.php";
    ?>
    <div class="flex mb-6  lg:w-9/12 mx-auto">
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const button = document.getElementById("sidePanelBtn");
                    const sidePanel = document.getElementById("sidePanel");

                button.addEventListener("click", function() {
                    sidePanel.classList.toggle("-translate-x-full");
                    button.children[0].classList.toggle("rotate-180");
                })
            })
        </script>


        <aside
            class=" py-10 pt-5 px-5 mr-4 w-64 bg-fage-50 rounded-2xl mt-10 lg:block hidden md:items-start fixed top-1/8 left-1/10 ">
            <ul class="">
                <li class="  border-b text-fage-600 hover:text-fage-900">
                    <a class="p-2 mb-2 inline-block" href="#CNESER">CNESER</a>
                </li>
                <li class=" border-b text-fage-600 hover:text-fage-900">
                    <a class="p-2 mb-2 inline-block " href="#social">Social</a>
                </li>
                <li class=" border-b text-fage-600 hover:text-fage-900">
                    <a class="p-2 mb-2 inline-block" href="#sante">Santé</a>

                </li>
                <li class=" border-b text-fage-600 hover:text-fage-900">
                    <a class="p-2 mb-2 inline-block" href="#eco">Ecologie</a>

                </li>

            </ul>
        </aside>

        <main class="lg:mt-8 lg:w-9/12  p-10 lg:p-0 lg:ml-[250px] relative flex-1 ">
            <div id="sidePanel"
                class="py-10 pt-5 px-5 mr-4  bg-fage-50 rounded-l-none rounded-tr-none  rounded-2xl  mt-10 left-0 top-30 h-fit fixed -translate-x-full transition duration-700 lg:hidden block">
                <button id="sidePanelBtn"
                    class="absolute top-0 left-full bg-fage-100 hover:bg-fage-200 rounded-r-2xl p-2 text-center lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="transition duration-700 w-10">
                        <path
                            d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z" />
                    </svg>
                </button>
                <ul class="">
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-2 mb-2 inline-block text-xl" href="#CNESER">CNESER</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-2 mb-2 inline-block text-xl " href="#social">Social</a>
                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-2 mb-2 inline-block text-xl" href="#sante">Santé</a>

                    </li>
                    <li class=" border-b text-fage-600 hover:text-fage-900">
                        <a class="p-2 mb-2 inline-block text-xl" href="#eco">Ecologie</a>

                    </li>

                </ul>
            </div>
            <article>
                <h1 id="CNESER" class="text-4xl text-fage-500 font-bold text-center  scroll-mt-32">CNESER</h1></br>

                <section class=" mt-4">
                    <h2 class="text-2xl text-fage-400 font-bold">Qu'est-ce que les élections CNESER ?</h2>
                    <p><strong>Le CNESER (Conseil National de l'Enseignement Supérieur et de la Recherche)</strong>
                        est un organe consultatif réunissant des représentant·e·s de la communauté universitaire.
                        Il examine les textes relatifs à l'enseignement supérieur et à la recherche :
                        <strong>encadrement des stages, budget, formation professionnelle, accréditation des
                            établissements, </strong>
                        Composé d'une centaine de membres, il se réunit en séance plénière ou en formation restreinte,
                        généralement une fois par mois.
                    </p>
                </section>
                <hr class="my-4">
                <section class="flex flex-col-reverse lg:flex-row items-center lg:mr-8 lg:my-0 my-6 ">

                    <img src="assets/image/elections-cneser.png" alt="election cneser" width="300" height="300"
                        class="object-cover ">

                    <div>
                        Il est notamment consulté sur :
                        <hr class="border-0 size-4">
                        <ul class="list-disc">
                            <li class="ml-6">Les stratégies nationales de l'enseignement supérieur et de la recherche et
                                les rapports
                                biennaux au Parlement</li></br>
                            <li class="ml-6">Les orientations générales des contrats pluriannuels et la répartition des
                                emplois et
                                des moyens entre les différents établissements</li></br>
                            <li class="ml-6">Les projets de réformes concernant l'organisation de la recherche et les
                                réformes
                                relatives à l'emploi scientifique</li></br>
                            <li class="ml-6">Le cadre national des formations, la liste des diplômes nationaux, les
                                modalités et
                                demandes d'accréditation
                                ainsi que la carte des formations supérieures et de la recherche prévus à l'article
                            </li></br>
                            <li class="ml-6">La création, la suppression ou le regroupement d'établissements ou de
                                composantes </li>
                            </br>
                        </ul>
                    </div>
                </section>
                <hr class="my-4">
                <h2 class="text-2xl text-fage-400 font-bold">Qui compose le CNESER ?</h2>
                <img src="assets/image/compo-cneser.png" alt="election cneser" class="mx-auto ">
                <hr class="my-4">
                <section class="block gap-20 lg:flex-row items-center lg:mr-8">
                    <h2 class="text-2xl text-fage-400 font-bold">Nos livrets</h2></br>

                    <ul
                        class="lg:grid lg:grid-cols-2  lg:gap-20 flex-col-reverse lg:flex-row justify-center gap-20 mx-auto mb-20">
                        <a
                            href="https://www.fage.org/ressources/documents/source/1/8999-8999-PF-LONGUE-CNESER-25-27.pdf">
                            <li
                                class="bg-linear-to-r from-fage-700 to-fage-400  rounded-lg p-4 text-center m-7 lg:m-0 hover:font-bold hover:shadow-lg hover:-translate-y-1">
                                Profession de foi 25-27 </br>(full version)</li>
                        </a>
                        <a href="https://www.fage.org/ressources/documents/source/1/9022-PF-COURTE-CNESER-25-27.pdf">
                            <li
                                class="bg-linear-to-r from-fage-700 to-fage-400  rounded-lg p-4 text-center m-7 lg:m-0 hover:font-bold hover:shadow-lg hover:-translate-y-1">
                                Profession de foi 25-27 </br>(short version)</li>
                        </a>

                    </ul>
                    <ul
                        class="lg:grid lg:grid-cols-3 lg:col-end-2  lg:gap-20 flex-col-reverse lg:flex-row justify-center gap-20 mx-auto ">
                        <a href="https://www.fage.org/ressources/documents/source/1/9006-BILAN-CNESER-23-25.pdf">
                            <li
                                class="bg-linear-to-r from-amber-400 to-amber-800 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Bilan 2023-2025</li>
                        </a>
                        <a
                            href="https://www.fage.org/ressources/documents/source/1/9001-LIVRETS-PF-CNESER-25-27.pdf.pdf">
                            <li
                                class="bg-linear-to-r from-violet-300 to-violet-700 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret Ingénieur</li>
                        </a>
                        <a
                            href="https://www.fage.org/ressources/documents/source/1/8998-Livrets-20PF-20CNESER-20-203e-20cyc.pdf">
                            <li
                                class="bg-linear-to-r from-green-400 to-green-700 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret Doctorat</li>
                        </a>
                        <a href="https://www.fage.org/ressources/documents/source/1/9024-Livret-TE-CNESER-25-27.pdf">
                            <li
                                class="bg-linear-to-r from-emerald-400 to-emerald-800 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret climat</li>
                        </a>
                        <a href="https://www.fage.org/ressources/documents/source/1/9026-LIVRET-LCD-25-27.pdf">
                            <li
                                class="bg-linear-to-r from-red-800 to-rose-600 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret Discrimination</li>
                        </a>
                        <a href="https://www.fage.org/ressources/documents/source/1/8997-LIVRETS-PF-CNESER-Sante.pdf">
                            <li
                                class="bg-linear-to-r from-fuchsia-500 to-fuchsia-800 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret Santé</li>
                        </a>
                        <a href="https://www.fage.org/ressources/documents/source/1/8993-LIVRETS-PF-CNESER-allshs.pdf">
                            <li
                                class="bg-linear-to-r from-yellow-100 to-yellow-400 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret ALLSHS</li>
                        </a>
                        <a
                            href="https://www.fage.org/ressources/documents/source/1/9011-LIVRETS-PF-CNESER-Psychologie.pdf">
                            <li
                                class="bg-linear-to-r from-lime-400 to-lime-800 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret Psychologie</li>
                        </a>
                        <a href="https://www.fage.org/ressources/documents/source/1/8995-LIVRET-OUTRE-MER-25-27.pdf">
                            <li
                                class="bg-linear-to-r from-cyan-600 to-cyan-300 hover:font-bold rounded-lg p-4 text-center  m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret Outre-mer</li>
                        </a>
                        <a
                            href="https://www.fage.org/ressources/documents/source/1/8990-LIVRETS-PF-CNESER-grandes-ecoles.pdf">
                            <li
                                class="bg-linear-to-r from-orange-400 to-orange-800 hover:font-bold rounded-lg p-4 text-center  m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret Grande Ecole</li>
                        </a>
                        <a
                            href="https://www.fage.org/ressources/documents/source/1/8992-LIVRETS-PF-CNESER-classe-preparatoi.pdf">
                            <li
                                class="bg-linear-to-r from-red-300 to-orange-600 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret CPGE</li>
                        </a>
                        <a href="https://www.fage.org/ressources/documents/source/1/8991-LIVRETS-PF-CNESER-IUT.pdf">
                            <li
                                class="bg-linear-to-r from-indigo-600 to-sky-400 hover:font-bold rounded-lg p-4 text-center m-7 lg:m-0 hover:shadow-lg hover:-translate-y-1">
                                Livret IUT</li>
                        </a>

                    </ul>



                </section>
                </br>
                <section class="bg-fage-100 rounded-2xl p-6 mt-6">
                    <h2 class="text-2xl text-fage-600 font-bold">
                        Nos autres contributions
                    </h2>
                    <ul class="list-disc pl-6 py-2">
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8979-Contribution-FAGE-VEE-2025.pdf">2025
                                - Valorisation de l'engagement étudiant</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8975-Contribution-FAGE-Re-gulation-ESR-p.pdf">2025
                                - Régulation de l'ESR privé</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8985-Note-Re-forme-FDE.pdf">2024
                                - Note réforme FDE</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8981-Contribution-FAGE-Urgence-e-cologiq.pdf">2024
                                - Urgence écologique dans l'ESR</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8978-Contribution-FAGE-OIP-2024.pdf">2024
                                - Orientation et Insertion Professionnelle</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8983-Contribution-FAGE-Financement-ESR-2.pdf">2024
                                - Financement de l'ESR</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8976-Contribution-FAGE-Mon-Master-2024.pdf">2024
                                - Mon Master</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8984-Contribution-Sante-Mentale-FAGE-202.pdf">2024
                                - Santé mentale</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8986-Contribution-FAGE-Parcoursup-2023.pdf">2024
                                - Accès à la santé</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8986-Contribution-FAGE-Parcoursup-2023.pdf">2023
                                - Parcoursup</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8977-Contribution-FAGE-PEC-2023.pdf">2023
                                - Parcours d'Engagement et de Citoyenneté</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8982-Contribution-FAGE-De-mocratie-2023.pdf">2023
                                - La démocratie au défi des jeunes</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/9020-Contribution-FAGE-LCD-Lutte-contre-.pdf">2023
                                - Lutte contre les discriminations et violences associées</a> </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/9021-Contribution-FAGE-100H-Inclusion-PS.pdf">2022
                                - Inclusion des personnes en situation de handicap</a> (avec 100% Handinamique) </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8989-8989-Enquete-FAGE-Doctorat-2022.pdf">2022
                                - 3ème cycle et doctorat </a>(résultats d'enquête) </li>
                        <li class="p-1"><a
                                href="https://www.fage.org/ressources/documents/source/1/8988-8989-Enquete-FAGE-Doctorat-2022.pdf">2022
                                - 3ème cycle et doctorat </a>(contribution) </li>

                    </ul>



                </section>
            </article>
            <hr class="my-4">
            <article>
                <h1 id="social" class="text-4xl text-fage-500 font-bold text-center scroll-mt-32 ">Social</h1></br>
                <section>
                    <h2 class="text-2xl text-fage-400 font-bold">Nos idées à propos du Crous</h2>
                    <ol class="flex flex-wrap pl-6 py-2 gap-6 justify-center">

                        <li class="flex items-center lg:basis-[40%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Le CROUS doit <strong>centraliser</strong> toutes les aides sociales pour un accès plus
                                <strong>juste</strong> et <strong>lisible</strong> aux droits des étudiants.
                            </p>


                        </li>
                        <li class="flex items-center lg:basis-[40%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Transférer la gestion des étudiants en <strong>formations sanitaires et sociales</strong>
                                aux CROUS <strong>harmoniser</strong> leurs critère</p>

                        </li>
                    </ol>
                    <h2 class="text-2xl text-fage-400 font-bold">Nos idées sur l'accès au logement</h2>
                    <ol class="flex flex-wrap pl-6 py-2 gap-6 justify-center">

                        <li class="flex items-center lg:basis-[40%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Développer massivement le parc de logements étudiants, faciliter la colocation et étendre
                                le dispositif VISALE</p>

                        </li>
                        <li class="flex items-center lg:basis-[40%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Réformer les aides au logement et exonérer la taxe d'habitation pour réduire les freins
                                financiers à l'autonomie</p>
                        </li>
                        <li class="flex items-center lg:basis-[40%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Encadrer les loyers dans les zones tendues et adapter les aides au logement aux besoins
                                des étudiants via un dossier unique </p>
                        </li>
                    </ol>
                    <h2 class="text-2xl text-fage-400 font-bold">Nos idées à propos de la restauration universitaire
                    </h2>
                    <ol class="flex flex-wrap pl-6 py-2 gap-6 justify-center">

                        <li class="flex items-center lg:basis-[40%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Garantir un accès équitable aux restaurants universitaires avec des tarifs sociaux
                                adaptés et un service étendu.</p>

                        </li>
                        <li class="flex items-center lg:basis-[40%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Développer une restauration durable, locale et respectueuse de l'environnement, en
                                sensibilisant les étudiants et en réduisant le gaspillage.</p>
                        </li>
                    </ol>


                </section>
                <section>
                    <h2 class="text-2xl text-fage-300 font-bold">Nos campagnes</h2>
                    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-fage-600 ">
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a
                                href="https://www.fage.org/innovation-sociale/campagnes-citoyennete/sois-jeune-et-tais-toi/">
                                <img class=" w-full" src="assets/image/SoisjeuneetTaistoi.png"
                                    alt="Sois jeune et tais toi">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center">

                                        Sois jeune et tais-toi ???
                                    </h2>
                                </div>
                            </a>
                        </li>
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a
                                href="https://www.fage.org/innovation-sociale/accessibilite-enseignement-superieur/lutter-contre-discriminations/">
                                <img class=" w-full" src="assets/image/AccessivilitéDansES.png" alt="Accessibilité">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center  ">

                                        Accessibilité dans l'Enseignement supérieur </h2>
                                </div>
                            </a>
                        </li>
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a href="https://www.fage.org/innovation-sociale/solidarite-etudiante/egalite-femme-homme/">
                                <img class=" w-full" src="assets/image/egaliteHF.png" alt="EgalitéHommeFemme">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center  ">

                                        Egalité Homme Femme </h2>
                                </div>
                            </a>
                        </li>
                    </ul>
                </section>
            </article>
            <hr class="my-4">

            <article>
                <h1 id="sante" class="text-4xl text-fage-500 font-bold text-center scroll-mt-32">Santé</h1></br>
                <section>
                    <h2 class="text-2xl text-fage-400 font-bold">Nos idées sur la santé</h2>
                    <ol class="flex flex-wrap pl-6 py-2 gap-6 justify-center">

                        <li class="flex items-center lg:basis-[45%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Transformer les SUMPPS en Centres de Santé Universitaires pour un accès centralisé aux
                                soins
                                et renforcer la prévention et l'innovation en santé sur les campus.</p>

                        </li>
                        <li class="flex items-center lg:basis-[45%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Assurer l'égalité d'accès aux soins et à la prévention pour tous les jeunes, via la prise
                                en charge intégrale des vaccins,
                                le tiers payant et la création d'un Dossier Numérique de Santé.</p>
                        </li>
                        <li class="flex items-center lg:basis-[30%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Transférer la gestion de la sécurité sociale étudiante de mutuelles étudiantes à la CNAM
                                pour garantir un accès aux soins plus efficace et fiable.</p>
                        </li>
                        <li class="flex items-center lg:basis-[30%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Définir une politique nationale de prévention appliquée localement
                                via les services universitaires de santé et le CROUS.</p>
                        </li>
                        <li class="flex items-center lg:basis-[30%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Instaurer un statut unique pour tous les étudiants, incluant stagiaires, alternants,
                                apprentis et étudiants étrangers, afin d'uniformiser l'accès au régime.</p>
                        </li>
                    </ol>
                </section>
                <section>
                    <h2 class="text-2xl text-fage-400 font-bold">Nos campagnes</h2>
                    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-fage-600 ">
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a href="https://www.fage.org/innovation-sociale/vivre-en-bonne-sante/sexe-et-chocolat/">
                                <img class=" w-full" src="assets/image/SexetChoco.png" alt="Sexetchocolat">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center">

                                        Sexe et chocolat </h2>
                                </div>
                            </a>
                        </li>
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a
                                href="https://www.fage.org/innovation-sociale/vivre-en-bonne-sante/sante-mentale/bouge-ton-blues.htm">
                                <img class=" w-full" src="assets/image/Bouge-ton-Blues.png" alt="bougetonblues">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center  ">
                                        Bouge ton Blues</h2>
                                </div>
                            </a>
                        </li>
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a
                                href="https://www.fage.org/innovation-sociale/vivre-en-bonne-sante/bien-dans-ton-assiette/">
                                <img class=" w-full" src="assets/image/bien-dans-ton-assiette.jpg"
                                    alt="Bien Dans Ton Assiette">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center  ">

                                        Bien dans ton assiette ! </h2>
                                </div>
                            </a>
                        </li>
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a
                                href="https://www.fage.org/innovation-sociale/vivre-en-bonne-sante/actions-de-lutte-contre-le-sida/">
                                <img class=" w-full" src="assets/image/sida.png" alt="Sida">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center  ">


                                        Actions de lutte contre le sida
                                    </h2>
                                </div>
                            </a>
                        </li>
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a
                                href="https://www.fage.org/innovation-sociale/vivre-en-bonne-sante/enfumage-acteur-prevention-tabac.htm">
                                <img class=" w-full" src="assets/image/enfumage.jpeg" alt="Enfumage">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center  ">


                                        L'Enfumage : focus sur le tabac et ses lobbies </h2>
                                </div>
                            </a>
                        </li>
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a href="https://www.fage.org/innovation-sociale/vivre-en-bonne-sante/sante-mentale/">
                                <img class=" w-full" src="assets/image/Psy.png" alt="Psy">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center  ">


                                        Santé mentale : les dispositifs de soutien </h2>
                                </div>
                            </a>
                        </li>
                    </ul>
                </section>
            </article>
            <hr class="my-4">
            <article>
                <h1 id="eco" class="text-4xl text-fage-500 font-bold text-center scroll-mt-32">Ecologie</h1></br>
                <section>
                    <h2 class="text-2xl text-fage-400 font-bold">Nos idées sur l'écologie</h2>
                    <ol class="flex flex-wrap pl-6 py-2 gap-6 justify-center">

                        <li class="flex items-center lg:basis-[45%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Mettre en place une stratégie écologique dans les CROUS : bilan carbone,
                                rénovation DPE A, transition écologique intégrée.</p>

                        </li>
                        <li class="flex items-center lg:basis-[45%] text-center bg-fage-50 rounded-2xl p-2">
                            <p>Favoriser la mobilité durable et l'alimentation responsable : covoiturage, menus
                                végétariens,
                                réduction du plastique, distribution des invendus.</p>
                        </li>

                    </ol>
                </section>
                <section>
                    <h2 class="text-2xl text-fage-400 font-bold">Nos campagnes</h2>
                    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-fage-600 ">
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a
                                href="https://www.fage.org/innovation-sociale/accessibilite-enseignement-superieur/developpement-durable-consommation-responsable/charte-engagement-developpement-durable-associations-etudiantes.htm">
                                <img class=" w-full" src="assets/image/Charte-DD.jpeg"
                                    alt="Charte développement durable">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center">

                                        La charte d'engagements : S'engager en faveur du développement durable
                                    </h2>
                                </div>
                            </a>
                        </li>
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a
                                href="https://www.fage.org/innovation-sociale/accessibilite-enseignement-superieur/developpement-durable-consommation-responsable/guide-technique-developpement-durable-associations-etudiantes.htm">
                                <img class=" w-full" src="assets/image/Guide-tech-DD.png"
                                    alt="Guide développement durable">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center  ">


                                        Le guide technique : Mettre en place des actions éco-responsables
                                    </h2>
                                </div>
                            </a>
                        </li>
                        <li
                            class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:text-fage-900">
                            <a
                                href="https://www.fage.org/innovation-sociale/accessibilite-enseignement-superieur/developpement-durable-consommation-responsable/consommation-reponsable-associations-etudiantes.htm">
                                <img class=" w-full" src="assets/image/guide_conso_responsable_fage.jpeg"
                                    alt="Conso responsable">

                                <div class="p-5">
                                    <h2 class="font-bold text-lg mb-3 text-center  ">


                                        La consommation responsable
                                    </h2>
                                </div>
                            </a>
                        </li>
                    </ul>
                </section>
            </article>

        </main>


    </div>

    <?php
    require_once __DIR__ .  "/../templates/footer.php";
    ?>
</body>