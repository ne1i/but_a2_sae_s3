<?php

namespace ButA2SaeS3;

use ButA2SaeS3\dto\AddAdherentDto;

class Components
{
    public static function BackToLink($label = "Retour à l'accueil", $url = "/admin")
    {
        $button = '<a href="' . $url . '" class="flex gap-4 items-center text-blue-600 hover:text-blue-800 ">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"
                class="w-10 border-2 rounded-full p-1">
                <path fill-rule="evenodd"
                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
            </svg>
            <p class="text-xl -ml-1 -mt-1 underline">' . $label . '</p>
        </a>';
        return $button;
    }


    public static function Heading2($content, $id = "")
    {
        $classes = "text-3xl text-shadow-2xs mb-4";
        $id_str = empty($id) ? "" : 'id="' . $id . '"';
        return '<h1 ' . $id_str . '  class="' . $classes . '">' . $content . '</h1>';
    }

    public static function AdherantTableRow(AddAdherentDto $adherant, bool $alternate)
    {
        $bg_colors = $alternate ? "bg-gray-200 " : "bg-gray-50";
        $tr = '<tr class="hover:bg-gray-300 ' . $bg_colors . '">
                    <td class="border px-4 py-2">' . $adherant->nom . '</td>
                    <td class="border px-4 py-2">' . $adherant->prenom . '</td>
                    <td class="border px-4 py-2">' . $adherant->adresse . '</td>
                    <td class="border px-4 py-2">' . $adherant->profession . '</td>
                    <td class="border px-4 py-2">' . $adherant->age . '</td>
                    <td class="border px-4 py-2">' . $adherant->ville . '</td>
                    <td class="border px-4 py-2">
                        <a href="" class="text-blue-600 underline">Modifier</a>
                        <a href="" class="text-blue-600 underline">Supprimer</a>
                    </td>
                </tr>';
        return $tr;
    }
}
