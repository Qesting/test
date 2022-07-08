<?php

    // funkcje używane w wielu miejscach

    // czy tablica jest jednowymiarowa?
    // działa lepiej niż / if (count($a) != count($a, 1)) /, ponieważ nie ignoruje pustych tablic potomnych (długość 0)
    function isNotMultidimensional(array $array) {
        foreach ($array as $element) if (is_array($element)) return false;
        return true;
    }

    // replacement dla nieistniejącej przed PHP 8 funkcji str_contains
    if (!function_exists('str_contains')) {
        function str_contains (string $haystack, string $needle)
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
    function showNot() {
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
     * - $formData - dane otrzymane w superglobalnej $_POST lub $_GET
     *
     * @return void
     */
    function argStrip($formData) {
        $res = "";
        if (is_array($formData)) { // jeżeli argument jest tablicą, każdy jej element zostanie przepuszczony przez funkcję
            $i = 0;
            $res = array();
            foreach($formData as $val) {
                $res[$i] = argStrip($val);
                $i++;
            }
        } else {
            $res = htmlspecialchars(stripslashes(trim($formData)));
        }
        return $res;
    }
    
    /**
     * Funkcja do zmieniania tablicy (najlepiej zawierającej pojedyncze znaki) na ciąg 
     * złożony z wszystkich jej elementów.
     *
     * @param  array $arr
     * @return void
     */
    function concArray($arr) {
        $result = "";
        if (is_array($arr)) {
            foreach ($arr as $val) {
                $result .= (is_array($val)) ? concArray($val) : argStrip($val);
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
    function review() {
        $arr = &$_SESSION['ans'];
        $points = 0;
        $max = 0;

        if(is_array($arr)) {
            // dla każdego indeksu w $_SESSION['ans']
            foreach($arr as $key => $ans) {
                if ($ans['type'] == 1) { // jeżeli typ == 1, po prostu porównaj
                    $points += ($ans['answer'] == $ans['correct']) ? $ans['points'] : 0;
                } else if ($ans['type'] == 2) { // jeżeli typ == 2, sprawdź występowanie kolejnych znaków udzielonej odpowiedzi w poprawnej (po concArray())
                    if (!empty($ans['answer'])) {
                        $temp = 0;
                        $ansArray = str_split($ans['answer']); // string -> tablica pojedynczych znaków
                        foreach ($ansArray as $char) {
                            $temp += (str_contains($ans['correct'], $char)) ? $ans['points'] : -$ans['points'];
                        }
                        $points += ($temp >= 0) ? $temp : 0; // nie chcemy odejmować punktów
                    }
                } else if ($ans['type'] == 3) { // jeżeli typ == 3, skonwertuj obie na lowercase (dla wszystkich utf-8) i porównaj
                    mb_internal_encoding('UTF-8');
                    $points += (mb_strtolower($ans['answer']) == mb_strtolower($ans['correct'])) ? $ans['points'] : 0;
                }
            }
        }
        return $points;
    }
?>