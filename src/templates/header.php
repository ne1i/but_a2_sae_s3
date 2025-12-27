<header
    class="flex lg:items-center items-stretch justify-stretch lg:justify-between lg:flex-row flex-col sticky top-0 left-0 right-0 z-10 bg-white shadow-sm p-3 lg:p-0">
    <div class="flex items-center justify-between lg:justify-start">
        <button class="lg:hidden hover:bg-fage-100 active:bg-fage-200 p-2 rounded flex flex-col gap-1"
            title="Ouvrir/fermer le menu">
            <span class="sr-only">Menu</span>
            <span class="block w-8 h-1 bg-fage-400 rounded"></span>
            <span class="block w-8 h-1 bg-fage-400 rounded"></span>
            <span class="block w-8 h-1 bg-fage-400 rounded"></span>
        </button>

        <script>
            const button = document.querySelector("button");
            button.addEventListener("click", () => {
                button.classList.toggle("active");
                const nav = document.querySelector("nav");
                nav.classList.toggle("hidden");
            });
        </script>

        <a class href="/">
            <span class="hidden" aria-hidden="true">Retour à
                l'accueil</span>
            <img src="assets/image/Logo_FAGE.svg" alt="Logo_FAGE" class="lg:max-w-40 max-w-20 lg:m-2" />
        </a>
    </div>

    <nav class="justify-center hidden lg:flex active">
        <ul class="flex flex-1 flex-wrap gap-2 lg:flex-row flex-col items-stretch lg:items-center w-full lg:w-auto">
            <li class="relative group">

                <a href="/presentation">
                    <div
                        class="flex items-center justify-center cursor-pointer select-none bg-fage-500 hover:bg-fage-600 active:bg-fage-700 rounded-full px-4 py-2 text-white font-bold transition-colors duration-200">
                        La FAGE
                        <svg class="hidden lg:inline-block w-4 h-4 ml-2 transition-transform duration-300 group-hover:rotate-180"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </a>


                <ul
                    class="absolute left-0 mt-2 w-60 bg-fage-500 rounded-xl overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-20 shadow-lg">
                    <li>
                        <a href="/presentation"
                            class="block px-4 py-2 text-white hover:bg-fage-600 border-b border-fage-400 transition-colors duration-200">
                            Présentation
                        </a>
                    </li>
                    <li>
                        <a href="/organisation"
                            class="block px-4 py-2 text-white hover:bg-fage-600 border-b border-fage-400 transition-colors duration-200">
                            Organisation
                        </a>
                    </li>
                    <li>
                        <a href="https://www.fage.org/les-assos-etudiantes/federations-fage/federations-annuaire/"
                            target="_blank"
                            class="block px-4 py-2 text-white hover:bg-fage-600 border-b border-fage-400 transition-colors duration-200">
                            Les associations membres
                        </a>
                    </li>
                    <li>
                        <a href="/aaaf"
                            class="block px-4 py-2 text-white hover:bg-fage-600 transition-colors duration-200">
                            L'AAAF
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/Nos_actions"
                    class="block text-center bg-fage-500 hover:bg-fage-600 active:bg-fage-700 py-2 rounded-full px-4 text-white font-bold">Nos
                    actions</a>
            </li>
            <li>
                <a href="/actu"
                    class="block text-center bg-fage-500 hover:bg-fage-600 active:bg-fage-700 py-2 rounded-full px-4 text-white font-bold">Actualités</a>
            </li>

            <li>
                <a href="/espace_asso"
                    class="block text-center bg-fage-500 hover:bg-fage-600 active:bg-fage-700 py-2 rounded-full px-4 text-white font-bold">Espace
                    association</a>
            </li>
            <li>
                <a href="https://www.helloasso.com/associations/federation-des-associations-generales-etudiantes-fage/formulaires/1"
                    target="_blank"
                    class=" block text-center bg-amber-500 hover:bg-amber-600 active:bg-amber-700 py-2 rounded-3xl px-4 text-white font-bold shadow-md">Faire
                    un don</a>
            </li>
            <li><a href="/contact"
                    class="block text-center bg-amber-500 hover:bg-amber-600 active:bg-amber-700 py-2 rounded-3xl px-4 text-white font-bold shadow-md">Contact</a>
            </li>
        </ul>
    </nav>


    <img src="assets/image/Logo_FAGE.svg" alt="Logo_FAGE"
        class="invisible lg:max-w-40 max-w-20 m-2 lg:block hidden" />


</header>