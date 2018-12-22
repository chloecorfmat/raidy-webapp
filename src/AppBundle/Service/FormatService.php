<?php

namespace AppBundle\Service;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

class FormatService
{
    /**
     * Delete special chars from phone number.
     *
     * @param string $phone Phone number
     *
     * @return mixed
     */
    public function telephoneNumber($phone)
    {
        $str = $phone;
        $charToReplace = [' ', '-', '.'];
        foreach ($charToReplace as $char) {
            $str = str_replace($char, '', $str);
        }

        return $str;
    }

    /**
     * @param string $phone Phone number
     *
     * @return mixed|null
     */
    public function mobilePhoneNumber($phone)
    {
        $number = $this->telephoneNumber($phone);

        if (0 === strpos($number, '06')
            || 0 === strpos($number, '07')
        ) {
            return $number;
        }

        return null;
    }

    /**
     * @param string $password Password to verify.
     * @param Form   $form     Form to add errors if needed.
     * @return bool If password contains errors.
     */
    public function checkPassword($password, &$form)
    {
        $noErrors = true;

        // Check length.
        if (!preg_match("(^.{8,50}$)", $password)) {
            $noErrors = false;
            $form->addError(new FormError('Le mot de passe doit comporter entre 8 et 50 caractères.'));
        }

        // Check uppercase.
        if (!preg_match("([A-Z]+)", $password)) {
            $noErrors = false;
            $form->addError(new FormError('Le mot de passe doit comporter au moins une lettre majuscule.'));
        }

        // Check lowercase.
        if (!preg_match("([a-z]+)", $password)) {
            $noErrors = false;
            $form->addError(new FormError('Le mot de passe doit comporter au moins une lettre minuscule.'));
        }

        // Check number.
        if (!preg_match("(\d+)", $password)) {
            $noErrors = false;
            $form->addError(new FormError('Le mot de passe doit comporter au moins un chiffre.'));
        }

        // Check special char.
        if (!preg_match('([@;,&()!?:%*€$£+=#_\/\\.\[\]\{\}-]+)', $password)) {
            $noErrors = false;
            $form->addError(
                new FormError(
                    'Le mot de passe doit comporter au moins un caractère spécial parmi la liste suivante : ' .
                    '@ ; , & ( ) ! ? : % * € $ £ + = # _ \ / [ ] { } - .'
                )
            );
        }

        return $noErrors;
    }
}
