<?php

namespace AppBundle\Service;

class FormatService
{
    /**
     * Delete special chars from phone number.
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
     * @return mixed|null
     */
    public function mobilePhoneNumber($phone)
    {
        $number = $this->telephoneNumber($phone);
        if (strpos($number, '06') === 0 ||
            strpos($number, '07') === 0
        ) {
            return $number;
        }

        return null;
    }
}
