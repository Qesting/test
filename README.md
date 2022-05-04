# TESTOPOL

**„TESTOPOL”** to hobbystycznie tworzona platforma do rozwiązywania testów zbudowana na języku PHP, bazie danych MySQL oraz frontendowych bibliotekach Bootstrap 4 i jQuery 3.6.

Platforma posiada:
- testy w wersji przewijanej i po jednym pytaniu;
- 3 rodzaje pytań:
    - zamknięte jednokrotnego wyboru
    - zamknięte wielokrotnego wyboru
    - otwarte (tekstowe)
- ręczną weryfikację użytkowników przez administratora - nikt niepowołany nie dostanie się do bazy testów;
- mechanizm sesji
    - możliwość zabezpieczenia testu przed dostępem poza sesją
    - zapis wyników do bazy
    - podgląd zapisanych wyników na specjalnym ekranie wpisów
    - automatyczne ocenianie według standardowej skali ocen
    - sześciocyfrowe kody sesji - milion możliwych kombinacji
- w pełni działający interfejs użytkownika do zarządzania testami i sesjami
- możliwość nadania prawa do przejrzenia odpowiedzi po zakończeniu testu 

## Aktualna wersja: 1.1

Nadchodzące zmiany:
- przewodnik po funkcjach
- dokumenty testopol
- tryb ciemny
- przepisanie interefejsu dodawania pytań na prostszą wersję, opartą na JS 

## Opis techniczny

Platforma testopol zbudowana jest z użyciem następujących narzędzi/języków/programów:
- PHP w wersji 7.4
- MySQL w wersji 8.0
- HTML w wersji 5
- CSS w wersji 3
- JavaScript w wersji ES6
- Bootstrap w wersji 4 **--biblioteka**
- jQuery w wersji 3.6 **--biblioteka**