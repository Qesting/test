<?php

    // funkcje używane w wielu miejscach

    // czy tablica jest jednowymiarowa?
    // działa lepiej niż / if (count($a) != count($a, 1)) /, ponieważ nie ignoruje pustych tablic potomnych (długość 0)
    function isNotMultidimensional(array $array) :bool {
        foreach ($array as $element) if (is_array($element)) return false;
        return true;
    }

    // replacement dla nieistniejącej przed PHP 8 funkcji str_contains
    if (!function_exists('str_contains')) {
        function str_contains (string $haystack, string $needle) :bool
        {
            return empty($needle) || strpos($haystack, $needle) !== false;
        }
    }
   
    /**
     * Funkcja do dekodowania wiadomości ($_SESSION['notice'] / $notice)
     * - s-* - sukces (zielony alert)
     * - e-* - błąd (czerwony alert)
     * 
     * @return void
     */
    function showNot() :void {
        global $notice, $notice_class;

        if (!empty($_SESSION['notice'])) {
            $notice = $_SESSION['notice'];
            unset($_SESSION['notice']);
        } 
        
        if (preg_match("/^s-/", $notice)) {
            $notice_class = "alert alert-success";
        } else if (preg_match("/^e-/", $notice)) {
            $notice_class = "alert alert-danger";
        } else if (preg_match("/^w-/", $notice)) {
            $notice_class = "alert alert-warning";
        }
        $notice = substr($notice, 2, (strlen($notice) - 2));
    }
    
    /**
     * Funkcja do zmieniania otrzymanych danych na ich "bezpieczną" formę
     * - $formData - dane otrzymane w superglobalnej $_POST lub $_GET (element tablicy)
     *
     * @return void
     */
    function argStrip(string|array|null $formData) :string|array {
        $res = "";
        if (is_array($formData)) { // jeżeli argument jest tablicą, każdy jej element zostanie przepuszczony przez funkcję
            $res = array();
            foreach($formData as $key => $val) $res[$key] = argStrip($val);
        } else {
            $res = htmlspecialchars(stripslashes(trim($formData)));
        }
        return $res;
    }
    
    /**
     * Funkcja do zmieniania tablicy (najlepiej zawierającej pojedyncze znaki) na ciąg 
     * złożony z wszystkich jej elementów.
     * - $separator - znak rozdzielający elementy (domyślnie pusty)
     *
     * @param  array $arr
     * @param  string $separator
     * @return void
     */
    function concArray(?array $arr, string $separator = "") :string {
        $result = "";
        if (is_array($arr)) {
            foreach ($arr as $val) {
                $result .= (is_array($val)) ? concArray($val) : argStrip($val).$separator;
            }
        }
        return $result;
    }
    
    /**
     * Funkcja oceniająca testy
     * - wywołanie: $score_cal = review();
     *
     * @return void
     */
    function review() :int {
        $ans = &$_SESSION['ans'];
        $quest = unserialize($_SESSION['test'])->testQuestions;
        $points = 0;

        if(is_array($ans) && count($ans) > 0) {

            for ($i = 0; $i < count($quest); $i++) {

                $q = $quest[$i];

                if ($q->questionType == 1) $points += ($ans[$i] == $q->questionAnswer) ? $q->questionPoints : 0;
                else if ($q->questionType == 2) {
                    if (!empty($ans[$i])) {
                        $temp = 0;
                        $ansArray = str_split($ans[$i]);
                        foreach ($ansArray as $char) $temp += (str_contains($q->questionAnswer, $char)) ? $q->questionPoints : -$q->questionPoints; 
                        $points += ($temp >= 0) ? $temp : 0;
                    }
                } else if ($q->questionType == 3) {
                    mb_internal_encoding('UTF-8');
                    $points += (mb_strtolower($ans[$i]) == mb_strtolower($q->questionAnswer)) ? $q->questionPoints : 0;
                } 

            }
        }
        return $points;
    }
?>