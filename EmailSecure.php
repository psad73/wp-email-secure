<?php
defined('WPINC') or die;

class EmailSecure
{

    private $key;

    public function init()
    {
        $class = __CLASS__;
        new $class;
    }

    public function __construct()
    {
        $key = microtime(true);
        $key = "qwerty";
        $secKey = substr(md5($key), 16);
        $this->key = $secKey;
        add_action('wp_head', [&$this, 'emailSecureKeyMetaTag']);
        add_action('wp_enqueue_scripts', [&$this, 'loadEmailSecureScripts']);
        add_shortcode('secemail', [&$this, 'secureEmailText']);
    }

    public function emailSecureKeyMetaTag()
    {

        echo '<meta name="x-code-email-sec" content="' . $this->key . '">';
    }

    public function loadEmailSecureScripts()
    {
        wp_enqueue_script('scriptEmailSecure', plugins_url("js/email-secure.js", __FILE__));
    }

    function secureEmailText($atts)
    {        
        $email = $atts['email'];
        $tagClass = $atts['class'];
        $action = "return emailSecureDecode(this);";

        $Content = "";

        $emailPart1 = $this->fnEncrypt($this->key, substr($email, 0, strpos($email, "@")));
        $emailPart2 = $this->fnEncrypt($this->key, substr($email, strpos($email, "@") + 1));

        $outputEmail = $emailPart1 . "@" . $emailPart2 . ".com";
        $text = $atts['text'] ? $atts['text'] : $outputEmail;

        //$Content = '<a href="/contact" data-xcode="$outputEmail"></a>';

        $Content = '<a href="mailto:' . $outputEmail . '" class="' . $tagClass . '" onclick="' . $action . '">' . $text . '</a>';
        
        return $Content;
    }

    public function fnEncrypt($password, $text)
    {
        // move text to base64 so we avoid special chars
        $base64 = base64_encode($text);

        // text string to array
        $arr = str_split($base64, 1);
        // array of password
        $arrPass = str_split($password, 1);
        $lastPassLetter = 0;

        // encrypted string
        $encrypted = '';

        // encrypt
        for ($i = 0; $i < count($arr); $i++) {

            $letter = $arr[$i];

            $passwordLetter = $arrPass[$lastPassLetter];

            $temp = self::getLetterFromAlphabetForLetter($passwordLetter, $letter);

            if ($temp === false) {
                // if any error, return null
                return null;
            } else {
                // concat to the final response encrypted string
                $encrypted .= $temp;
            }

            /*
              This is important: if we're out of letters in our
              password, we need to start from the begining.
             */
            if ($lastPassLetter == (count($arrPass) - 1)) {
                $lastPassLetter = 0;
            } else {
                $lastPassLetter++;
            }
        }

        // We finally return the encrypted string
        return $encrypted;
    }

    private static function getLetterFromAlphabetForLetter($letter, $letterToChange)
    {
        
        // this is the alphabet we know, plus numbers and the = sign
        $abc = 'abcdefghijklmnopqrstuvwxyz0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // get the position of the given letter, according to our abc

        $posLetter = strpos($abc, $letter);

        // if we cannot get it, then we can't continue
        if ($posLetter == -1) {
            die('Password letter ' + $letter + ' not allowed.');
            return false;
        }
        // according to our abc, get the position of the letter to encrypt
        $posLetterToChange = strpos($abc, $letterToChange);

        // again, if any error, we cannot continue...
        if ($posLetterToChange == -1) {
            die('Password letter ' + $letter + ' not allowed.');
            return false;
        }

        // let's build the new abc. this is the important part
        $part1 = substr($abc, $posLetter, strlen($abc));
        $part2 = substr($abc, 0, $posLetter);
        $newABC = '' . $part1 . '' . $part2;

        // we get the encrypted letter
        $letterAccordingToAbcArr = str_split($newABC, 1);
        $letterAccordingToAbc = $letterAccordingToAbcArr[$posLetterToChange];

        // and return to the routine...
        return $letterAccordingToAbc;
    }

    public function fnDecrypt($password, $text)
    {

        // convert the string to decrypt into an array
        $arr = str_split($text, 1);

        // let's also create an array from our password
        $arrPass = str_split($password, 1);

        // keep control about which letter from the password we use
        $lastPassLetter = 0;

        // this is the final decrypted string
        $decrypted = '';

        // let's start...
        for ($i = 0; $i < count($arr); $i++) {

            // next letter from the string to decrypt
            $letter = $arr[$i];

            // get the next letter from the password
            $passwordLetter = $arrPass[$lastPassLetter];
            // get the decrypted letter according to the password
            $temp = self::getInvertedLetterFromAlphabetForLetter($passwordLetter, $letter);
            if ($temp === false) {
                // if any error, return null
                return null;
            } else {
                // concat the response
                $decrypted .= $temp;
            }

            // if our password is too short,
            // let's start again from the first letter
            if ($lastPassLetter == (count($arrPass) - 1)) {
                $lastPassLetter = 0;
            } else {
                $lastPassLetter++;
            }
        }
        // return the decrypted string and converted
        // from base64 to plain text
        return base64_decode($decrypted);
    }

    private static function getInvertedLetterFromAlphabetForLetter($letter, $letterToChange)
    {

        $abc = 'abcdefghijklmnopqrstuvwxyz0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $posLetter = strpos($abc, $letter);

        if ($posLetter == -1) {
            echo 'Password letter ' . $letter . ' not allowed.';
            return false;
        }
        $part1 = substr($abc, $posLetter, strlen($abc));
        $part2 = substr($abc, 0, $posLetter);

        $newABC = '' . $part1 . '' . $part2;

        $posLetterToChange = strpos($newABC, $letterToChange);

        if ($posLetterToChange == -1) {
            echo 'Password letter ' . $letter . ' not allowed.';
            return false;
        }

        $letterAccordingToAbcArr = str_split($abc, 1);
        $letterAccordingToAbc = $letterAccordingToAbcArr[$posLetterToChange];

        return $letterAccordingToAbc;
    }
}
