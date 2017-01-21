<?php
namespace App\helper;

class helper_date
{
    /* Le contenu de us2fr et fr2us est identique, mais je laisse
     * deux méthodes pour être conforme à l'énoncé.
     */

    public static function us2fr($date)
    {
        // Ex : 1999-09-21

        if(helper_date::check($date))
        {
            $matches = explode("-", $date);
            return ($matches[2].'-'.$matches[1].'-'.$matches[0]);
        }

        return false;
    }

    public static function fr2us($date)
    {
        // Ex : 21-09-1999

        if(helper_date::check($date))
        {
            $matches = explode("-", $date);
            return ($matches[2].'-'.$matches[1].'-'.$matches[0]);
        }

        return false;
    }

    public static function check($date)
    {
        if(preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches))
        {
            if (checkdate($matches[2], $matches[3], $matches[1]))
                return true;
        }
        else if(preg_match("/^(\d{2})-(\d{2})-(\d{4})$/", $date, $matches))
        {
            if (checkdate($matches[2], $matches[1], $matches[3]))
                return true;
        }

        return false;
    }
}
?>