# Pogodynka — AI2 LAB B

Symfony 7.4.* z pakietami `webapp`, bez Turbo, AssetMappera, Stimulus i receptur Docker.
Projekt znajduje się w katalogu głównym repozytorium.

## Uruchomienie

Potrzebne są PHP 8.3+ (również dla narzędzi developerskich), Composer oraz rozszerzenia
PHP, m.in. XML/DOM, XMLWriter, ctype i iconv. Na Ubuntu brakujące XML/ZIP można dodać:

```sh
sudo apt-get install php8.3-xml php8.3-zip
composer install
php -S localhost:8000 -t public
```

Otwórz http://pogodynka.localhost:8000. Do sprawozdania zastąp `8000` swoim numerem
albumu, zarówno w poleceniu, jak i adresie. Domyślna strona powitalna Symfony ma status
HTTP 404 do czasu dodania pierwszej trasy. To prawidłowe na etapie tego laboratorium.

```sh
composer validate --strict
php bin/console lint:container
php bin/phpunit
```

Plik `.env` zawiera domyślną konfigurację receptur, w tym przykładowe połączenie
PostgreSQL. Baza nie jest potrzebna do strony powitalnej; przed implementacją
pomiarów ustaw własny `DATABASE_URL` w ignorowanym `.env.local` i odpowiedni sterownik PDO.

## Sprawozdanie

- `AI2-LB-grN-nazwisko-imie.docx` — uzupełniony szablon, z oznaczonymi brakami.
- `docs/AI2-LB-grN-nazwisko-imie.pdf` — roboczy eksport, do ponownego wygenerowania po uzupełnieniu.
- `docs/erd.drawio` — edytowalny diagram do otwarcia w https://app.diagrams.net/.
- `docs/erd.png` — obraz diagramu umieszczony w DOCX.

Przed oddaniem:

1. Uzupełnij niestandardowe właściwości dokumentu: imię, nazwisko, numer albumu,
   grupę i lokalne ścieżki. Zaktualizuj pola i spis treści (`Ctrl+A`, `F9`).
2. Porównaj przyjęty model danych ze szczegółowymi wymaganiami prowadzącego.
3. Zrób zrzut terminala i przeglądarki z portem równym numerowi albumu;
   w przeglądarce muszą być widoczne URL, wersja Symfony i ścieżka projektu.
4. Samodzielnie napisz opis pakietów (do 150 słów), różnicę `require`/`require-dev`
   (do 2 zdań) i podsumowanie. Te trzy sekcje zabraniają używania LLM.
5. Sprawdź zrzut gałęzi `lab-b`, wyeksportuj końcowy PDF i wyślij go prowadzącemu.

Diagram opisuje projekt bazy, a nie wdrożone encje lub migracje. Kod aplikacji
pozostaje szkieletem zgodnie z zakresem LAB B.
