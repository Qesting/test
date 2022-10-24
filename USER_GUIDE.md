# Instrukcja instalacji systemu testopol

### Dla ludzi, którzy nie rozumieją mojego spaghetti kodu

1. Stwórz bazę danych **(sugerowana nazwa: 'test')**  
2. Zaimportuj do bazy skrypt **/sql/testsystem.sql**  
3. Stwórz nowego użytkownika **(sugerowana nazwa: 'connect')** z hasłem **(sugerowane: '1234')**  
4. Nadaj użytkownikowi **przynajmniej** uprawnienia **INSERT, SELECT, UPDATE, DELETE** na wszystkich tabelach w bazie  

Jeżeli postanowisz użyć innych bazy, użytkownika, hasła, ustaw właściwe poświadczenia w **/config/credentials.php**