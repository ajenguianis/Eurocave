<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            'replaceLanguageInUrl' => new TwigFunction('area', [$this, 'replaceLanguageInUrl']),
        );
    }

    public function replaceLanguageInUrl($currentLanguage, $newLanguage, $url)
    {

        //EDIT BEGIN
        if (strpos($url,$currentLanguage) == false) {
            $url = getBaseUrl($url).'/'.$currentLanguage;
        }
        //EDIT END
        return str_replace('/' . $currentLanguage . '/', '/' . $newLanguage . '/', $url);
    }
}