<?php

// src/AppBundle/Twig/AppExtension.php
namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('telephone', array($this, 'telephoneFilter')),
        );
    }

    public function telephoneFilter($number)
    {
        $str = str_split($number, 2);
        $final = $str[0];
        array_shift($str);

        foreach ($str as $s) {
            $final .= ' ' . $s;
        }

        return $final;
    }
}