<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";


$adherent_stats = $db->get_adherent_statistics();
$mission_stats = $db->get_mission_statistics();
$financial_stats = $db->get_financial_statistics();
$participation_stats = $db->get_participation_statistics();
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">
        <div class="bg-white rounded-lg shadow-lg mb-8 p-8">
            <div class="mb-6">
                <?= c::BackToLink(); ?>
            </div>
            <?= c::Heading2("Tableau de Bord Statistique") ?>
            <div class="mt-8 flex justify-evenly bg-gray-100 shadow-lg rounded-lg">

                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Adhérents actifs</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($adherent_stats['total_active']) ?></p>
                            <p class="text-xs text-green-600">+<?= $adherent_stats['new_this_year'] ?> cette année</p>
                        </div>
                    </div>
                </div>

                <div class="   p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Missions totales</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($mission_stats['total_missions']) ?></p>
                            <p class="text-xs text-blue-600"><?= $mission_stats['upcoming_missions'] ?> à venir</p>
                        </div>
                    </div>
                </div>

                <div class="   p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Cotisations (année)</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($financial_stats['contributions_this_year'] / 100, 2) ?> €</p>
                            <p class="text-xs text-green-600"><?= $financial_stats['contributor_count'] ?> contributeurs</p>
                        </div>
                    </div>
                </div>

                <div class="   p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total dons</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($financial_stats['total_donations'] / 100, 2) ?> €</p>
                            <p class="text-xs text-purple-600"><?= $financial_stats['donor_count'] ?> donateurs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques des Adhérents</h3>

                <div class="space-y-4 bg-gray-100 p-4 rounded-lg shadow-lg">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Répartition par âge</h4>
                        <?php foreach ($adherent_stats['age_distribution'] as $age_group): ?>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600"><?= $age_group['age_group'] ?></span>
                                <span class="text-sm font-medium"><?= $age_group['count'] ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?= ($age_group['count'] / $adherent_stats['total_active']) * 100 ?>%"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Villes principales</h4>
                        <?php foreach (array_slice($adherent_stats['city_distribution'], 0, 5) as $city): ?>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600"><?= htmlspecialchars($city['city']) ?></span>
                                <span class="text-sm font-medium"><?= $city['count'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques des Missions</h3>

                <div class="space-y-4 bg-gray-100 p-4 rounded-lg shadow-lg">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600"><?= $mission_stats['missions_this_year'] ?></p>
                            <p class="text-sm text-gray-600">Missions cette année</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?= $mission_stats['upcoming_missions'] ?></p>
                            <p class="text-sm text-gray-600">Missions à venir</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600"><?= $mission_stats['total_participants'] ?></p>
                            <p class="text-sm text-gray-600">Participants totaux</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-yellow-600"><?= $mission_stats['avg_participants_per_mission'] ?></p>
                            <p class="text-sm text-gray-600">Participants/mission</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Taux de participation</h4>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-green-600 h-4 rounded-full flex items-center justify-center text-xs text-white" style="width: <?= $participation_stats['participation_rate'] ?>%">
                                <?= $participation_stats['participation_rate'] ?>%
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1"><?= $mission_stats['total_participants'] ?> adhérents sur <?= $adherent_stats['total_active'] ?> ont participé à au moins une mission</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques Financières</h3>

                <div class="space-y-4 bg-gray-100 p-4 rounded-lg shadow-lg">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?= number_format($financial_stats['total_subsidies'] / 100, 2) ?> €</p>
                            <p class="text-sm text-gray-600">Total subventions</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600"><?= number_format($financial_stats['total_donations'] / 100, 2) ?> €</p>
                            <p class="text-sm text-gray-600">Total dons</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Évolution mensuelle des cotisations</h4>
                        <div class="space-y-2">
                            <?php foreach (array_slice($financial_stats['monthly_contributions'], -6) as $month): ?>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-gray-600"><?= date('m/Y', strtotime($month['month'] . '-01')) ?></span>
                                    <span class="text-sm font-medium"><?= number_format($month['amount'] / 100, 2) ?> €</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= min(100, ($month['amount'] / 10000) * 100) ?>%"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Adhérents les plus actifs</h3>

                <div class="space-y-3">
                    <?php foreach ($participation_stats['most_active_adherents'] as $member): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($member['first_name']) ?> <?= htmlspecialchars($member['last_name']) ?></p>
                                <p class="text-sm text-gray-600">Participations</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-blue-600"><?= $member['mission_count'] ?></p>
                                <p class="text-xs text-gray-500">missions</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>


        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Exporter les données</h3>
            <div class="flex gap-4">
                <?= c::Button("Imprimer le rapport", "gray", "button", "", ["onclick" => "window.print()"]) ?>
                <?= c::Button("Exporter en PDF", "blue", "button", "", ["onclick" => "alert('Export PDF à implémenter')"]) ?>
                <?= c::Button("Exporter en CSV", "green", "button", "", ["onclick" => "alert('Export CSV à implémenter')"]) ?>
            </div>
        </div>

    </main>

</body>