# Posta API

Magyar városok és irányítószámaik REST API-ja Laravel 12-ben. Az adatokat bárki lekérdezheti, de az irányítószámok módosításához tokenes hitelesítés kell (Laravel Sanctum).

## Adatbázis

Az alkalmazás a meglévő `telepulesek` MySQL/MariaDB adatbázist használja:

| Tábla | Tartalom | Honnan jön? |
|---|---|---|
| `megye` | megyék (`id`, `mnev`) | `database/telepulesek.sql` |
| `varos` | 346 város (`vnev`, `megyeid`, `vtipid`, népesség, terület…) | `database/telepulesek.sql` |
| `varostipus` | várostípusok (főváros, megyei jogú város…) | `database/telepulesek.sql` |
| `iranyitoszam` | 750 irányítószám (`irsz`, `varosid`) | Laravel migráció + seeder (`database/data/iranyitoszamok.csv`) |

Az irányítószámok a tanári „Magyarország Irányítószám Listája” PDF-ből származnak. A PDF-ből csak az irányítószám és a városnév került át, a megyét a `varos` tábla adja. Ennek oka, hogy a PDF megye oszlopa sok sorban hibás.

> Megjegyzés: a `megye` táblában a „Fejér” kétszer szerepel (7-es és 8-as id). A városok mind a 7-es id-t használják, a 8-as sorhoz nem tartozik város.

## Telepítés

1. XAMPP-ban indítsd el az **Apache** és a **MySQL** modult.
2. phpMyAdmin-ban hozz létre egy `telepulesek` adatbázist, és importáld bele a `database/telepulesek.sql` fájlt (ha még nincs meg).
3. Futtasd ezeket a parancsokat:

```bash
git clone <repo-url> posta-api
cd posta-api
composer install
cp .env.example .env
php artisan key:generate
```

4. A `.env` fájlban állítsd be az adatbázist:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=telepulesek
DB_USERNAME=root
DB_PASSWORD=
```

5. Futtasd ezeket is:

```bash
php artisan migrate --seed
php artisan serve
```

> ⚠️ **Ne futtasd a `php artisan migrate:fresh` parancsot!** Ez MINDEN táblát töröl, a `megye`, `varos` és `varostipus` táblát is.

Az API címe `http://localhost:8000/api`. A teszt felhasználó `admin@example.com`, a jelszava `password`.

## Végpontok

| Metódus | Végpont | Leírás | Token kell? |
|---|---|---|:---:|
| POST | `/api/login` | Bejelentkezés, visszaadja a tokent | – |
| GET | `/api/user` | A bejelentkezett felhasználó adatai | ✔ |
| POST | `/api/logout` | Kijelentkezés (a token törlése) | ✔ |
| GET | `/api/megyek` | Megyék listája, a városok számával | – |
| GET | `/api/megyek/{id}` | Egy megye a városaival | – |
| GET | `/api/varosok` | Városok listája (megyével és típussal) | – |
| GET | `/api/varosok?kereses={szöveg}` | Keresés a város nevében | – |
| GET | `/api/varosok?megye={id}` | Városok szűrése megyére | – |
| GET | `/api/varosok/{id}` | Egy város az irányítószámaival | – |
| GET | `/api/iranyitoszamok` | Irányítószámok listája (várossal és megyével) | – |
| GET | `/api/iranyitoszamok?kereses={szöveg}` | Keresés: az irányítószám eleje (`40`) vagy a városnév része (`deb`) | – |
| GET | `/api/iranyitoszamok?megye={id}` | Irányítószámok szűrése megyére | – |
| GET | `/api/iranyitoszamok?kereses=..&megye=..` | A keresés és a szűrés együtt is használható | – |
| GET | `/api/iranyitoszamok/{id}` | Egy irányítószám | – |
| POST | `/api/iranyitoszamok` | Új irányítószám | ✔ |
| PUT | `/api/iranyitoszamok/{id}` | Irányítószám módosítása | ✔ |
| DELETE | `/api/iranyitoszamok/{id}` | Irányítószám törlése | ✔ |

A védett végpontokhoz ezeket a fejléceket kell küldeni:

```
Authorization: Bearer <token>
Accept: application/json
```

A POST és a PUT kérés törzse:

```json
{ "irsz": "4099", "varosid": 62 }
```

### Válaszkódok

| Kód | Jelentés |
|---|---|
| 200 | Rendben |
| 201 | Létrehozva |
| 401 | Nincs token, hibás a token, vagy rossz az e-mail cím vagy a jelszó |
| 404 | Nincs ilyen rekord |
| 422 | Validációs hiba (pl. nem 4 számjegyű az irányítószám, vagy nincs ilyen város) |

## Tesztelés Brunóval

1. Brunóban válaszd az **Open Collection** menüpontot, és nyisd meg a `bruno` mappát.
2. Jobb felül válaszd ki a **Local** környezetet.
3. Futtasd először az **1 Auth → Login** kérést. Ez a tokent automatikusan elmenti.
4. Utána sorban futtasd a többi kérést. Az „Új” kérés elmenti a létrehozott rekord id-jét, így a „Módosítás” és a „Törlés” ezt a rekordot kezeli.

## API dokumentáció (apiDoc)

```bash
npm install -g apidoc
apidoc -i app/Http/Controllers -o public/apidoc
```

A dokumentáció ezen a címen nézhető meg: `http://localhost:8000/apidoc/index.html`
