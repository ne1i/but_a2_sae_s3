<?php
require_once __DIR__ . "/../db.php";
$db = new FageDB();
require_once __DIR__ . "/../templates/admin_cookie_check.php";



require_once __DIR__ . "/../templates/admin_head.php";

?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen flex flex-col items-center justify-center">
    <div class="grid lg:grid-cols-3 grid-cols-2 lg:m-0 m-4 gap-2">
        <a <?php

            ?>
            id="benevoles" class="bg-white hover:bg-gray-300 lg:p-12 lg:py-16 py-12 px-2 text-center lg:text-2xl text-lg flex flex-col items-center gap-3 shadow-sm rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="lg:w-30 w-20" viewBox="0 0 640 640">
                <path d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z" />
            </svg>
            Adhérents et bénévoles</a>
        <a id="missions" class="bg-white hover:bg-gray-300 lg:p-12 lg:py-16 py-12 px-2 text-center lg:text-2xl text-lg flex flex-col items-center gap-3 shadow-sm rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="lg:w-30 w-20" viewBox="0 0 640 640">
                <path d="M224 64C206.3 64 192 78.3 192 96L192 128L160 128C124.7 128 96 156.7 96 192L96 240L544 240L544 192C544 156.7 515.3 128 480 128L448 128L448 96C448 78.3 433.7 64 416 64C398.3 64 384 78.3 384 96L384 128L256 128L256 96C256 78.3 241.7 64 224 64zM96 288L96 480C96 515.3 124.7 544 160 544L480 544C515.3 544 544 515.3 544 480L544 288L96 288z" />
            </svg>Missions et évènements</a>
        <a id="partenariats" class="bg-white hover:bg-gray-300 lg:p-12 lg:py-16 py-12 px-2 text-center lg:text-2xl text-lg flex flex-col items-center gap-3 shadow-sm rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="lg:w-30 w-20" viewBox="0 0 640 640">
                <path d="M305 151.1L320 171.8L335 151.1C360 116.5 400.2 96 442.9 96C516.4 96 576 155.6 576 229.1L576 231.7C576 343.9 436.1 474.2 363.1 529.9C350.7 539.3 335.5 544 320 544C304.5 544 289.2 539.4 276.9 529.9C203.9 474.2 64 343.9 64 231.7L64 229.1C64 155.6 123.6 96 197.1 96C239.8 96 280 116.5 305 151.1z" />
            </svg>Partenaires, subventions et <br> donateurs</a>
        <a id="communication-gestion" class="bg-white hover:bg-gray-300 lg:p-12 lg:py-16 py-12 px-2 text-center lg:text-2xl text-lg flex flex-col items-center gap-3 shadow-sm rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="lg:w-30 w-20" viewBox="0 0 640 640">
                <path d="M119.9 75.5C108.6 68.6 93.8 72.3 86.9 83.6C62.1 124.6 47.9 172.7 47.9 224C47.9 275.3 62.1 323.4 86.9 364.4C93.8 375.7 108.5 379.4 119.9 372.5C131.3 365.6 134.9 350.9 128 339.5C107.7 305.9 96 266.3 96 224C96 181.7 107.7 142.1 128.1 108.4C135 97.1 131.3 82.3 120 75.4zM520 75.5C508.7 82.4 505 97.1 511.9 108.5C532.3 142.2 544 181.8 544 224.1C544 266.4 532.3 306 511.9 339.7C505 351 508.7 365.8 520 372.7C531.3 379.6 546.1 375.9 553 364.6C577.8 323.6 592 275.5 592 224.2C592 172.9 577.8 124.6 553 83.6C546.1 72.3 531.4 68.6 520 75.5zM352 279.4C371.1 268.3 384 247.7 384 224C384 188.7 355.3 160 320 160C284.7 160 256 188.7 256 224C256 247.7 268.9 268.4 288 279.4L288 544C288 561.7 302.3 576 320 576C337.7 576 352 561.7 352 544L352 279.4zM212.2 155C219.4 143.8 216.1 129 205 121.8C193.9 114.6 179 117.9 171.8 129C154.2 156.4 144 189 144 224C144 259 154.2 291.6 171.8 319C179 330.2 193.8 333.4 205 326.2C216.2 319 219.4 304.2 212.2 293C199.4 273.1 192 249.4 192 224C192 198.6 199.4 174.9 212.2 155zM468.2 129C461 117.8 446.2 114.6 435 121.8C423.8 129 420.6 143.8 427.8 155C440.6 174.9 448 198.6 448 224C448 249.4 440.6 273.1 427.8 293C420.6 304.2 423.9 319 435 326.2C446.1 333.4 461 330.1 468.2 319C485.8 291.6 496 259 496 224C496 189 485.8 156.4 468.2 129z" />
            </svg>Communication interne et <br> gestion des contenus</a>
        <a id="statistiques" class="bg-white hover:bg-gray-300 lg:p-12 lg:py-16 py-12 px-2 text-center lg:text-2xl text-lg flex flex-col items-center gap-3 shadow-sm rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="lg:w-30 w-20" viewBox="0 0 640 640">
                <path d="M256 144C256 117.5 277.5 96 304 96L336 96C362.5 96 384 117.5 384 144L384 496C384 522.5 362.5 544 336 544L304 544C277.5 544 256 522.5 256 496L256 144zM64 336C64 309.5 85.5 288 112 288L144 288C170.5 288 192 309.5 192 336L192 496C192 522.5 170.5 544 144 544L112 544C85.5 544 64 522.5 64 496L64 336zM496 160L528 160C554.5 160 576 181.5 576 208L576 496C576 522.5 554.5 544 528 544L496 544C469.5 544 448 522.5 448 496L448 208C448 181.5 469.5 160 496 160z" />
            </svg>Statistiques et tableaux de bord</a>
        <a id="securite" href="/securite" class="bg-white hover:bg-gray-300 lg:p-12 lg:py-16 py-12 px-2 text-center lg:text-2xl text-lg flex flex-col items-center gap-3 shadow-sm rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="lg:w-30 w-20" viewBox="0 0 640 640">
                <path d="M256 312C322.3 312 376 258.3 376 192C376 125.7 322.3 72 256 72C189.7 72 136 125.7 136 192C136 258.3 189.7 312 256 312zM226.3 368C127.8 368 48 447.8 48 546.3C48 562.7 61.3 576 77.7 576L329.2 576C293 533.4 272 478.5 272 420.4L272 389.3C272 382 273 374.8 274.9 368L226.3 368zM477.3 552.5L464 558.8L464 370.7L560 402.7L560 422.3C560 478.1 527.8 528.8 477.3 552.6zM453.9 323.5L341.9 360.8C328.8 365.2 320 377.4 320 391.2L320 422.3C320 496.7 363 564.4 430.2 596L448.7 604.7C453.5 606.9 458.7 608.1 463.9 608.1C469.1 608.1 474.4 606.9 479.1 604.7L497.6 596C565 564.3 608 496.6 608 422.2L608 391.1C608 377.3 599.2 365.1 586.1 360.7L474.1 323.4C467.5 321.2 460.4 321.2 453.9 323.4z" />
            </svg>Sécurité, accès et maintenance</a>
    </div>
</body>

</html>