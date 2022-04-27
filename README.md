# testopol
Projekt otrzymał nazwę "TESTOPOL" na około 2 dni przed przejściem na v1.0

Niestety w dokumentacji pominięte zostały jedna lub dwie wersje (przy takiej ilości zmian zapomnieliśmy o ich numeracji).

## Aktualna wersja: 1.0

Nadchodzące zmiany:
- podglądanie wyników testów
- testy scrollowane
- przewodnik po funkcjach

*Nie pytajcie czemu jeżyki.*


# CHANGELOG

## ---=== REL v1.0 ===---
- Dodano opcję wyboru czasu per pytanie - od 30 do 60 sekund
- Zmodyfikowano fragment skryptu liczący wyniki - teraz nie uznaje jako punktu NULL==NULL
- Zmieniono sposób zapytań do bazy z mysqli proceduralnego z wpisywaniem zmiennych na mysqli proceduralne z przygotowanymi zapytaniami
- Zmieniono sposób zapytań POST - weryfikacja odbywa się w ramach tego samego pliku, który wysyła dane
- Dodano oceny
- Dodano obrazy dla ocen cel i ndt
- **Zmieniono interfejs dodawania pytań/odpowiedzi**