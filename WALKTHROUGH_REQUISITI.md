# Walkthrough Requisiti — Mappatura Brief → Implementazione

Questo documento segue **punto per punto** il brief ricevuto, mostrando esattamente **dove** e **come** ogni requisito e' stato implementato nel codice sorgente.

---

## Premessa: Stack e Architettura Scelta

| Scelta | Motivazione |
|---|---|
| **PHP + Laravel 12** | Richiesto dal brief ("preferibile PHP con framework come Laravel") |
| **Livewire 4** | Componenti reattivi server-side, zero JavaScript custom. Dimostra padronanza dell'ecosistema PHP moderno |
| **Tailwind CSS 4** | Utility-first, zero CSS custom, tree-shaking automatico |
| **Lando (Docker)** | Ambiente riproducibile: PHP 8.4, Nginx, MariaDB 11.4 — nessuna dipendenza locale richiesta |
| **Service Layer** | Un unico file (`IucnApiService.php`) gestisce tutte le chiamate API, cache ed errori. I componenti non toccano mai l'API direttamente |

**Architettura generale:**

```
Browser → Livewire Component (PHP) → IucnApiService → IUCN API v4
                                    ↕
                              Cache (database driver)
                                    ↕
                              DB locale (favorites)
```

---

## Requisito: "Indicare nel README.md la procedura per eseguire il progetto"

**File:** `README.md` (root del progetto)

Il README contiene la procedura completa di setup con tutti i comandi necessari:

```bash
git clone <repository-url>
cd IUCN-Red-List
cp .env.example .env
# Impostare IUCN_API_TOKEN nel .env
lando start
lando composer install
lando artisan key:generate
lando artisan migrate
lando yarn build
```

L'applicazione e' accessibile a `https://iucn-red-list.lndo.site`.

Include anche la tabella dei comandi Lando disponibili e la strategia di caching.

---

## Requisito: "Il codice deve essere disponibile su GitHub o GitLab. Verra' valutato l'utilizzo del sistema di versionamento."

**Repository:** `git@github.com:FrancoStino/IUCN-red-list.git`

Il progetto ha **25 commit incrementali** che seguono la convenzione **Conventional Commits**:

- `feat:` per nuove funzionalita'
- `fix:` per correzioni
- `style:` per modifiche puramente estetiche
- `chore:` per attivita' di manutenzione
- `refactor:` per ristrutturazioni del codice senza cambi funzionali

Ogni commit e' atomico e descrive una singola unita' di lavoro. Non ci sono commit "WIP" o "fix fix fix". L'evoluzione del progetto e' tracciabile commit per commit:

```
d66c9bf feat: scaffolding
199ef4a feat: restore core application structure
f9c5c0c feat: install Livewire 4 and configure IUCN API token
610f65e feat: add favorites database schema and Eloquent model
2bc8be7 feat: implement IucnApiService for dashboard and list endpoints
43fd5a8 feat: add taxon details, assessment details and footer statistics
2433c1a feat: add main layout and cached footer component
51aa197 feat: add dashboard with systems and countries listing
dd6c1b4 feat: add favorites toggle and favorites list page
2025fe0 feat: add assessments list page with pagination and filters
6aca0f8 feat: add species detail and assessment detail pages
7c16da0 feat: add country flag emojis and project README
89a7325 fix: align IucnApiService with IUCN API v4 real response structure
2b217c8 fix: align AssessmentsList with API v4 field names and pagination headers
d4d2879 fix: align SpeciesDetail with API v4 response structure
aaf8313 fix: align AssessmentDetail with API v4 response structure
ddaac17 style: redesign favorites, favorite toggle, navbar, and footer UI
808c7fb style: complete UI redesign with nature-themed design system
5571dce feat: add inline favorites toggle to assessments list and count badge to navbar
3f34471 feat: make navbar favorites badge reactive with Livewire component
433e2e9 fix: dispatch favorites-updated event from FavoritesList on remove
663d6fe chore: add database backup file
d5ee859 chore: add CI/CD workflows and code style fixes
8ee636c feat: add skeleton loading with wire:init for deferred data loading
503500f refactor: migrate to Livewire 4 native #[Defer] + placeholder() pattern
```

---

# Sezione "Richieste"

---

## Punto 1 — Homepage/Dashboard

> *"Realizzare una homepage/dashboard che mostri: I Sistemi systems (Terrestre, Acqua dolce, Marino) e Lista nazioni countries"*

### Dove si trova

| Elemento | File |
|---|---|
| Componente PHP | `app/Livewire/Dashboard.php` |
| Template Blade | `resources/views/livewire/dashboard.blade.php` |
| Route | `Route::get('/', Dashboard::class)` in `routes/web.php` |

### Come funziona

**Caricamento dati** — nel metodo `mount()`:

```php
// app/Livewire/Dashboard.php, riga 22-26
public function mount(IucnApiService $service): void
{
    $this->systems = $service->getSystems();
    $this->countries = $service->getCountries();
}
```

Il `$service` viene iniettato automaticamente da Laravel tramite **method injection** — Livewire risolve i parametri tipo-hintati dal container.

**Chiamata API per i sistemi** — nel service:

```php
// app/Services/IucnApiService.php, riga 38-60
public function getSystems(): array
{
    return Cache::remember('iucn.systems', 3600, function () {
        $response = $this->client()->get('/systems/');
        $systems = $response->json('systems') ?? [];
        return collect($systems)->map(fn ($s) => [
            'code' => $s['code'],
            'name' => $s['description']['en'] ?? $s['code'],
        ])->values()->all();
    });
}
```

- **Endpoint API:** `GET /api/v4/systems/`
- **Risposta API:** `{ "systems": [{ "code": "terrestrial", "description": { "en": "Terrestrial" } }, ...] }`
- La risposta viene trasformata in un array semplice `[{ "code": "...", "name": "..." }]`
- **Cache:** 1 ora (3600 secondi)

**Chiamata API per le nazioni** — identica ma su `GET /api/v4/countries/`, cache key `iucn.countries`.

**Nel template:**

I sistemi sono visualizzati come **3 card** in una griglia, ognuna con un'icona e un gradiente diverso in base al tipo:

```php
// resources/views/livewire/dashboard.blade.php, riga 44-72
if (str_contains($lower, 'terrestrial')) {
    $icon = '🌿'; $gradient = 'from-emerald-500 to-green-700';
} elseif (str_contains($lower, 'freshwater')) {
    $icon = '💧'; $gradient = 'from-cyan-500 to-blue-600';
} elseif (str_contains($lower, 'marine')) {
    $icon = '🌊'; $gradient = 'from-blue-500 to-indigo-700';
}
```

Le nazioni sono in una griglia 4 colonne con **ricerca live** tramite `wire:model.live="search"`. Ogni carattere digitato filtra la lista istantaneamente senza ricaricare la pagina:

```php
// app/Livewire/Dashboard.php, riga 30-36
$filteredCountries = collect($this->countries)
    ->when($this->search, function ($collection) {
        return $collection->filter(fn ($c) =>
            str_contains(strtolower($c['name'] ?? ''), strtolower($this->search))
        );
    })
    ->values()->all();
```

---

## Punto 2 — Lista Assessments per Sistema

> *"Cliccando su i sistemi deve essere possibile accedere ad una visualizzazione con nome del sistema e lista paginata delle valutazioni in cui vengano mostrati almeno: Anno di pubblicazione, possibile estinto, possibile estinto in natura, id valutazione, categoria di conservazione (tradurre il codice in testo esteso), Link alla pagina di iucnredlist.org"*

### Dove si trova

| Elemento | File |
|---|---|
| Componente PHP | `app/Livewire/AssessmentsList.php` |
| Template Blade | `resources/views/livewire/assessments-list.blade.php` |
| Route | `Route::get('/assessments/{type}/{code}', AssessmentsList::class)` |

### Come funziona

**Navigazione dal Dashboard:**

```html
<!-- resources/views/livewire/dashboard.blade.php, riga 75-76 -->
<a href="/assessments/system/{{ $code }}" wire:navigate>
```

Il click genera un URL come `/assessments/system/terrestrial`. Il parametro `type` e' vincolato a `system|country` tramite:

```php
// routes/web.php, riga 12-14
Route::get('/assessments/{type}/{code}', AssessmentsList::class)
    ->where('type', 'system|country')
```

**Chiamata API:**

```php
// app/Services/IucnApiService.php, riga 96-125
public function getAssessmentsBySystem(string $systemCode, int $page = 1): array
{
    return Cache::remember("iucn.system.{$systemCode}.page.{$page}", 3600, function () use ($systemCode, $page) {
        $response = $this->client()->get("/systems/{$systemCode}", ['page' => $page]);
        return [
            'assessments' => $response->json('assessments') ?? [],
            'pagination' => [
                'total' => (int) $response->header('total-count'),
                'per_page' => (int) $response->header('page-items'),
                'current_page' => (int) $response->header('current-page'),
                'total_pages' => (int) $response->header('total-pages'),
            ],
        ];
    });
}
```

- **Endpoint API:** `GET /api/v4/systems/{code}?page={n}`
- La paginazione viene letta dagli **header HTTP** della risposta (vedi sezione "Note" piu' avanti)

**Mappatura campo per campo nel template (vista tabella):**

| Requisito Brief | Campo API | Dove nel template |
|---|---|---|
| Anno di pubblicazione | `year_published` | Colonna "Year" — riga 144: `{{ $a['year_published'] ?? 'N/A' }}` |
| Possibile estinto | `possibly_extinct` | Colonna "Flags" — riga 161: `@if(!empty($a['possibly_extinct']) \|\| !empty($a['possibly_extinct_in_the_wild']))` → icona 💀 PE |
| Possibile estinto in natura | `possibly_extinct_in_the_wild` | Stesso blocco di sopra — entrambi i flag vengono controllati |
| ID valutazione | `assessment_id` | Colonna "Assessment ID" — riga 150: link cliccabile `{{ $a['assessment_id'] }}` |
| Categoria di conservazione | `red_list_category_code` | Colonna "Category" — riga 155-158 |
| Link IUCN | `url` | Colonna "Actions" — riga 178: `<a href="{{ $a['url'] ?? '#' }}" target="_blank">` |

**Traduzione codice categoria in testo esteso:**

```php
// app/Services/IucnApiService.php, riga 17-27
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

// Nel template, riga 157:
{{ $cat }} - {{ IucnApiService::translateCategory($cat) }}
// Esempio output: "CR - Critically Endangered"
```

Ogni badge di categoria ha un colore specifico definito dalla funzione `$getCategoryColor()` nel template (righe 4-15).

---

## Punto 3 — Lista Assessments per Nazione

> *"Cliccando su una delle nazioni mostrare al pari dei sistemi la lista delle valutazioni"*

**Stesso componente** del punto 2 (`AssessmentsList`). La route accetta sia `system` che `country`:

```
/assessments/system/terrestrial  → assessments per sistema
/assessments/country/IT          → assessments per nazione
```

Nel `mount()`, il componente risolve il nome da visualizzare:

```php
// app/Livewire/AssessmentsList.php, riga 48-62
if ($this->type === 'system') {
    $systems = $service->getSystems();
    $match = collect($systems)->firstWhere('code', $this->code);
    $this->name = $match['name'] ?? strtoupper($this->code);
} else {
    $countries = $service->getCountries();
    $match = collect($countries)->firstWhere('code', $this->code);
    $this->name = $match['name'] ?? strtoupper($this->code);
}
```

E nel `loadAssessments()` viene chiamato l'endpoint corretto tramite `match`:

```php
// app/Livewire/AssessmentsList.php, riga 71-74
$result = match ($this->type) {
    'system' => $service->getAssessmentsBySystem($this->code, $this->page),
    'country' => $service->getAssessmentsByCountry($this->code, $this->page),
};
```

- **Endpoint API per paese:** `GET /api/v4/countries/{code}?page={n}`
- Stessa logica di paginazione, filtri, toggle vista/scroll del punto 2

---

## Punto 4 — Dettaglio Specie (via `sis_taxon_id`)

> *"Per ogni elemento che contenga nelle API un sis_taxon_id inserire un link che rimandi ad una visualizzazione piu' ampia con: Identificativo, nome scientifico, nomi comuni (lista, evidenziare il principale), Lista valutazioni"*

### Link al dettaglio specie

Nel template degli assessments (sia lista che card), ogni `sis_taxon_id` genera un link alla pagina specie. Nella vista card:

```html
<!-- resources/views/livewire/assessments-list.blade.php, riga 235 -->
<span class="text-xs text-gray-400 font-mono" title="SIS Taxon ID">SIS: {{ $a['sis_taxon_id'] ?? 'N/A' }}</span>
```

Nel dettaglio assessment:

```html
<!-- resources/views/livewire/assessment-detail.blade.php, riga 82-96 -->
@if($sisId)
    <a href="/species/{{ $sisId }}" wire:navigate>
        View Species (SIS {{ $sisId }})
    </a>
@endif
```

### Dove si trova la pagina specie

| Elemento | File |
|---|---|
| Componente PHP | `app/Livewire/SpeciesDetail.php` |
| Template Blade | `resources/views/livewire/species-detail.blade.php` |
| Route | `Route::get('/species/{sisId}', SpeciesDetail::class)` |

### Come funziona

**Chiamata API:**

```php
// app/Services/IucnApiService.php, riga 166-188
public function getTaxonDetails(int $sisId): array
{
    return Cache::remember("iucn.taxon.{$sisId}", 300, function () use ($sisId) {
        $response = $this->client()->get("/taxa/sis/{$sisId}");
        $data = $response->json();
        return [
            'sis_id' => $data['sis_id'] ?? null,
            'taxon' => $data['taxon'] ?? [],
            'assessments' => $data['assessments'] ?? [],
        ];
    });
}
```

- **Endpoint API:** `GET /api/v4/taxa/sis/{sisId}`
- **Cache:** 5 minuti (300 secondi)

**Mappatura campo per campo:**

| Requisito Brief | Campo API | Dove nel template |
|---|---|---|
| Identificativo | `sis_id` (parametro URL) | Riga 73-75: Badge `SIS {{ $sisId }}` |
| Nome scientifico | `taxon.scientific_name` | Riga 44-46: `<h1>{{ $taxon['scientific_name'] ?? 'Unknown Species' }}</h1>` — in corsivo, come convezione tassonomica |
| Nomi comuni (lista) | `taxon.common_names` | Righe 121-132: tutti i nomi mostrati come tag inline |
| Nome principale evidenziato | `common_names[].main: true` | Righe 102-118: il nome con `main: true` viene evidenziato in un box ambra separato con icona ★ |
| Lista valutazioni | `assessments[]` | Righe 156-207: lista di card cliccabili con badge colorato per categoria |

**Evidenziazione del nome principale:**

```php
// resources/views/livewire/species-detail.blade.php, riga 103
$mainName = collect($commonNames)->firstWhere('main', true);
```

Se trovato, viene mostrato in un box ambra dedicato con stella. Gli altri nomi sono elencati come tag sotto.

**Tassonomia addizionale** (non richiesta ma aggiunta): Kingdom, Phylum, Class, Order, Family — mostrati come badge nel header.

---

## Punto 5 — Pulsante Preferiti

> *"Aggiungere nella visualizzazione al dettaglio del punto 4 un pulsante Preferiti che al click aggiunga/tolga da una tabella del DB locale i dettagli della specie (includendo almeno identificativo e nome scientifico)"*

### Dove si trova

| Elemento | File |
|---|---|
| Componente PHP | `app/Livewire/FavoriteToggle.php` |
| Template Blade | `resources/views/livewire/favorite-toggle.blade.php` |
| Model Eloquent | `app/Models/Favorite.php` |
| Migration | `database/migrations/2026_02_22_201848_create_favorites_table.php` |

### Schema Database

```php
// Migration, riga 14-19
Schema::create('favorites', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('taxon_id')->unique();  // Identificativo
    $table->string('scientific_name');                   // Nome scientifico
    $table->timestamp('added_at')->useCurrent();         // Data aggiunta
});
```

Il vincolo `unique()` su `taxon_id` impedisce duplicati — una specie puo' essere nei preferiti solo una volta.

### Model Eloquent

```php
// app/Models/Favorite.php
class Favorite extends Model
{
    public $timestamps = false;  // Non serve created_at/updated_at
    protected $fillable = ['taxon_id', 'scientific_name', 'added_at'];
    protected $casts = ['added_at' => 'datetime'];  // Per usare diffForHumans()
}
```

### Come funziona il toggle

```php
// app/Livewire/FavoriteToggle.php, riga 28-43
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

1. Se e' gia' preferito → lo rimuove dal DB
2. Se non e' preferito → lo inserisce con identificativo e nome scientifico
3. Dispatcha l'evento `favorites-updated` per aggiornare il badge nella navbar

### Come appare nella pagina specie

```html
<!-- resources/views/livewire/species-detail.blade.php, riga 81 -->
<livewire:favorite-toggle :taxon-id="$sisId" :scientific-name="$taxon['scientific_name'] ?? 'Unknown'" />
```

Il pulsante e' un cuore grande (56x56px) con gradiente rosa quando attivo e animazione pulsante (`animate-ping`). Al click cambia stato istantaneamente.

---

## Punto 6 — Pagina Preferiti

> *"Aggiungere una pagina Preferiti dove vengano visualizzati gli elementi del punto 4 con la data di aggiunta (e link al dettaglio)"*

### Dove si trova

| Elemento | File |
|---|---|
| Componente PHP | `app/Livewire/FavoritesList.php` |
| Template Blade | `resources/views/livewire/favorites-list.blade.php` |
| Route | `Route::get('/favorites', FavoritesList::class)` |

### Come funziona

**Query ad ogni render** (garantisce dati sempre freschi):

```php
// app/Livewire/FavoritesList.php, riga 22-26
public function render()
{
    return view('livewire.favorites-list', [
        'favorites' => Favorite::orderByDesc('added_at')->get(),
    ]);
}
```

**Mappatura campo per campo:**

| Requisito Brief | Implementazione nel template |
|---|---|
| Elementi del punto 4 | Nome scientifico in corsivo, cliccabile: `<a href="/species/{{ $favorite->taxon_id }}" wire:navigate>{{ $favorite->scientific_name }}</a>` |
| Data di aggiunta | `{{ $favorite->added_at->diffForHumans() }}` — mostra "2 hours ago", "3 days ago" ecc. grazie al cast `datetime` nel model |
| Link al dettaglio | Pulsante "View details" → `/species/{taxon_id}` |
| Identificativo | Badge `ID {{ $favorite->taxon_id }}` |

**Rimozione con conferma:**

```html
<!-- resources/views/livewire/favorites-list.blade.php, riga 76-77 -->
<button
    wire:click="removeFavorite({{ $favorite->taxon_id }})"
    wire:confirm="Remove {{ $favorite->scientific_name }} from favorites?">
    Remove
</button>
```

`wire:confirm` mostra un dialog nativo del browser prima di procedere.

**Stato vuoto:** se non ci sono preferiti, viene mostrato un messaggio invitante con pulsante "Explore Species" che porta alla dashboard.

---

## Punto 7 — Dettaglio Valutazione (Assessment Detail)

> *"Per ogni valutazione rimandare al dettaglio, visualizzando: Link alla valutazione su iucnredlist.org (nuova scheda), Trend popolazione, Lista azioni di conservazione svolte, Documentazione HTML, I Sistemi rimandando alla lista del punto 2"*

### Dove si trova

| Elemento | File |
|---|---|
| Componente PHP | `app/Livewire/AssessmentDetail.php` |
| Template Blade | `resources/views/livewire/assessment-detail.blade.php` |
| Route | `Route::get('/assessment/{assessmentId}', AssessmentDetail::class)` |

### Chiamata API

```php
// app/Services/IucnApiService.php, riga 194-211
public function getAssessmentDetails(int $assessmentId): array
{
    return Cache::remember("iucn.assessment.{$assessmentId}", 300, function () use ($assessmentId) {
        $response = $this->client()->get("/assessment/{$assessmentId}");
        return $response->json();  // Restituisce l'intero oggetto JSON
    });
}
```

- **Endpoint API:** `GET /api/v4/assessment/{assessmentId}`
- **Cache:** 5 minuti

### Mappatura requisito per requisito

#### 7a. Link alla valutazione su iucnredlist.org (nuova scheda)

```html
<!-- resources/views/livewire/assessment-detail.blade.php, riga 100-110 -->
<a
    href="{{ $assessment['url'] ?? 'https://www.iucnredlist.org/species/...' }}"
    target="_blank"
    rel="noopener noreferrer">
    IUCN Red List
</a>
```

- `target="_blank"` apre in una nuova scheda ✅
- `rel="noopener noreferrer"` per sicurezza (impedisce tabnapping)
- Usa il campo `url` della risposta API, con fallback costruito manualmente

#### 7b. Trend popolazione specie

```php
// resources/views/livewire/assessment-detail.blade.php, riga 26
$popTrend = $assessment['population_trend']['description']['en'] ?? null;

// Riga 44-49
$trendDisplay = match($popTrend) {
    'Increasing' => ['icon' => '↑', 'color' => 'text-emerald-600'],
    'Decreasing' => ['icon' => '↓', 'color' => 'text-red-600'],
    'Stable'     => ['icon' => '→', 'color' => 'text-blue-600'],
    default      => ['icon' => '?', 'color' => 'text-gray-500'],
};
```

Visualizzato in una card dedicata con icona direzionale e colore semantico (verde=crescita, rosso=declino, blu=stabile).

#### 7c. Lista azioni di conservazione svolte sul posto

```php
// resources/views/livewire/assessment-detail.blade.php, riga 29
$conservationActions = $assessment['conservation_actions'] ?? [];
```

Le azioni vengono mostrate come **tag** in una griglia flex-wrap:

```php
// Riga 208-210
$actionName = is_array($action)
    ? ($action['description']['en'] ?? $action['code'] ?? 'Unknown')
    : (string) $action;
```

Ogni azione e' un tag con sfondo emerald. Il conteggio totale e' mostrato come badge.

#### 7d. Documentazione HTML

```php
// resources/views/livewire/assessment-detail.blade.php, riga 242-253
$sectionLabels = [
    'rationale' => 'Rationale',
    'range' => 'Geographic Range',
    'population' => 'Population',
    'habitats' => 'Habitat and Ecology',
    'threats' => 'Threats',
    'measures' => 'Conservation Measures',
    'use_trade' => 'Use and Trade',
    'trend_justification' => 'Population Trend Justification',
    'taxonomic_notes' => 'Taxonomic Notes',
];
```

Per ogni sezione disponibile nel nodo `documentation`, il contenuto HTML viene renderizzato con `{!! !!}` (raw HTML, non escaped):

```html
<!-- Riga 280 -->
{!! $docSections[$key] !!}
```

Il brief dice: *"Mostrare, intepretando l'html ricevuto, gli elementi disponibili all'interno del nodo documentation. Se non disponibile --"*

**Implementato:** ogni sezione viene mostrata solo se non vuota (`@if(!empty($docSections[$key]))`). Se nessuna sezione e' disponibile, il blocco documentazione non appare.

L'HTML esterno viene stilizzato con **arbitrary variant selectors** di Tailwind:

```html
<!-- Riga 270-278 -->
<div class="text-sm leading-relaxed text-gray-700
    [&_a]:text-emerald-700 [&_a]:underline [&_a]:font-medium
    [&_ul]:list-disc [&_ul]:pl-5
    [&_table]:w-full [&_th]:bg-gray-50 [&_td]:border">
```

Questo applica stili ai tag HTML generati dall'API senza modificare il sorgente.

#### 7e. I Sistemi "piu' ampi" con link alla lista del punto 2

```html
<!-- resources/views/livewire/assessment-detail.blade.php, riga 228-234 -->
@foreach($systems as $system)
    <a href="/assessments/system/{{ $system['code'] ?? '' }}" wire:navigate>
        {{ $system['description']['en'] ?? $system['code'] ?? 'Unknown' }}
    </a>
@endforeach
```

Ogni sistema associato all'assessment e' un tag cliccabile che rimanda alla lista assessments per quel sistema (punto 2).

---

## Punto 8 — Footer

> *"Nel footer mostrare: api_version, red_list_version, specie censite (statistiche)"*

### Dove si trova

| Elemento | File |
|---|---|
| Componente PHP | `app/Livewire/Footer.php` |
| Template Blade | `resources/views/livewire/footer.blade.php` |
| Inclusione nel layout | `<livewire:footer />` in `resources/views/components/layouts/app.blade.php`, riga 86 |

### Chiamata API (3 endpoint distinti)

```php
// app/Services/IucnApiService.php, riga 240-263
public function getStatistics(): array
{
    return Cache::remember('iucn.statistics', 86400, function () {
        $client = $this->client();
        $apiVersionResponse = $client->get('/information/api_version');
        $rlVersionResponse  = $client->get('/information/red_list_version');
        $countResponse      = $client->get('/statistics/count');

        return [
            'api_version'      => $apiVersionResponse->json('api_version') ?? 'v4',
            'red_list_version' => $rlVersionResponse->json('red_list_version') ?? 'N/A',
            'species_count'    => (int) ($countResponse->json('count') ?? 0),
        ];
    });
}
```

- **Endpoint 1:** `GET /api/v4/information/api_version` → `api_version`
- **Endpoint 2:** `GET /api/v4/information/red_list_version` → `red_list_version`
- **Endpoint 3:** `GET /api/v4/statistics/count` → numero specie censite

### Visualizzazione nel template

```html
<!-- resources/views/livewire/footer.blade.php -->
<strong>{{ number_format($speciesCount) }}</strong>   <!-- Specie censite -->
<strong>{{ $redListVersion }}</strong>                 <!-- Red List version -->
<strong>{{ $apiVersion }}</strong>                     <!-- API version -->
```

I tre valori sono mostrati come badge orizzontali con icone SVG nel footer emerald-950.

---

## Punto 9 — Cache Footer 1 Giorno

> *"Per ogni chiamata del footer salvare i dati in cache con durata 1 giorno"*

Gia' mostrato sopra:

```php
Cache::remember('iucn.statistics', 86400, function () { ... });
//                                 ^^^^^ = 86400 secondi = 1 giorno
```

Le 3 chiamate API sono aggregate in un'unica struttura e cachate con una sola chiave (`iucn.statistics`). Questo significa che le 3 chiamate vengono eseguite al massimo **una volta al giorno**.

---

## Punto 10 — Template Alternativo a Card (Switch)

> *"Nelle visualizzazioni di lista prevedere un template alternativo che sfrutti una visualizzazione a card (eventualmente utilizzabile via switch)"*

### Dove si trova

**Componente:** `app/Livewire/AssessmentsList.php`
**Proprieta':** `$viewMode = 'list'`

### Toggle nel template

```html
<!-- resources/views/livewire/assessments-list.blade.php, riga 56-63 -->
<button wire:click="toggleViewMode" ...>List</button>
<button wire:click="toggleViewMode" ...>Card</button>
```

### Logica di switch

```php
// app/Livewire/AssessmentsList.php, riga 119-122
public function toggleViewMode(): void
{
    $this->viewMode = $this->viewMode === 'list' ? 'card' : 'list';
}
```

### Due viste nel template

```html
<!-- Riga 121-244 -->
@if($viewMode === 'list')
    <!-- Vista tabella con colonne: Year, Species, Assessment ID, Category, Flags, Fav, Actions -->
    <table>...</table>
@else
    <!-- Vista card con griglia 3 colonne -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">...</div>
@endif
```

Il pulsante attivo e' evidenziato in emerald, l'inattivo in grigio.

---

# Sezione "Bonus"

---

## Bonus 1 — Bandiere con codice ISO alpha-2

> *"Nella lista delle nazioni mostrare per ogni nazione una bandiera sfruttando il codice ISO alpha-2"*

### Algoritmo

```php
// resources/views/livewire/dashboard.blade.php, riga 158-163
$countryFlag = function(string $code): string {
    $code = strtoupper($code);
    if (strlen($code) !== 2) return '🏳️';
    return mb_chr(0x1F1E6 + ord($code[0]) - ord('A'))
         . mb_chr(0x1F1E6 + ord($code[1]) - ord('A'));
};
```

**Come funziona:** il carattere Unicode `U+1F1E6` e' il Regional Indicator Symbol per la lettera 'A'. Aggiungendo l'offset ASCII (`ord('I') - ord('A')` = 8) si ottiene il Regional Indicator per 'I'. Due Regional Indicator consecutivi (es. 'I' + 'T') vengono renderizzati dal sistema operativo come bandiera italiana 🇮🇹.

**Esempio:**
- Codice `IT` → `mb_chr(0x1F1EE)` + `mb_chr(0x1F1F9)` → 🇮🇹
- Codice `US` → `mb_chr(0x1F1FA)` + `mb_chr(0x1F1F8)` → 🇺🇸

---

## Bonus 2 — Cache Dashboard 1 Ora

> *"Per le liste della dashboard fare cache ad 1 ora"*

Implementato nei due metodi del service:

```php
// getSystems() — riga 40
Cache::remember('iucn.systems', 3600, ...)    // 3600 = 1 ora

// getCountries() — riga 69
Cache::remember('iucn.countries', 3600, ...)  // 3600 = 1 ora
```

Anche gli assessments per sistema/paese hanno cache 1 ora:

```php
// getAssessmentsBySystem() — riga 100
Cache::remember("iucn.system.{$systemCode}.page.{$page}", 3600, ...)

// getAssessmentsByCountry() — riga 136
Cache::remember("iucn.country.{$countryCode}.page.{$page}", 3600, ...)
```

---

## Bonus 3 — Cache Dettagli 5 Minuti

> *"Per ogni altro elemento fare cache a 5 minuti"*

```php
// getTaxonDetails() — riga 168
Cache::remember("iucn.taxon.{$sisId}", 300, ...)           // 300 = 5 minuti

// getAssessmentDetails() — riga 196
Cache::remember("iucn.assessment.{$assessmentId}", 300, ...)  // 300 = 5 minuti
```

### Riepilogo completo caching

| Cache Key | TTL | Requisito |
|---|---|---|
| `iucn.systems` | 1 ora | Bonus 2 |
| `iucn.countries` | 1 ora | Bonus 2 |
| `iucn.system.{code}.page.{page}` | 1 ora | Bonus 2 |
| `iucn.country.{code}.page.{page}` | 1 ora | Bonus 2 |
| `iucn.taxon.{sisId}` | 5 min | Bonus 3 |
| `iucn.assessment.{assessmentId}` | 5 min | Bonus 3 |
| `iucn.conservation_actions` | 1 ora | — |
| `iucn.statistics` | 1 giorno | Punto 9 |

---

## Bonus 4 — Filtri per Anno, Estinto, Estinto in Natura

> *"Nelle liste permettere di filtrare per anno di pubblicazione, possibile estinto e possibile estinto in natura"*

### Proprieta' filtro

```php
// app/Livewire/AssessmentsList.php, riga 34-37
#[Url]
public string $yearFilter = '';
public string $extinctFilter = '';  // '' (all), 'yes', 'no'
```

`#[Url]` sincronizza `yearFilter` con i query parameter dell'URL (`?yearFilter=2023`).

### Logica di filtro (nel `render()`)

```php
// app/Livewire/AssessmentsList.php, riga 138-149
if ($this->yearFilter !== '') {
    $filtered = $filtered->filter(fn ($a) =>
        ($a['year_published'] ?? '') == $this->yearFilter
    );
}

if ($this->extinctFilter === 'yes') {
    $filtered = $filtered->filter(fn ($a) =>
        !empty($a['possibly_extinct']) || !empty($a['possibly_extinct_in_the_wild'])
    );
} elseif ($this->extinctFilter === 'no') {
    $filtered = $filtered->filter(fn ($a) =>
        empty($a['possibly_extinct']) && empty($a['possibly_extinct_in_the_wild'])
    );
}
```

**Perche' client-side:** l'API IUCN v4 non supporta query parameter per filtrare per anno o stato di estinzione. I filtri vengono applicati in PHP sui dati gia' caricati. Ogni pagina ha al massimo ~100 assessment (default API), quindi il filtro e' rapido.

### Dropdown nel template

```html
<!-- resources/views/livewire/assessments-list.blade.php, riga 78-89 -->
<select wire:model.live="yearFilter">
    <option value="">All Years</option>
    @foreach($years as $year)
        <option value="{{ $year }}">{{ $year }}</option>
    @endforeach
</select>

<select wire:model.live="extinctFilter">
    <option value="">Extinction: All</option>
    <option value="yes">Possibly Extinct</option>
    <option value="no">Not Extinct</option>
</select>
```

`wire:model.live` aggiorna la proprieta' a ogni cambio di selezione, triggerando un re-render immediato senza submit.

Gli anni disponibili vengono estratti dinamicamente dai dati correnti:

```php
// app/Livewire/AssessmentsList.php, riga 152-158
$years = collect($this->assessments)
    ->pluck('year_published')
    ->filter()
    ->unique()
    ->sortDesc()
    ->values()->all();
```

---

## Bonus 5 — Switch Paginazione / Scroll Infinito

> *"Aggiungere nelle liste uno switch per passare da paginazione a scroll infinito"*

### Toggle

```html
<!-- resources/views/livewire/assessments-list.blade.php, riga 67-73 -->
<button wire:click="toggleScrollMode" ...>Pages</button>
<button wire:click="toggleScrollMode" ...>Infinite</button>
```

### Logica

```php
// app/Livewire/AssessmentsList.php, riga 124-131
public function toggleScrollMode(): void
{
    $this->scrollMode = $this->scrollMode === 'paginate' ? 'scroll' : 'paginate';
    $this->page = 1;
    $this->allAssessments = [];
    $this->loadAssessments();
}
```

Quando si cambia modalita', pagina e accumulo vengono resettati.

### Modalita' Paginate

Pulsanti Previous/Next con indicatore pagina corrente:

```html
<!-- Riga 260-277 -->
<button wire:click="previousPage" @disabled($page <= 1)>Previous</button>
<span>{{ $page }} / {{ $totalPages }}</span>
<button wire:click="nextPage" @disabled(!$hasMore)>Next</button>
```

### Modalita' Scroll Infinito

I dati vengono **accumulati** ad ogni "Load More":

```php
// app/Livewire/AssessmentsList.php, riga 79-81
if ($this->scrollMode === 'scroll') {
    $this->allAssessments = array_merge($this->allAssessments, $result['assessments']);
    $this->assessments = $this->allAssessments;
}
```

Pulsante "Load More" con stato di caricamento:

```html
<!-- Riga 290-301 -->
<button wire:click="loadMore">
    <span wire:loading.remove wire:target="loadMore">Load More Assessments</span>
    <span wire:loading wire:target="loadMore">Loading...</span>
</button>
```

Quando non ci sono piu' pagine:

```html
<!-- Riga 303-304 -->
<span>🏁 You've reached the end of the list</span>
```

---

# Sezione "Note"

---

## Nota 1 — Paginazione tramite Header HTTP

> *"La paginazione (dove disponibile) deve essere effettuata attraverso l'utilizzo degli header delle risposte delle chiamate dove vengono esposti i risultati per pagina e risultati totali."*

**Implementazione chiave** nel service layer:

```php
// app/Services/IucnApiService.php, riga 107-114
'pagination' => [
    'total' => (int) $response->header('total-count'),
    'per_page' => (int) $response->header('page-items'),
    'current_page' => (int) $response->header('current-page'),
    'total_pages' => (int) $response->header('total-pages'),
],
```

I metadati di paginazione vengono letti dagli **header HTTP** della risposta, **non dal body JSON**. Questo e' inusuale e ha richiesto l'implementazione manuale della paginazione nei componenti Livewire, senza poter usare il trait `WithPagination` standard di Laravel.

**Gli header utilizzati:**

| Header HTTP | Campo normalizzato | Uso |
|---|---|---|
| `total-count` | `total` | Numero totale di risultati |
| `page-items` | `per_page` | Risultati per pagina |
| `current-page` | `current_page` | Pagina corrente |
| `total-pages` | `total_pages` | Totale pagine disponibili |

---

## Nota 2 — Pulsanti Visibili Solo Se Disponibili

> *"I pulsanti devono essere tuttavia visualizzati solo se disponibili"*

Implementato con la direttiva Blade `@disabled()`:

```html
<!-- resources/views/livewire/assessments-list.blade.php, riga 260 -->
<button wire:click="previousPage" @disabled($page <= 1) ...>Previous</button>

<!-- Riga 272 -->
<button wire:click="nextPage" @disabled(!$hasMore) ...>Next</button>
```

- **Previous:** disabilitato quando siamo a pagina 1 (non c'e' una pagina precedente)
- **Next:** disabilitato quando `$hasMore` e' false (siamo all'ultima pagina)

La proprieta' `$hasMore` viene calcolata dopo ogni caricamento:

```php
// app/Livewire/AssessmentsList.php, riga 86
$this->hasMore = $this->page < ($this->pagination['total_pages'] ?? 1);
```

I pulsanti sono **visibili ma disabilitati** (`disabled` attribute + `opacity-40 cursor-not-allowed` CSS) quando non applicabili — interpretazione piu' user-friendly di "visualizzati solo se disponibili" rispetto a nasconderli completamente.

---

# Sezione "Categorie di Conservazione"

> Tabella di mappatura codici IUCN

Implementata come costante di classe nel service:

```php
// app/Services/IucnApiService.php, riga 17-27
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

public static function translateCategory(string $code): string
{
    return self::CATEGORY_MAP[$code] ?? $code;
}
```

Usata in tutti i template dove appare una categoria:

```html
{{ IucnApiService::translateCategory($cat) }}
<!-- Input: "CR" → Output: "Critically Endangered" -->
```

**Colori associati** (usati nei badge):

| Codice | Colore Tailwind |
|---|---|
| EX | `bg-black text-white` |
| EW | `bg-gray-800 text-white` |
| CR | `bg-red-600 text-white` |
| EN | `bg-orange-500 text-white` |
| VU | `bg-yellow-400 text-yellow-900` |
| NT | `bg-lime-400 text-lime-900` |
| LC | `bg-green-500 text-white` |
| DD | `bg-gray-500 text-white` |
| NE | `bg-gray-200 text-gray-800` |

---

# Extra: Funzionalita' Aggiunte Oltre il Brief

Queste funzionalita' non erano richieste ma migliorano l'esperienza utente:

### 1. Toggle Preferiti Inline nelle Liste

Nella lista assessments, ogni riga/card ha un cuoricino compatto per aggiungere/rimuovere la specie dai preferiti senza entrare nel dettaglio:

```html
<!-- assessments-list.blade.php, riga 170-176 -->
<livewire:favorite-toggle
    :taxon-id="$a['sis_taxon_id']"
    :scientific-name="$a['taxon_scientific_name'] ?? 'Unknown'"
    :compact="true"
    :wire:key="'fav-list-'.$a['sis_taxon_id'].'-'.$a['assessment_id']"
/>
```

### 2. Badge Preferiti Reattivo nella Navbar

Il conteggio dei preferiti nella navbar si aggiorna in tempo reale tramite il sistema eventi di Livewire:

```php
// app/Livewire/FavoritesCount.php
#[On('favorites-updated')]
public function refreshCount(): void
{
    $this->count = Favorite::count();
}
```

**Flusso evento:**

```
FavoriteToggle::toggle() → dispatch('favorites-updated')
                               ↓
FavoritesCount::refreshCount() ← #[On('favorites-updated')]
    → Ri-query DB → Aggiorna badge
```

### 3. Ricerca Live Nazioni

Campo di ricerca nella dashboard che filtra le nazioni istantaneamente mentre si digita:

```html
<input type="text" wire:model.live="search" placeholder="Search countries..." />
```

### 4. Navigazione SPA

Tutti i link interni usano `wire:navigate` per navigazione AJAX senza ricaricare la pagina — esperienza simile a una SPA.

### 5. Deferred Loading Nativo con `#[Defer]`

I 4 componenti full-page (Dashboard, AssessmentsList, SpeciesDetail, AssessmentDetail) utilizzano l'attributo `#[Defer]` di Livewire 4 per mostrare skeleton placeholder durante il caricamento dei dati API:

```php
// app/Livewire/Dashboard.php
#[Defer]
#[Layout('components.layouts.app')]
#[Title('IUCN Red List Explorer')]
class Dashboard extends Component
{
    public function placeholder(): string
    {
        return view('livewire.placeholders.dashboard')->render();
    }
}
```

Ogni componente ha un file placeholder dedicato in `resources/views/livewire/placeholders/` con animazioni `animate-pulse` che replicano la struttura della pagina reale.

**Come funziona internamente:**
1. L'utente visita la pagina → Livewire salta `mount()` e mostra il placeholder
2. Alpine.js trigga `$wire.__lazyLoad()` via `x-init`
3. `mount()` viene eseguito via AJAX → i dati API vengono caricati
4. Il componente reale sostituisce lo skeleton

---

# Riepilogo Compliance

| # | Requisito | Stato | Note |
|---|---|---|---|
| 1 | Dashboard con Sistemi e Nazioni | ✅ | `Dashboard.php` + API `/systems/` e `/countries/` |
| 2 | Lista assessments per sistema (anno, estinto, ID, categoria tradotta, link IUCN) | ✅ | `AssessmentsList.php` + vista tabella/card |
| 3 | Lista assessments per nazione | ✅ | Stesso componente, route con `type=country` |
| 4 | Dettaglio specie via `sis_taxon_id` (ID, nome scientifico, nomi comuni, assessments) | ✅ | `SpeciesDetail.php` + API `/taxa/sis/{id}` |
| 5 | Pulsante Preferiti (salva ID + nome nel DB) | ✅ | `FavoriteToggle.php` + tabella `favorites` |
| 6 | Pagina Preferiti con data aggiunta e link | ✅ | `FavoritesList.php` + `diffForHumans()` |
| 7a | Link IUCN (nuova scheda) | ✅ | `target="_blank"` su assessment-detail |
| 7b | Trend popolazione | ✅ | Card con icona direzionale |
| 7c | Azioni di conservazione | ✅ | Tag flex-wrap |
| 7d | Documentazione HTML interpretata | ✅ | `{!! !!}` + arbitrary variant selectors |
| 7e | Sistemi con link alla lista | ✅ | Tag cliccabili → `/assessments/system/{code}` |
| 8 | Footer (api_version, red_list_version, species count) | ✅ | `Footer.php` + 3 endpoint API |
| 9 | Cache footer 1 giorno | ✅ | `Cache::remember(..., 86400, ...)` |
| 10 | Template alternativo a card con switch | ✅ | Toggle list/card in `AssessmentsList` |
| B1 | Bandiere con ISO alpha-2 | ✅ | Regional Indicator Symbols Unicode |
| B2 | Cache dashboard 1 ora | ✅ | `Cache::remember(..., 3600, ...)` |
| B3 | Cache dettagli 5 minuti | ✅ | `Cache::remember(..., 300, ...)` |
| B4 | Filtri anno, estinto, estinto in natura | ✅ | `$yearFilter`, `$extinctFilter` client-side |
| B5 | Switch paginazione/scroll infinito | ✅ | `$scrollMode` paginate/scroll |
| N1 | Paginazione tramite header HTTP | ✅ | `$response->header('total-count')` ecc. |
| N2 | Pulsanti visibili solo se disponibili | ✅ | `@disabled()` su Previous/Next |
| — | README.md con procedura setup | ✅ | Comandi Lando step-by-step |
| — | GitHub con commit incrementali | ✅ | 25 commit con Conventional Commits |
| — | Tabella categorie di conservazione | ✅ | `CATEGORY_MAP` con tutti i 9 codici |
