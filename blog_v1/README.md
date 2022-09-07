Aplikacja blog

Instrukcja instalacji:

1 Pobrać repozytorium

2 Wykonać polecenie: composer install

3 Zainstalować kontenery: docker-compose build

4 Wystartować kontenery: docker-compose up -d

5 Ustawić prawidłowe dostępy do bazy danych w pliku .env: DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name

6 Wejść do kontenera: docker-compose exec php bash

7 Załadować przykładowe dane: bin/console doctrine:fixtures:load
