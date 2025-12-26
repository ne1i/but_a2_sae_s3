<?php

namespace ButA2SaeS3;

use ButA2SaeS3\dto\AdherentDto;

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

    public static function Button($text, $variant = 'fage', $type = 'button', $class = '', $attributes = [])
    {
        $variants = [
            'fage' => ['bg' => 'bg-fage-700', 'hover' => 'hover:bg-fage-800'],
            'green' => ['bg' => 'bg-green-600', 'hover' => 'hover:bg-green-700'],
            'red' => ['bg' => 'bg-red-600', 'hover' => 'hover:bg-red-700'],
            'yellow' => ['bg' => 'bg-yellow-500', 'hover' => 'hover:bg-yellow-600'],
            'gray' => ['bg' => 'bg-gray-500', 'hover' => 'hover:bg-gray-600']
        ];
        
        $color = $variants[$variant] ?? $variants['fage'];
        
        $attr_string = '';
        foreach ($attributes as $attr => $value) {
            $attr_string .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
        }
        
        $classes = 'rounded-full py-2 px-6 text-white ' . $color['bg'] . ' ' . $color['hover'] . ' ' . $class;
        
        if ($type === 'link') {
            return '<a href="' . ($attributes['href'] ?? '#') . '" class="' . $classes . '"' . $attr_string . '>' . $text . '</a>';
        }
        
        return '<button type="' . $type . '" class="' . $classes . '"' . $attr_string . '>' . $text . '</button>';
    }

    public static function FormInput($name, $label, $type = 'text', $value = '', $required = false, $class = '', $attributes = [])
    {
        $required_attr = $required ? 'required' : '';
        $attr_string = '';
        foreach ($attributes as $attr => $attr_value) {
            if ($attr !== 'value') {
                $attr_string .= ' ' . $attr . '="' . htmlspecialchars($attr_value) . '"';
            }
        }
        
        $classes = 'border-2 rounded-full pl-2 py-1 bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-fage-300 ' . $class;
        
        return '<div class="flex flex-col ' . ($attributes['container-class'] ?? '') . '">
                    <label for="' . $name . '" class="text-lg">' . $label . '</label>
                    <input type="' . $type . '" name="' . $name . '" class="' . $classes . '" value="' . htmlspecialchars($value) . '" ' . $required_attr . $attr_string . '>
                </div>';
    }

    public static function FormSelect($name, $label, $options, $selected = '', $class = '', $attributes = [])
    {
        $attr_string = '';
        $required_attr = '';
        foreach ($attributes as $attr => $value) {
            if ($attr !== 'required') {
                $attr_string .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
            } elseif ($value) {
                $required_attr = 'required';
            }
        }
        
        $classes = 'border-2 rounded-full pl-2 py-1 bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-fage-300 appearance-none pr-8 ' . $class;
        
        $options_html = '';
        foreach ($options as $value => $option_text) {
            $selected_attr = ($value == $selected) ? 'selected' : '';
            $options_html .= '<option value="' . htmlspecialchars($value) . '" ' . $selected_attr . '>' . htmlspecialchars($option_text) . '</option>';
        }
        
        return '<div class="flex flex-col ' . ($attributes['container-class'] ?? '') . '">
                    <label for="' . $name . '" class="text-lg">' . $label . '</label>
                    <select name="' . $name . '" class="' . $classes . '"' . $attr_string . ' ' . $required_attr . '>
                        ' . $options_html . '
                    </select>
                </div>';
    }

    public static function Message($text, $type = 'success')
    {
        $colors = [
            'success' => 'text-green-500',
            'error' => 'text-red-500',
            'warning' => 'text-yellow-600',
            'info' => 'text-blue-500'
        ];
        
        $color = $colors[$type] ?? $colors['success'];
        
        return '<span class="' . $color . ' text-center">' . $text . '</span>';
    }

    public static function Link($text, $url, $variant = 'default', $class = '')
    {
        $variants = [
            'default' => 'text-blue-600 hover:text-blue-800 underline',
            'danger' => 'text-red-600 hover:text-red-800 underline',
            'muted' => 'text-gray-600 hover:text-gray-800 underline'
        ];
        
        $color = $variants[$variant] ?? $variants['default'];
        $classes = $color . ' ' . $class;
        
        return '<a href="' . $url . '" class="' . $classes . '">' . $text . '</a>';
    }

    public static function AdherantTableRow(AdherentDto $adherant, bool $alternate)
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
                        <a href="/edit_adherent?id=' . $adherant->id . '#adherents-table" class="text-blue-600 underline">Modifier</a>
                        <form method="post" style="display: inline;" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cet adhérent ?\')">
                            <input type="hidden" name="delete_id" value="' . $adherant->id . '">
                            <button type="submit" class="bg-transparent hover:bg-transparent text-red-600 hover:text-red-700 underline ml-2 px-0 py-0 border-0 focus:outline-none">Supprimer</button>
                        </form>
                    </td>
                </tr>';
        return $tr;
    }
}
