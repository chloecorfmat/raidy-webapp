<?php

// src/AppBundle/Twig/AppExtension.php
namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * Add filters on Twig.
     *
     * @return array|\Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('telephone', array($this, 'telephoneFilter')),
        );
    }

    /**
     * @param string $number
     *    Phone number.
     * @return string Phone number with spaces.
     */
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
