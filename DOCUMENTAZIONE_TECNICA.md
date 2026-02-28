# Documentazione Tecnica — IUCN Red List Explorer

---

## 1. Panoramica del Progetto

### 1.1 Obiettivo

Applicazione web che consuma la IUCN Red List API v4 per esplorare gli assessment delle specie classificate nella Lista Rossa dell'IUCN. Sviluppata come prova tecnica per una valutazione di colloquio. Il codice sorgente e' disponibile su GitHub con commit incrementali e significativi (25 commit totali).

### 1.2 Funzionalita' Implementate

| Funzionalita' | Descrizione | Requisito Brief |
|---|---|---|
| Dashboard | Sistemi ecologici (Terrestrial, Freshwater, Marine) + lista nazioni con bandiere emoji | Punto 1 |
| Lista Assessments | Paginata per sistema o paese, toggle lista/card, toggle paginazione/scroll infinito, filtri per anno e stato estinzione | Punto 2-3 |
| Dettaglio Specie | Nome scientifico, nomi comuni (principale evidenziato), tassonomia, lista assessments | Punto 4 |
| Dettaglio Assessment | Trend popolazione, azioni di conservazione, sezioni documentazione HTML, sistemi con link | Punto 4 |
| Preferiti | Pulsante toggle salva/rimuove specie nel DB locale, pagina `/favorites` con data aggiunta | Punti 5-6 |
| Footer con cache | api_version, red_list_version, conteggio specie — cache 1 giorno | Punto 7 |
| Caching granulare | 1h per liste dashboard, 5min per dettagli | Punto 7 |

### 1.3 Vincoli del Brief

- La paginazione deve utilizzare gli **header delle risposte HTTP** (non il body JSON).
- I pulsanti di navigazione devono apparire **solo se disponibili** (es. "Previous" disabilitato a pagina 1).
- Il codice deve essere su GitHub/GitLab con commit incrementali.
- Il README.md deve contenere la procedura di setup.

---

## 2. Stack Tecnologico

### 2.1 Backend

| Tecnologia | Versione | Ruolo |
|---|---|---|
| PHP | 8.4 | Linguaggio server-side |
| Laravel | 12.x | Framework MVC |
| Livewire | 4.x | Componenti reattivi server-driven |

**Motivazione Laravel 12**: ultima versione stabile, supporto PHP 8.4, integrazione nativa con Livewire 4.

**Motivazione Livewire 4 (vs Vue/React)**: permette di costruire interfacce dinamiche senza scrivere JavaScript custom. Tutto lo stato vive sul server. Riduce la complessita' e dimostra padronanza dell'ecosistema PHP moderno. Livewire gestisce automaticamente le chiamate AJAX, la sincronizzazione dello stato e il re-rendering del DOM.

### 2.2 Frontend

| Tecnologia | Versione | Ruolo |
|---|---|---|
| Tailwind CSS | 4.x | Utility-first CSS framework |
| Vite | 7.x | Build tool e dev server con HMR |
| Yarn | latest | Package manager frontend |

**Motivazione Tailwind CSS 4**: elimina il file `tailwind.config.js`. La configurazione avviene direttamente nel CSS tramite la direttiva `@theme`. Il plugin `@tailwindcss/vite` sostituisce PostCSS.

### 2.3 Database e Infrastruttura

| Tecnologia | Versione | Ruolo |
|---|---|---|
| MariaDB | 11.4 | Database relazionale |
| Lando | 3.x | Ambiente Docker-based |
| Nginx | (via Lando) | Web server |

### 2.4 Dipendenze PHP (`composer.json`)

```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1",
    "livewire/livewire": "^4.1"
  }
}
```

Nota: **nessuna dipendenza aggiuntiva** oltre a Laravel e Livewire. Il progetto si basa interamente sulle funzionalita' native del framework.

### 2.5 Dipendenze Frontend (`package.json`)

```json
{
  "devDependencies": {
    "@tailwindcss/vite": "^4.0.0",
    "tailwindcss": "^4.0.0",
    "laravel-vite-plugin": "^2.0.0",
    "vite": "^7.3.1"
  }
}
```

---

## 3. Ambiente di Sviluppo (Lando/Docker)

### 3.1 Configurazione `.lando.yml`

```yaml
name: iucn-red-list
recipe: laravel
config:
  webroot: public
  php: '8.4'
  via: nginx
  database: mariadb:11.4
  xdebug: true
```

Il recipe `laravel` di Lando pre-configura PHP, Nginx, e il database. I servizi aggiuntivi includono:

- **phpmyadmin**: accessibile a `phpmyadmin.iucn-red-list.lndo.site` per ispezione DB
- **appserver**: installa Node.js 24 e Yarn durante il build (`build_as_root`), poi esegue `yarn` per installare le dipendenze frontend

### 3.2 Tooling Custom

| Comando | Descrizione |
|---|---|
| `lando artisan` | Esegue comandi Laravel Artisan |
| `lando composer` | Gestione dipendenze PHP |
| `lando yarn` | Gestione dipendenze frontend |
| `lando dev` | Avvia il server Vite con HMR |
| `lando tinker` | REPL Laravel |
| `lando test` | Esegue PHPUnit |
| `lando pint` | Code style fixer (Laravel Pint) |

**Nota importante**: la macchina host NON ha PHP installato. Tutti i comandi vengono eseguiti all'interno dei container Docker tramite Lando.

### 3.3 URL dell'Applicazione

L'applicazione e' accessibile a `https://iucn-red-list.lndo.site` (HTTPS con certificato self-signed via Lando).

---

## 4. Configurazione

### 4.1 Variabili d'Ambiente (`.env`)

```env
IUCN_API_TOKEN=5A9enNRRkUV9iDZ9BFWGGfq5o1faU7gCkwsJ
IUCN_API_BASE_URL=https://api.iucnredlist.org/api/v4
```

### 4.2 File di Configurazione Custom (`config/iucn.php`)

```php
return [
    'base_url' => env('IUCN_API_BASE_URL', 'https://api.iucnredlist.org/api/v4'),
    'token' => env('IUCN_API_TOKEN', ''),
];
```

Questo file centralizza l'accesso alle credenziali API. I componenti non accedono mai direttamente alle variabili d'ambiente: utilizzano sempre `config('iucn.token')` e `config('iucn.base_url')`. Questo pattern e' fondamentale perche':

1. I valori di `env()` sono disponibili solo durante il boot — dopo `php artisan config:cache` le chiamate `env()` dirette restituiscono `null`.
2. Centralizzare la configurazione facilita il testing (si puo' sovrascrivere nei test con `config()->set()`).

### 4.3 Configurazione Vite (`vite.config.js`)

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
```

- `laravel-vite-plugin`: gestisce il manifest degli asset per la produzione e il refresh automatico in sviluppo
- `@tailwindcss/vite`: sostituisce il vecchio approccio PostCSS di Tailwind CSS 3
- `refresh: true`: ricarica il browser quando cambiano i file Blade

### 4.4 CSS Entry Point (`resources/css/app.css`)

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif,
        'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
}
```

- `@import 'tailwindcss'`: importa tutte le utilities Tailwind (sostituisce le vecchie direttive `@tailwind base/components/utilities`)
- `@source`: indica a Tailwind dove cercare le classi CSS usate, per la tree-shaking in produzione
- `@theme`: definisce variabili CSS custom come il font di default

---

## 5. Routing e Navigazione

### 5.1 Tabella Route

```php
Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/favorites', FavoritesList::class)->name('favorites');
Route::get('/assessments/{type}/{code}', AssessmentsList::class)
    ->where('type', 'system|country')
    ->name('assessments');
Route::get('/species/{sisId}', SpeciesDetail::class)->name('species.detail');
Route::get('/assessment/{assessmentId}', AssessmentDetail::class)->name('assessment.detail');
```

| Route | Componente | Tipo | Parametri |
|---|---|---|---|
| `/` | Dashboard | Full-page | Nessuno |
| `/favorites` | FavoritesList | Full-page | Nessuno |
| `/assessments/{type}/{code}` | AssessmentsList | Full-page | `type`: system\|country, `code`: codice sistema/paese |
| `/species/{sisId}` | SpeciesDetail | Full-page | `sisId`: intero (SIS Taxon ID) |
| `/assessment/{assessmentId}` | AssessmentDetail | Full-page | `assessmentId`: intero |

### 5.2 Vincolo sui Parametri

La route `/assessments/{type}/{code}` usa `->where('type', 'system|country')` per validare che il parametro `type` sia uno dei due valori ammessi. Questo impedisce URL non validi e restituisce 404 automaticamente.

### 5.3 Navigazione SPA con `wire:navigate`

Tutti i link interni usano l'attributo `wire:navigate`:

```html
<a href="/species/{{ $sisId }}" wire:navigate>
```

Questo attributo di Livewire 4 trasforma la navigazione in una transizione AJAX: invece di ricaricare l'intera pagina, Livewire scarica solo il contenuto cambiato e aggiorna il DOM. Il risultato e' un'esperienza utente simile a una SPA (Single Page Application) senza framework JavaScript frontend.

**Come funziona internamente**: quando l'utente clicca un link `wire:navigate`, Livewire intercetta il click, esegue una fetch AJAX all'URL, riceve l'HTML della nuova pagina, e sostituisce il contenuto del `<body>` mantenendo gli script e gli stili gia' caricati. La history del browser viene aggiornata tramite `pushState`.

---

## 6. Service Layer — `IucnApiService`

### 6.1 Architettura

La classe `App\Services\IucnApiService` e' il **singolo punto di accesso** a tutte le chiamate API IUCN. Nessun componente Livewire chiama l'API direttamente. Vantaggi:

- **Centralizzazione**: se l'API cambia struttura, si modifica una sola classe
- **Caching**: la logica di cache e' incapsulata qui, trasparente ai consumatori
- **Error handling**: gestione errori uniforme con fallback graceful
- **Testabilita'**: si puo' mockare l'intera classe nei test

### 6.2 Registrazione nel Container

`IucnApiService` **non richiede registrazione manuale** in un Service Provider. Laravel la risolve automaticamente tramite **auto-wiring**: il costruttore dichiara la dipendenza `HttpFactory $http` e il container la inietta.

```php
public function __construct(private readonly HttpFactory $http) {}
```

### 6.3 Client HTTP (`client()`)

```php
private function client(): PendingRequest
{
    return $this->http->withToken((string) config('iucn.token'))
        ->baseUrl((string) config('iucn.base_url'))
        ->timeout(15)
        ->retry(2, 500);
}
```

- `withToken()`: aggiunge l'header `Authorization: Bearer {token}` a ogni richiesta
- `baseUrl()`: tutte le chiamate sono relative a `https://api.iucnredlist.org/api/v4`
- `timeout(15)`: attende massimo 15 secondi prima di lanciare un'eccezione
- `retry(2, 500)`: in caso di errore, riprova fino a 2 volte con 500ms di pausa tra i tentativi

### 6.4 Metodi — Dettaglio Completo

#### `getSystems(): array`

| Aspetto | Dettaglio |
|---|---|
| Endpoint | `GET /systems/` |
| Cache key | `iucn.systems` |
| Cache TTL | 3600 secondi (1 ora) |
| Risposta API | `{ "systems": [{ "code": "terrestrial", "description": { "en": "Terrestrial" } }, ...] }` |
| Mappatura | Trasforma in `[{ "code": "terrestrial", "name": "Terrestrial" }]` |

```php
$systems = $response->json('systems') ?? [];
return collect($systems)->map(fn($s) => [
    'code' => $s['code'],
    'name' => $s['description']['en'] ?? $s['code'],
])->values()->all();
```

La risposta dell'API usa un formato annidato con `description.en` per i nomi localizzati. Il service la semplifica in un array piatto `code/name`.

#### `getCountries(): array`

Identico a `getSystems()` ma sull'endpoint `GET /countries/`. Stesso pattern di mappatura (`countries` invece di `systems`). Cache key: `iucn.countries`.

#### `getAssessmentsBySystem(string $systemCode, int $page = 1): array`

| Aspetto | Dettaglio |
|---|---|
| Endpoint | `GET /systems/{code}?page={page}` |
| Cache key | `iucn.system.{code}.page.{page}` |
| Cache TTL | 3600 secondi (1 ora) |
| Risposta body | `{ "assessments": [{ "assessment_id": 123, "sis_taxon_id": 456, "taxon_scientific_name": "...", "year_published": 2023, "red_list_category_code": "CR", "possibly_extinct": false, "url": "..." }, ...] }` |
| Risposta headers | `total-count`, `page-items`, `current-page`, `total-pages` |

```php
return [
    'assessments' => $response->json('assessments') ?? [],
    'pagination' => [
        'total' => (int) $response->header('total-count'),
        'per_page' => (int) $response->header('page-items'),
        'current_page' => (int) $response->header('current-page'),
        'total_pages' => (int) $response->header('total-pages'),
    ],
];
```

**Punto chiave per il colloquio**: la paginazione viene letta dagli **header HTTP** della risposta, non dal body JSON. Questo e' inusuale e ha richiesto l'implementazione manuale della paginazione nei componenti Livewire, senza poter usare il trait `WithPagination` standard di Laravel.

#### `getAssessmentsByCountry(string $countryCode, int $page = 1): array`

Identico a `getAssessmentsBySystem` ma sull'endpoint `GET /countries/{code}`. Cache key: `iucn.country.{code}.page.{page}`.

#### `getTaxonDetails(int $sisId): array`

| Aspetto | Dettaglio |
|---|---|
| Endpoint | `GET /taxa/sis/{sisId}` |
| Cache key | `iucn.taxon.{sisId}` |
| Cache TTL | 300 secondi (5 minuti) |
| Risposta | `{ "sis_id": 123, "taxon": { "scientific_name": "...", "kingdom_name": "...", "phylum_name": "...", "class_name": "...", "order_name": "...", "family_name": "...", "common_names": [{ "name": "...", "language": "...", "main": true }] }, "assessments": [{ "assessment_id": ..., "year_published": ..., "red_list_category_code": "..." }] }` |

```php
return [
    'sis_id'      => $data['sis_id'] ?? null,
    'taxon'       => $data['taxon'] ?? [],
    'assessments' => $data['assessments'] ?? [],
];
```

TTL di 5 minuti perche' i dati tassonomici potrebbero essere aggiornati piu' frequentemente rispetto alle liste di sistema/paese.

#### `getAssessmentDetails(int $assessmentId): array`

| Aspetto | Dettaglio |
|---|---|
| Endpoint | `GET /assessment/{assessmentId}` |
| Cache key | `iucn.assessment.{assessmentId}` |
| Cache TTL | 300 secondi (5 minuti) |

Restituisce l'intero oggetto JSON dell'assessment, che include:

- `red_list_category.code` e `red_list_category.description.en`: codice e nome della categoria
- `population_trend.description.en`: trend della popolazione (Increasing/Decreasing/Stable/Unknown)
- `possibly_extinct`, `possibly_extinct_in_the_wild`: flag booleani
- `criteria`: criterio di valutazione IUCN (es. "A2cd")
- `conservation_actions`: array di azioni, ogni azione ha `description.en` e `code`
- `systems`: array di sistemi (Terrestrial/Freshwater/Marine) con `code` e `description.en`
- `documentation`: oggetto con chiavi `rationale`, `range`, `population`, `habitats`, `threats`, `measures`, `use_trade`, `trend_justification`, `taxonomic_notes` — ognuna contenente HTML
- `url`: link diretto alla pagina IUCN
- `year_published`: anno di pubblicazione
- `sis_taxon_id`: ID del taxon per il link alla pagina specie

#### `getStatistics(): array`

| Aspetto | Dettaglio |
|---|---|
| Endpoints | 3 chiamate separate |
| Cache key | `iucn.statistics` |
| Cache TTL | 86400 secondi (1 giorno) |

```php
$apiVersionResponse = $client->get('/information/api_version');
$rlVersionResponse  = $client->get('/information/red_list_version');
$countResponse      = $client->get('/statistics/count');

return [
    'api_version'      => $apiVersionResponse->json('api_version') ?? 'v4',
    'red_list_version' => $rlVersionResponse->json('red_list_version') ?? 'N/A',
    'species_count'    => (int) ($countResponse->json('count') ?? 0),
];
```

Tre endpoint distinti aggregati in un'unica struttura, con cache di 1 giorno. Se una delle tre chiamate fallisce, il fallback restituisce valori di default.

#### `translateCategory(string $code): string` (statico)

Mappa i codici IUCN ai nomi completi:

```php
public const array CATEGORY_MAP = [
    'EX' => 'Extinct',
    'EW' => 'Extinct in the Wild',
    'CR' => 'Critically Endangered',
    'EN' => 'Endangered',
    'VU' => 'Vulnerable',
    'NT' => 'Near Threatened',
    'LC' => 'Least Concern',
    'DD' => 'Data Deficient',
    'NE' => 'Not Evaluated',
];
```

Usata nei template Blade per mostrare il nome completo accanto al codice.

### 6.5 Gestione Errori

```php
private function logError(string $endpoint, array $params, Throwable $e): void
{
    if ($e instanceof RequestException && $e->response->status() === 429) {
        Log::warning("IUCN API Rate Limited: {$endpoint}", [...]);
        return;
    }
    Log::error("IUCN API Error: {$endpoint}", [...]);
}
```

- **Rate limiting (429)**: loggato come warning, non come errore — e' un comportamento atteso sotto carico
- **Altri errori**: loggati come errore con traccia completa
- **Fallback**: ogni metodo restituisce array vuoti o strutture di default in caso di errore, garantendo che i componenti non crashino mai

### 6.6 Struttura di Fallback per Assessments

```php
private function assessmentFallback(): array
{
    return [
        'assessments' => [],
        'pagination' => [
            'total' => 0,
            'per_page' => 100,
            'current_page' => 1,
            'total_pages' => 1,
        ],
    ];
}
```

Garantisce che il componente `AssessmentsList` riceva sempre una struttura valida, anche in caso di errore API.

---

## 7. Schema Database

### 7.1 Tabella `favorites`

```sql
CREATE TABLE favorites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    taxon_id BIGINT UNSIGNED UNIQUE NOT NULL,
    scientific_name VARCHAR(255) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Migration Laravel:

```php
Schema::create('favorites', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('taxon_id')->unique();
    $table->string('scientific_name');
    $table->timestamp('added_at')->useCurrent();
});
```

- **`taxon_id` UNIQUE**: impedisce duplicati — una specie puo' essere nei preferiti solo una volta
- **`scientific_name`**: salvato localmente per evitare di dover chiamare l'API per mostrare i preferiti
- **Nessun `created_at`/`updated_at`**: il model ha `$timestamps = false`

### 7.2 Model `Favorite`

```php
class Favorite extends Model
{
    public $timestamps = false;

    protected $fillable = ['taxon_id', 'scientific_name', 'added_at'];

    protected $casts = ['added_at' => 'datetime'];
}
```

- `$timestamps = false`: disabilita la gestione automatica di `created_at`/`updated_at`
- `$casts['added_at' => 'datetime']`: consente l'uso di metodi Carbon come `diffForHumans()` nel template
- `$fillable`: protegge da mass-assignment — solo questi 3 campi possono essere impostati via `create()`

### 7.3 Perche' non c'e' autenticazione

Il brief non richiedeva un sistema utenti. I preferiti sono **globali** (condivisi da chiunque acceda all'applicazione). In un contesto reale, si aggiungerebbe una colonna `user_id` e il vincolo unique diventerebbe `unique(['taxon_id', 'user_id'])`.

---

## 8. Componenti Livewire — Dettaglio Completo

### 8.1 Concetti Fondamentali di Livewire 4

Prima di analizzare ogni componente, e' essenziale capire il ciclo di vita di Livewire:

1. **`mount()`**: eseguito una sola volta al primo rendering (equivalente del `__construct` per il componente). Qui si inizializzano le proprieta' e si caricano i dati.
2. **`render()`**: eseguito a ogni aggiornamento del componente. Restituisce una view Blade.
3. **Proprieta' pubbliche**: sincronizzate automaticamente con il frontend. Ogni modifica triggera un re-render.
4. **`#[Layout()]`**: attributo PHP 8 che specifica il layout wrapper per i componenti full-page.
5. **`#[Title()]`**: imposta il titolo della pagina HTML.
6. **`#[Url]`**: sincronizza la proprieta' con i query parameter dell'URL (es. `?page=2&yearFilter=2023`).
7. **`#[On('event-name')]`**: ascolta un evento Livewire e chiama il metodo decorato.
8. **`$this->dispatch('event-name')`**: emette un evento a tutti i componenti nella pagina.
9. **`#[Defer]`**: attributo Livewire 4 che implementa il deferred loading nativo. Quando applicato a una classe componente, Livewire salta `mount()` al primo rendering e mostra il contenuto di `placeholder()` come skeleton. Subito dopo il caricamento della pagina, Livewire esegue `mount()` via AJAX e sostituisce il placeholder con il contenuto reale. A differenza di `#[Lazy]` (che usa `x-intersect` e carica quando l'elemento entra nel viewport), `#[Defer]` usa `x-init` e carica immediatamente in modo asincrono.
10. **`placeholder()`**: metodo che restituisce una view Blade da mostrare come skeleton durante il caricamento differito. Usato insieme a `#[Defer]`.

**Requisito Livewire 4**: ogni template Blade di un componente Livewire deve avere un **singolo elemento HTML root**. Questo e' il motivo per cui tutti i template iniziano con `<div>` (o `<footer>`, `<span>`, ecc.).

### 8.2 Dashboard (`App\Livewire\Dashboard`)

**File**: `app/Livewire/Dashboard.php`
**Template**: `resources/views/livewire/dashboard.blade.php`
**Route**: `GET /`
**Attributo**: `#[Defer]` — deferred loading nativo di Livewire 4
**Placeholder**: `resources/views/livewire/placeholders/dashboard.blade.php`

#### Deferred Loading (`#[Defer]`)

Il componente utilizza l'attributo `#[Defer]` di Livewire 4. Questo significa che:

1. Al primo caricamento della pagina, `mount()` viene **saltato**
2. Livewire chiama `placeholder()` che restituisce la view `livewire.placeholders.dashboard`
3. Il placeholder mostra uno skeleton con animazioni `animate-pulse` (hero + griglie sistemi/paesi)
4. Subito dopo, Livewire esegue `mount()` via AJAX in background
5. I dati reali sostituiscono lo skeleton senza ricaricare la pagina

```php
public function placeholder(): string
{
    return view('livewire.placeholders.dashboard')->render();
}
```

Questo pattern sostituisce il precedente approccio manuale con `wire:init` + proprieta' `$loading` + `@if($loading)` nel template. Il risultato e' codice piu' pulito e il pattern nativo del framework.

#### Proprieta'

| Proprieta' | Tipo | Default | Descrizione |
|---|---|---|---|
| `$systems` | `array` | `[]` | Lista dei sistemi ecologici |
| `$countries` | `array` | `[]` | Lista di tutti i paesi |
| `$search` | `string` | `''` | Testo di ricerca per filtrare i paesi |

#### Metodi

**`mount(IucnApiService $service)`**: carica sistemi e paesi dal service. Con `#[Defer]`, questo metodo viene saltato al primo rendering e eseguito successivamente via AJAX — nel frattempo l'utente vede lo skeleton placeholder. Il service viene iniettato tramite **method injection** di Livewire — Livewire risolve automaticamente i parametri tipo-hintati dal container Laravel.

**`render()`**: filtra i paesi lato client in base alla proprieta' `$search`:

```php
$filteredCountries = collect($this->countries)
    ->when($this->search, function ($collection) {
        return $collection->filter(fn ($c) =>
            str_contains(strtolower($c['name'] ?? ''), strtolower($this->search))
        );
    })
    ->values()->all();
```

Il filtro e' case-insensitive e opera sulla collection gia' caricata in memoria (nessuna chiamata API aggiuntiva).

#### Template Blade

**Sezione Hero**: banner emerald-950 con cerchi decorativi sfocati (`blur-3xl`) e badge "IUCN Red List API v4" con indicatore pulsante (`animate-pulse`).

**Sezione Sistemi**: griglia 3 colonne. Ogni sistema viene mappato a un trattamento visivo diverso tramite `str_contains()`:

```php
if (str_contains($lower, 'terrestrial')) {
    $icon = '🌿'; $gradient = 'from-emerald-500 to-green-700';
} elseif (str_contains($lower, 'freshwater')) {
    $icon = '💧'; $gradient = 'from-cyan-500 to-blue-600';
} elseif (str_contains($lower, 'marine')) {
    $icon = '🌊'; $gradient = 'from-blue-500 to-indigo-700';
}
```

**Sezione Paesi**: griglia 4 colonne con ricerca live. L'input utilizza `wire:model.live="search"` che aggiorna la proprieta' `$search` a ogni carattere digitato, triggerando un re-render.

**Algoritmo Flag Emoji**: i codici ISO alpha-2 dei paesi vengono convertiti in emoji di bandiere usando i Regional Indicator Symbols Unicode:

```php
$countryFlag = function(string $code): string {
    $code = strtoupper($code);
    if (strlen($code) !== 2) return '🏳️';
    return mb_chr(0x1F1E6 + ord($code[0]) - ord('A'))
         . mb_chr(0x1F1E6 + ord($code[1]) - ord('A'));
};
```

Come funziona: il carattere Unicode `U+1F1E6` e' il Regional Indicator Symbol 'A'. Aggiungendo l'offset del carattere ASCII (`ord('I') - ord('A')` = 8) si ottiene il Regional Indicator per 'I'. Due Regional Indicator consecutivi (es. 'I' + 'T') vengono renderizzati come bandiera (in questo caso, Italia).

### 8.3 AssessmentsList (`App\Livewire\AssessmentsList`)

**File**: `app/Livewire/AssessmentsList.php`
**Template**: `resources/views/livewire/assessments-list.blade.php`
**Route**: `GET /assessments/{type}/{code}`
**Attributo**: `#[Defer]` — deferred loading nativo di Livewire 4
**Placeholder**: `resources/views/livewire/placeholders/assessments-list.blade.php`

Questo e' il componente piu' complesso dell'applicazione. Utilizza lo stesso pattern `#[Defer]` + `placeholder()` del Dashboard per mostrare uno skeleton durante il caricamento iniziale.

#### Proprieta'

| Proprieta' | Tipo | Attributi | Descrizione |
|---|---|---|---|
| `$type` | `string` | — | `'system'` o `'country'` (dal route parameter) |
| `$code` | `string` | — | Codice del sistema o paese |
| `$name` | `string` | — | Nome human-readable (risolto in `mount()`) |
| `$viewMode` | `string` | — | `'list'` (tabella) o `'card'` (griglia) |
| `$scrollMode` | `string` | — | `'paginate'` (prev/next) o `'scroll'` (infinito) |
| `$page` | `int` | `#[Url]` | Pagina corrente, sincronizzata con URL |
| `$yearFilter` | `string` | `#[Url]` | Filtro anno, sincronizzato con URL |
| `$extinctFilter` | `string` | — | `''`, `'yes'`, o `'no'` |
| `$assessments` | `array` | — | Dati correnti da visualizzare |
| `$allAssessments` | `array` | — | Accumulo per scroll infinito |
| `$pagination` | `array` | — | Metadati paginazione |
| `$hasMore` | `bool` | `true` | Se ci sono altre pagine |

#### Logica di Paginazione

```php
public function loadAssessments(): void
{
    $result = match ($this->type) {
        'system' => $service->getAssessmentsBySystem($this->code, $this->page),
        'country' => $service->getAssessmentsByCountry($this->code, $this->page),
    };

    $this->pagination = $result['pagination'];

    if ($this->scrollMode === 'scroll') {
        $this->allAssessments = array_merge($this->allAssessments, $result['assessments']);
        $this->assessments = $this->allAssessments;
    } else {
        $this->assessments = $result['assessments'];
    }

    $this->hasMore = $this->page < ($this->pagination['total_pages'] ?? 1);
}
```

In modalita' **paginate**: i dati vengono sostituiti a ogni cambio pagina.
In modalita' **scroll**: i nuovi dati vengono **accumulati** nell'array `$allAssessments` tramite `array_merge`. L'utente vede tutti i risultati caricati fino a quel momento.

#### Filtri Client-Side

```php
// Nel render()
if ($this->yearFilter !== '') {
    $filtered = $filtered->filter(fn ($a) =>
        ($a['year_published'] ?? '') == $this->yearFilter
    );
}

if ($this->extinctFilter === 'yes') {
    $filtered = $filtered->filter(fn ($a) =>
        !empty($a['possibly_extinct']) || !empty($a['possibly_extinct_in_the_wild'])
    );
}
```

**Perche' client-side**: l'API IUCN v4 non supporta parametri di filtro per anno o stato di estinzione sugli endpoint di sistema/paese. I filtri vengono quindi applicati in PHP sui dati gia' caricati. I dropdown usano `wire:model.live` per aggiornare immediatamente.

#### Toggle View e Scroll Mode

```php
public function toggleViewMode(): void {
    $this->viewMode = $this->viewMode === 'list' ? 'card' : 'list';
}

public function toggleScrollMode(): void {
    $this->scrollMode = $this->scrollMode === 'paginate' ? 'scroll' : 'paginate';
    $this->page = 1;
    $this->allAssessments = [];
    $this->loadAssessments();
}
```

Quando si cambia la modalita' di scroll, la pagina viene resettata a 1 e l'accumulo svuotato per evitare dati duplicati.

#### Template Blade — Vista Lista (Tabella)

Colonne: Anno | Specie | Assessment ID | Categoria | Flag | Preferiti | Azioni

Ogni riga include un componente `FavoriteToggle` inline in modalita' compact:

```html
<livewire:favorite-toggle
    :taxon-id="$a['sis_taxon_id']"
    :scientific-name="$a['taxon_scientific_name'] ?? 'Unknown'"
    :compact="true"
    :wire:key="'fav-list-'.$a['sis_taxon_id'].'-'.$a['assessment_id']"
/>
```

La `:wire:key` e' fondamentale: Livewire la usa per tracciare univocamente ogni istanza del componente nel DOM. Senza una key unica, Livewire potrebbe confondere le istanze durante il re-render.

#### Template Blade — Paginazione

```html
<button wire:click="previousPage" @disabled($page <= 1) ...>Previous</button>
<button wire:click="nextPage" @disabled(!$hasMore) ...>Next</button>
```

La direttiva Blade `@disabled()` aggiunge l'attributo HTML `disabled` quando la condizione e' vera. Questo soddisfa il requisito del brief: "I pulsanti devono essere visualizzati solo se disponibili" (i pulsanti sono sempre visibili ma disabilitati quando non applicabili).

#### Template Blade — Scroll Infinito

```html
@if($hasMore)
    <button wire:click="loadMore">
        <span wire:loading.remove wire:target="loadMore">Load More</span>
        <span wire:loading wire:target="loadMore">Loading...</span>
    </button>
@else
    <div>You've reached the end of the list</div>
@endif
```

- `wire:loading` / `wire:loading.remove`: mostrano/nascondono elementi durante le richieste AJAX
- `wire:target="loadMore"`: limita l'effetto loading solo alle azioni `loadMore`

### 8.4 SpeciesDetail (`App\Livewire\SpeciesDetail`)

**File**: `app/Livewire/SpeciesDetail.php`
**Route**: `GET /species/{sisId}`
**Attributo**: `#[Defer]` — deferred loading nativo di Livewire 4
**Placeholder**: `resources/views/livewire/placeholders/species-detail.blade.php`

Componente semplice di visualizzazione. Utilizza `#[Defer]` + `placeholder()` per mostrare uno skeleton con header, nomi e lista assessments durante il caricamento. Carica i dati tassonomici e la lista degli assessments storici della specie.

#### Mount e Data Flow

```php
public function mount(int $sisId, IucnApiService $service): void
{
    $this->sisId = $sisId;
    $data = $service->getTaxonDetails($sisId);
    $this->taxon = $data['taxon'] ?? [];
    $this->assessments = $data['assessments'] ?? [];
}
```

#### Template — Nomi Comuni

Il nome comune principale (con `main: true`) viene evidenziato in un box ambra separato:

```php
$mainName = collect($commonNames)->firstWhere('main', true);
```

Gli altri nomi comuni vengono mostrati come tag inline.

#### Template — Lista Assessments

Ogni assessment mostra un badge colorato in base alla categoria IUCN con mapping completo:

```php
$catColors = match($cat) {
    'EX' => ['bg' => 'bg-black', 'text' => 'text-white'],
    'CR' => ['bg' => 'bg-red-600', 'text' => 'text-white'],
    'LC' => ['bg' => 'bg-emerald-500', 'text' => 'text-white'],
    // ... etc
};
```

#### Pulsante Preferiti (Full Mode)

```html
<livewire:favorite-toggle :taxon-id="$sisId"
    :scientific-name="$taxon['scientific_name'] ?? 'Unknown'" />
```

Qui `compact` non e' passato, quindi usa il default `false` = pulsante grande con animazione.

### 8.5 AssessmentDetail (`App\Livewire\AssessmentDetail`)

**File**: `app/Livewire/AssessmentDetail.php`
**Route**: `GET /assessment/{assessmentId}`
**Attributo**: `#[Defer]` — deferred loading nativo di Livewire 4
**Placeholder**: `resources/views/livewire/placeholders/assessment-detail.blade.php`

Utilizza `#[Defer]` + `placeholder()` per mostrare uno skeleton completo (header, info cards, sezioni documentazione) durante il caricamento dei dati dall'API.

#### Template — Sezioni Documentazione HTML

La sezione piu' complessa del template. L'API restituisce HTML grezzo nei campi `documentation.*`. Per renderizzarlo mantenendo lo stile coerente, si usa:

```html
<div class="text-sm leading-relaxed text-gray-700
    [&_a]:text-emerald-700 [&_a]:underline [&_a]:font-medium
    [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-3
    [&_ol]:list-decimal [&_ol]:pl-5
    [&_table]:w-full [&_table]:border-collapse
    [&_th]:bg-gray-50 [&_th]:border [&_th]:px-4 [&_th]:py-2.5
    [&_td]:border [&_td]:px-4 [&_td]:py-2.5">
    {!! $docSections[$key] !!}
</div>
```

**Spiegazione tecnica**:

- `{!! !!}`: renderizza HTML non-escaped (vs `{{ }}` che escapa). Necessario perche' il contenuto e' HTML intenzionale dall'API.
- `[&_a]:text-emerald-700`: **arbitrary variant selector** di Tailwind. Il `&` rappresenta l'elemento corrente e `_a` seleziona tutti i tag `<a>` discendenti. Equivalente CSS: `.classe a { color: emerald-700 }`. Questo pattern permette di stilizzare HTML esterno senza modificarlo.

#### Template — Sistemi con Link

I sistemi associati all'assessment sono cliccabili e riportano alla lista degli assessments per quel sistema:

```html
<a href="/assessments/system/{{ $system['code'] ?? '' }}" wire:navigate>
    {{ $system['description']['en'] ?? $system['code'] ?? 'Unknown' }}
</a>
```

### 8.6 FavoriteToggle (`App\Livewire\FavoriteToggle`)

**File**: `app/Livewire/FavoriteToggle.php`
**Template**: `resources/views/livewire/favorite-toggle.blade.php`
**Tipo**: Componente embedded (non full-page)

#### Due Modalita' di Rendering

**Compact** (`$compact = true`): cuore piccolo 32x32px per l'uso inline nelle liste/tabelle:

```html
<button wire:click="toggle"
    class="w-8 h-8 rounded-full {{ $isFavorite ? 'text-rose-500 bg-rose-50' : 'text-gray-300' }}">
    <!-- SVG cuore pieno o vuoto -->
</button>
```

**Full** (`$compact = false`): pulsante grande 56x56px con gradiente e animazione pulsante per la pagina di dettaglio specie:

```html
<button wire:click="toggle"
    class="w-14 h-14 rounded-2xl {{ $isFavorite
        ? 'bg-gradient-to-br from-rose-500 to-pink-600 shadow-lg shadow-rose-500/40'
        : 'bg-white border-2 border-gray-200' }}">
    @if($isFavorite)
        <span class="absolute inset-0 rounded-2xl animate-ping bg-rose-400/20"></span>
    @endif
    <!-- SVG cuore -->
</button>
```

#### Logica Toggle

```php
public function toggle(): void
{
    if ($this->isFavorite) {
        Favorite::where('taxon_id', $this->taxonId)->delete();
        $this->isFavorite = false;
    } else {
        Favorite::create([
            'taxon_id' => $this->taxonId,
            'scientific_name' => $this->scientificName,
            'added_at' => now(),
        ]);
        $this->isFavorite = true;
    }
    $this->dispatch('favorites-updated');
}
```

Dopo ogni toggle, l'evento `favorites-updated` viene dispatched a tutti i componenti Livewire attivi nella pagina.

### 8.7 FavoritesList (`App\Livewire\FavoritesList`)

**File**: `app/Livewire/FavoritesList.php`
**Route**: `GET /favorites`

#### Render con Query Fresca

```php
public function render()
{
    return view('livewire.favorites-list', [
        'favorites' => Favorite::orderByDesc('added_at')->get(),
    ]);
}
```

La query viene eseguita a ogni render (non cachata nella proprieta'), garantendo che la lista sia sempre aggiornata dopo una rimozione.

#### Rimozione con Conferma

```html
<button
    wire:click="removeFavorite({{ $favorite->taxon_id }})"
    wire:confirm="Remove {{ $favorite->scientific_name }} from favorites?">
    Remove
</button>
```

`wire:confirm` mostra un dialog nativo del browser (`confirm()`) prima di eseguire l'azione. Previene rimozioni accidentali.

#### Data Aggiunta

```html
{{ $favorite->added_at->diffForHumans() }}
```

Grazie al cast `'added_at' => 'datetime'` nel model, `added_at` e' un'istanza di `Carbon`, che fornisce il metodo `diffForHumans()` per date relative ("2 hours ago", "3 days ago").

### 8.8 FavoritesCount (`App\Livewire\FavoritesCount`)

**File**: `app/Livewire/FavoritesCount.php`
**Template**: `resources/views/livewire/favorites-count.blade.php`
**Tipo**: Componente embedded nella navbar

Questo e' il componente che dimostra la **reattivita' cross-component** di Livewire.

```php
#[On('favorites-updated')]
public function refreshCount(): void
{
    $this->count = Favorite::count();
}
```

L'attributo `#[On('favorites-updated')]` di Livewire 4 registra il metodo come listener per l'evento `favorites-updated`. Quando qualsiasi componente nella pagina dispatcha questo evento, `refreshCount()` viene chiamato automaticamente, ri-interrogando il DB e aggiornando il badge.

#### Template Minimalista

```html
<span>
    @if($count > 0)
        <span class="absolute -top-1 -right-1 ... bg-rose-500 rounded-full">
            {{ $count }}
        </span>
    @endif
</span>
```

**Nota critica**: il root element DEVE essere un `<span>` (o qualsiasi singolo elemento). Livewire 4 richiede che ogni componente abbia esattamente un elemento root nel template. Se il template avesse avuto solo il `@if` senza wrapper, Livewire lancerebbe un errore 500.

### 8.9 Footer (`App\Livewire\Footer`)

**File**: `app/Livewire/Footer.php`
**Template**: `resources/views/livewire/footer.blade.php`
**Tipo**: Componente embedded nel layout

```php
public function mount(IucnApiService $service): void
{
    $stats = $service->getStatistics();
    $this->speciesCount = $stats['species_count'];
    $this->redListVersion = $stats['red_list_version'];
    $this->apiVersion = $stats['api_version'];
}
```

I dati vengono caricati una volta in `mount()` e sono gia' cachati per 1 giorno nel service. Questo significa che le 3 chiamate API per le statistiche vengono eseguite al massimo una volta al giorno.

---

## 9. Sistema di Eventi Livewire

### 9.1 Flusso Completo

```
AZIONE UTENTE
    |
    v
FavoriteToggle::toggle()
    |
    +-- Aggiorna DB (insert/delete)
    +-- Aggiorna $isFavorite locale
    +-- $this->dispatch('favorites-updated')
            |
            v
    FavoritesCount::refreshCount()  <-- #[On('favorites-updated')]
        |
        +-- Ri-query: Favorite::count()
        +-- Aggiorna $count
        +-- Livewire ri-renderizza il badge nella navbar

--- STESSO FLUSSO PER ---

FavoritesList::removeFavorite($taxonId)
    |
    +-- Elimina dal DB
    +-- $this->dispatch('favorites-updated')
    +-- Livewire ri-renderizza la lista (la query in render() esclude l'elemento rimosso)
```

### 9.2 Perche' Questo Pattern

L'alternativa sarebbe stata passare dati tra componenti tramite proprieta'. Ma i componenti `FavoriteToggle`, `FavoritesList` e `FavoritesCount` non hanno una relazione parent-child diretta. `FavoritesCount` vive nella navbar (nel layout), mentre `FavoriteToggle` puo' trovarsi in qualsiasi pagina. Gli eventi Livewire permettono comunicazione **cross-component senza accoppiamento**.

---

## 10. Layout Principale

### 10.1 Struttura (`resources/views/components/layouts/app.blade.php`)

```html
<!DOCTYPE html>
<html>
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 flex flex-col antialiased">
    <nav class="bg-emerald-950 sticky top-0 z-50">...</nav>
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 py-10">
        {{ $slot }}
    </main>
    <livewire:footer />
</body>
</html>
```

- `flex flex-col min-h-screen`: il body e' un flex container verticale che occupa almeno l'intera viewport. Con `flex-1` su `<main>`, il contenuto si espande e il footer resta in basso anche con poco contenuto.
- `sticky top-0 z-50`: la navbar resta fissa in cima durante lo scroll.
- `{{ $slot }}`: il contenuto del componente Livewire full-page viene inserito qui.
- `@vite()`: include i file CSS e JS compilati da Vite (con hash per cache-busting in produzione).

### 10.2 Navbar — Stato Attivo

```html
<a href="/" wire:navigate
    class="{{ request()->is('/') ? 'text-white bg-emerald-800/60' : 'text-emerald-300/80 hover:text-white' }}">
    Dashboard
</a>
```

`request()->is('/')` verifica se l'URL corrente corrisponde al pattern. Questo e' il modo Laravel standard per evidenziare il link attivo nella navigazione.

### 10.3 Navbar — Badge Preferiti

```html
<a href="/favorites" wire:navigate class="relative ...">
    Favorites
    <livewire:favorites-count />
</a>
```

Il componente `FavoritesCount` e' embedded sia nella navigazione desktop che mobile. Il badge appare come un cerchio rosso posizionato in `absolute` rispetto al link padre (`relative`).

---

## 11. Strategia di Caching — Riepilogo

| Cache Key | TTL | Tipo Dati | Motivazione |
|---|---|---|---|
| `iucn.systems` | 1 ora | Lista sistemi | Dati quasi statici, cambiano raramente |
| `iucn.countries` | 1 ora | Lista paesi | Dati quasi statici |
| `iucn.system.{code}.page.{page}` | 1 ora | Assessments per sistema | Equilibrio tra freschezza e prestazioni |
| `iucn.country.{code}.page.{page}` | 1 ora | Assessments per paese | Stesso rationale |
| `iucn.taxon.{sisId}` | 5 min | Dettaglio specie | Dati soggetti a revisioni, TTL basso |
| `iucn.assessment.{assessmentId}` | 5 min | Dettaglio assessment | Contiene dati critici sulla conservazione |
| `iucn.conservation_actions` | 1 ora | Lista azioni | Dati di riferimento |
| `iucn.statistics` | 1 giorno | Statistiche globali | Cambiano molto raramente |

Il driver di cache e' configurato in `.env` (default: `database`). In produzione si potrebbe usare Redis per prestazioni migliori.

---

## 12. Pattern UI/UX

### 12.1 Sistema Colori Categorie IUCN

| Codice | Nome | Colore Tailwind | Significato |
|---|---|---|---|
| EX | Extinct | `bg-black text-white` | Estinto |
| EW | Extinct in the Wild | `bg-gray-800 text-white` | Estinto in natura |
| CR | Critically Endangered | `bg-red-600 text-white` | In pericolo critico |
| EN | Endangered | `bg-orange-500 text-white` | In pericolo |
| VU | Vulnerable | `bg-yellow-500 text-white` | Vulnerabile |
| NT | Near Threatened | `bg-lime-500 text-white` | Quasi minacciato |
| LC | Least Concern | `bg-emerald-500 text-white` | Minima preoccupazione |
| DD | Data Deficient | `bg-gray-400 text-white` | Dati insufficienti |
| NE | Not Evaluated | `bg-gray-200 text-gray-700` | Non valutato |

### 12.2 Design System

L'applicazione usa un tema ispirato alla natura con:

- **Colore primario**: emerald-950 (sezioni scure), emerald-600 (azioni)
- **Sfondo**: stone-50 (grigio caldo)
- **Card**: `bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100`
- **Glass-morphism**: `backdrop-blur-sm` su card e controlli sticky
- **Gradiente decorativo**: `from-amber-400 via-emerald-400 to-teal-400` (accent bar)
- **Hover effects**: `hover:-translate-y-1` per effetto "lift", `hover:shadow-lg` per ombre
- **Transizioni**: `transition-all duration-300` su quasi tutti gli elementi interattivi

### 12.3 Trend Popolazione

```php
$trendDisplay = match($popTrend) {
    'Increasing' => ['icon' => '↑', 'color' => 'text-emerald-600'],
    'Decreasing' => ['icon' => '↓', 'color' => 'text-red-600'],
    'Stable'     => ['icon' => '→', 'color' => 'text-blue-600'],
    default      => ['icon' => '?', 'color' => 'text-gray-500'],
};
```

### 12.4 Loading States

```html
<div wire:loading class="absolute inset-0 bg-stone-50/60 backdrop-blur-sm z-20 flex items-center justify-center">
    <div class="animate-spin rounded-full h-10 w-10 border-2 border-emerald-200 border-t-emerald-600"></div>
</div>
```

`wire:loading` mostra un overlay semi-trasparente con spinner durante qualsiasi operazione Livewire del componente. L'overlay usa `absolute inset-0` per coprire l'intero contenuto.

### 12.5 Deferred Loading con `#[Defer]`

I 4 componenti full-page che effettuano chiamate API (Dashboard, AssessmentsList, SpeciesDetail, AssessmentDetail) utilizzano l'attributo `#[Defer]` di Livewire 4 per il caricamento differito.

**Come funziona:**

1. Il browser riceve la pagina con lo skeleton placeholder (animazioni `animate-pulse`)
2. Alpine.js trigga `$wire.__lazyLoad()` tramite `x-init` (non `x-intersect` come `#[Lazy]`)
3. `mount()` viene eseguito sul server via AJAX
4. Il componente renderizzato sostituisce il placeholder

**Placeholder views:** `resources/views/livewire/placeholders/`

| Componente | Placeholder | Contenuto Skeleton |
|---|---|---|
| Dashboard | `placeholders/dashboard.blade.php` | Hero + griglia sistemi + griglia paesi |
| AssessmentsList | `placeholders/assessments-list.blade.php` | Header + controlli + righe tabella |
| SpeciesDetail | `placeholders/species-detail.blade.php` | Header + nomi + lista assessments |
| AssessmentDetail | `placeholders/assessment-detail.blade.php` | Header + info cards + sezioni documentazione |

**Differenza da `#[Lazy]`:**
- `#[Lazy]`: usa `x-intersect`, carica quando l'elemento entra nel viewport (scroll-triggered)
- `#[Defer]`: usa `x-init`, carica immediatamente ma in modo asincrono (page-load-triggered)

`#[Defer]` e' la scelta corretta per componenti full-page perche' sono sempre visibili immediatamente.

---

## 13. Decisioni Architetturali e Motivazioni

### 13.1 Perche' Livewire e non Vue/React?

Livewire permette di gestire lo stato interamente sul server. Non serve un'API REST separata, non serve gestire autenticazione delle API lato frontend, non serve duplicare la logica di validazione. Per un'applicazione di questa complessita', Livewire e' la scelta ottimale in termini di rapporto costo/beneficio.

### 13.2 Perche' `declare(strict_types=1)` ovunque?

Type safety rigorosa. In modalita' strict, PHP lancia errori invece di fare coercizione implicita dei tipi. Questo previene bug sottili come passare una stringa dove ci si aspetta un intero.

### 13.3 Perche' `$timestamps = false` sul Model Favorite?

Il campo `updated_at` non ha senso per un preferito: un preferito viene creato o eliminato, mai aggiornato. Il campo `added_at` custom con `useCurrent()` e' sufficiente. Questo riduce overhead del DB e rende lo schema piu' pulito.

### 13.4 Perche' il Service Layer e non chiamate dirette?

Se l'API IUCN cambiasse versione o struttura, bisognerebbe modificare solo `IucnApiService.php` invece di 5 componenti diversi. Il service centralizza anche la logica di caching, retry e error handling. I componenti ricevono dati gia' puliti e normalizzati.

### 13.5 Perche' nessun Service Provider registrato?

`IucnApiService` non richiede binding esplicito nel container Laravel. Il suo costruttore accetta `HttpFactory`, che e' una classe concreta (non un'interfaccia). Laravel la risolve automaticamente tramite auto-wiring. Se in futuro si volesse un'interfaccia per il testing, si potrebbe creare un binding nel `AppServiceProvider`.

### 13.6 Perche' filtri client-side?

L'API IUCN v4 non supporta query parameter per filtrare per anno o stato di estinzione sugli endpoint di lista. L'unica opzione e' caricare i dati e filtrarli in PHP. Questo e' accettabile perche' ogni pagina contiene al massimo ~100 assessment (il default dell'API).

### 13.7 Perche' `retry(2, 500)`?

Errori HTTP transitori (timeout, 503) sono comuni con API esterne. Due tentativi aggiuntivi con 500ms di pausa risolvono la maggior parte dei problemi temporanei senza impattare l'esperienza utente. Il timeout di 15 secondi previene blocchi in caso di downtime dell'API.

---

## 14. Commit History

Il progetto ha 25 commit incrementali che dimostrano un processo di sviluppo organico:

```
503500f refactor: migrate to Livewire 4 native #[Defer] + placeholder() pattern
8ee636c feat: add skeleton loading with wire:init for deferred data loading
d5ee859 chore: add CI/CD workflows and code style fixes
663d6fe chore: add database backup file
433e2e9 fix: dispatch favorites-updated event from FavoritesList on remove
3f34471 feat: make navbar favorites badge reactive with Livewire component
5571dce feat: add inline favorites toggle to assessments list and count badge to navbar
808c7fb style: complete UI redesign with nature-themed design system
ddaac17 style: redesign favorites, favorite toggle, navbar, and footer UI
aaf8313 fix: align AssessmentDetail with API v4 response structure
d4d2879 fix: align SpeciesDetail with API v4 response structure
2b217c8 fix: align AssessmentsList with API v4 field names and pagination headers
89a7325 fix: align IucnApiService with IUCN API v4 real response structure
7c16da0 feat: add country flag emojis and project README
6aca0f8 feat: add species detail and assessment detail pages
2025fe0 feat: add assessments list page with pagination and filters
dd6c1b4 feat: add favorites toggle and favorites list page
51aa197 feat: add dashboard with systems and countries listing
2433c1a feat: add main layout and cached footer component
43fd5a8 feat: add taxon details, assessment details and footer statistics to IucnApiService
2bc8be7 feat: implement IucnApiService for dashboard and list endpoints
610f65e feat: add favorites database schema and Eloquent model
f9c5c0c feat: install Livewire 4 and configure IUCN API token
199ef4a feat: restore core application structure
d66c9bf feat: scaffolding
```

I commit seguono la convenzione **Conventional Commits**: `feat:` per nuove funzionalita', `fix:` per correzioni, `style:` per modifiche puramente estetiche, `chore:` per attivita' di manutenzione, `refactor:` per ristrutturazioni del codice senza cambi funzionali. Questo dimostra un uso professionale del sistema di versionamento.

---

## 15. Procedura di Setup (dal README)

```bash
git clone <repository-url>
cd IUCN-Red-List
cp .env.example .env
# Impostare IUCN_API_TOKEN nel file .env
lando start
lando composer install
lando artisan key:generate
lando artisan migrate
lando yarn build
```

L'applicazione e' accessibile a `https://iucn-red-list.lndo.site`.

Per lo sviluppo con HMR: `lando dev`.

---

## 16. Domande Frequenti per il Colloquio

### Come gestisci la paginazione?

La paginazione e' gestita manualmente perche' l'API IUCN restituisce i metadati negli header HTTP (`total-count`, `page-items`, `current-page`, `total-pages`), non nel body JSON. Il service layer estrae questi header e li normalizza in un array. Il componente `AssessmentsList` gestisce prev/next e numero pagina tramite metodi dedicati. Non e' possibile usare il trait `WithPagination` di Livewire perche' quello si aspetta un `LengthAwarePaginator` di Laravel.

### Come funziona il sistema di preferiti?

Il `FavoriteToggle` verifica se una specie e' gia' nei preferiti tramite `Favorite::where('taxon_id', ...)->exists()`. Al click, inserisce o elimina il record e dispatcha l'evento `favorites-updated`. Il componente `FavoritesCount` nella navbar ascolta questo evento tramite l'attributo `#[On('favorites-updated')]` e ri-interroga il conteggio dal DB, aggiornando il badge in tempo reale.

### Perche' Tailwind CSS 4 e non 3?

Tailwind 4 elimina la necessita' di `tailwind.config.js` e PostCSS. La configurazione avviene direttamente nel CSS tramite `@theme`. Il plugin `@tailwindcss/vite` si integra nativamente con Vite senza middleware aggiuntivi. Le direttive `@source` indicano dove cercare le classi per il tree-shaking.

### Come gestisci gli errori dell'API?

Ogni metodo del service e' wrappato in try-catch. Gli errori 429 (rate limiting) vengono loggati come warning. Tutti gli altri errori come error con traccia completa. In ogni caso, il metodo restituisce una struttura di fallback valida che permette al componente di renderizzare uno stato vuoto invece di crashare.

### Come funziona `wire:navigate`?

E' un attributo di Livewire 4 che trasforma i link in navigazione AJAX. Invece di ricaricare l'intera pagina, Livewire fa una fetch del nuovo contenuto e sostituisce solo il body, mantenendo gli asset gia' caricati. La history del browser viene aggiornata con `pushState`. Il risultato e' un'esperienza SPA senza scrivere JavaScript.

### Come funziona la reattivita' cross-component?

Livewire 4 supporta un sistema di eventi. Un componente dispatcha un evento con `$this->dispatch('event-name')`. Qualsiasi altro componente attivo nella pagina puo' ascoltare l'evento decorando un metodo con `#[On('event-name')]`. Il metodo viene eseguito automaticamente quando l'evento arriva. Questo permette comunicazione tra componenti senza relazione parent-child.

### Perche' `{!! !!}` nel template AssessmentDetail?

L'API IUCN restituisce HTML formattato nei campi di documentazione (rationale, habitat, threats, etc.). Usare `{{ }}` escaperebbe l'HTML rendendolo testo visibile. `{!! !!}` lo renderizza come HTML effettivo. I CSS vengono applicati tramite arbitrary variant selectors di Tailwind (`[&_a]:text-emerald-700`) per stilizzare i tag HTML generati dall'API senza modificare il sorgente.

### Come gestisci la cache?

Ogni metodo del service usa `Cache::remember()` con una chiave e un TTL specifici. I dati quasi statici (sistemi, paesi) sono cachati per 1 ora. I dettagli (taxon, assessment) per 5 minuti. Le statistiche del footer per 1 giorno. Il driver di cache e' il database (configurabile in `.env`). In produzione si potrebbe switchare a Redis cambiando una sola variabile d'ambiente.

### Come funziona il deferred loading con `#[Defer]`?

Livewire 4 introduce l'attributo `#[Defer]` per il caricamento differito nativo. Quando un componente ha `#[Defer]`, al primo rendering Livewire salta `mount()` e mostra il contenuto di `placeholder()` — uno skeleton con animazioni CSS. Subito dopo, Alpine.js esegue `$wire.__lazyLoad()` via `x-init`, che triggera `mount()` sul server via AJAX. Il componente renderizzato sostituisce lo skeleton. Questo pattern elimina la necessita' di gestire manualmente una proprieta' `$loading` e le direttive `wire:init` nel template, producendo codice piu' pulito e idiomatico.
