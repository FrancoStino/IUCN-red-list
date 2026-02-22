<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class IucnApiService
{
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

    public function __construct(
        private readonly HttpFactory $http
    ) {}

    /**
     * Get list of systems.
     *
     * @return array<int, array{code: string, name: string}>
     */
    public function getSystems(): array
    {
        return Cache::remember('iucn.systems', 3600, function () {
            try {
                $response = $this->client()->get('/systems/');

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (RequestException | Throwable $e) {
                $this->logError('/systems/', [], $e);
                return [];
            }
        });
    }

    /**
     * Get list of countries.
     *
     * @return array<int, array{code: string, name: string}>
     */
    public function getCountries(): array
    {
        return Cache::remember('iucn.countries', 3600, function () {
            try {
                $response = $this->client()->get('/countries/');

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (RequestException | Throwable $e) {
                $this->logError('/countries/', [], $e);
                return [];
            }
        });
    }

    /**
     * Get assessments by system code with pagination.
     *
     * @return array{assessments: array, pagination: array{total: int, per_page: int, current_page: int}}
     */
    public function getAssessmentsBySystem(string $systemCode, int $page = 1): array
    {
        $cacheKey = "iucn.system.{$systemCode}.page.{$page}";

        return Cache::remember($cacheKey, 3600, function () use ($systemCode, $page) {
            try {
                $response = $this->client()->get("/systems/{$systemCode}", [
                    'page' => $page,
                ]);

                if ($response->successful()) {
                    return [
                        'assessments' => $response->json(),
                        'pagination' => [
                            'total' => (int) $response->header('X-Total'),
                            'per_page' => (int) $response->header('X-Per-Page'),
                            'current_page' => (int) $response->header('X-Page'),
                        ],
                    ];
                }

                return $this->assessmentFallback();
            } catch (RequestException | Throwable $e) {
                $this->logError("/systems/{$systemCode}", ['page' => $page], $e);
                return $this->assessmentFallback();
            }
        });
    }

    /**
     * Get assessments by country code with pagination.
     *
     * @return array{assessments: array, pagination: array{total: int, per_page: int, current_page: int}}
     */
    public function getAssessmentsByCountry(string $countryCode, int $page = 1): array
    {
        $cacheKey = "iucn.country.{$countryCode}.page.{$page}";

        return Cache::remember($cacheKey, 3600, function () use ($countryCode, $page) {
            try {
                $response = $this->client()->get("/countries/{$countryCode}", [
                    'page' => $page,
                ]);

                if ($response->successful()) {
                    return [
                        'assessments' => $response->json(),
                        'pagination' => [
                            'total' => (int) $response->header('X-Total'),
                            'per_page' => (int) $response->header('X-Per-Page'),
                            'current_page' => (int) $response->header('X-Page'),
                        ],
                    ];
                }

                return $this->assessmentFallback();
            } catch (RequestException | Throwable $e) {
                $this->logError("/countries/{$countryCode}", ['page' => $page], $e);
                return $this->assessmentFallback();
            }
        });
    }

    /**
     * Get taxon details by SIS ID.
     *
     * @return array
     */
    public function getTaxonDetails(int $sisId): array
    {
        return Cache::remember("iucn.taxon.{$sisId}", 300, function () use ($sisId) {
            try {
                $response = $this->client()->get("/taxa/sis/{$sisId}");

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (RequestException | Throwable $e) {
                $this->logError("/taxa/sis/{$sisId}", [], $e);
                return [];
            }
        });
    }

    /**
     * Get assessment details by assessment ID.
     *
     * @return array
     */
    public function getAssessmentDetails(int $assessmentId): array
    {
        return Cache::remember("iucn.assessment.{$assessmentId}", 300, function () use ($assessmentId) {
            try {
                $response = $this->client()->get("/assessment/{$assessmentId}");

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (RequestException | Throwable $e) {
                $this->logError("/assessment/{$assessmentId}", [], $e);
                return [];
            }
        });
    }

    /**
     * Get conservation actions.
     *
     * @return array
     */
    public function getConservationActions(): array
    {
        return Cache::remember('iucn.conservation_actions', 3600, function () {
            try {
                $response = $this->client()->get('/conservation_actions/');

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (RequestException | Throwable $e) {
                $this->logError('/conservation_actions/', [], $e);
                return [];
            }
        });
    }

    /**
     * Get aggregated footer statistics.
     *
     * @return array{species_count: int, red_list_version: string, api_version: string}
     */
    public function getStatistics(): array
    {
        return Cache::remember('iucn.statistics', 86400, function () {
            try {
                $client = $this->client();

                $countResponse = $client->get('/statistics/count');
                $versionResponse = $client->get('/information/red_list_version');

                return [
                    'species_count' => $countResponse->successful() ? (int) $countResponse->json() : 0,
                    'red_list_version' => $versionResponse->successful() ? (string) $versionResponse->json() : 'N/A',
                    'api_version' => 'v4',
                ];
            } catch (RequestException | Throwable $e) {
                $this->logError('/statistics/aggregated', [], $e);
                return [
                    'species_count' => 0,
                    'red_list_version' => 'N/A',
                    'api_version' => 'v4',
                ];
            }
        });
    }

    /**
     * Translate IUCN category code to human-readable name.
     */
    public static function translateCategory(string $code): string
    {
        return self::CATEGORY_MAP[$code] ?? $code;
    }

    /**
     * Configure and return HTTP client.
     */
    private function client(): PendingRequest
    {
        return $this->http->withToken((string) config('iucn.token'))
            ->baseUrl((string) config('iucn.base_url'))
            ->timeout(15)
            ->retry(2, 500);
    }

    /**
     * Default fallback structure for assessments.
     *
     * @return array{assessments: array, pagination: array{total: int, per_page: int, current_page: int}}
     */
    private function assessmentFallback(): array
    {
        return [
            'assessments' => [],
            'pagination' => [
                'total' => 0,
                'per_page' => 100,
                'current_page' => 1,
            ],
        ];
    }

    /**
     * Log API errors with context.
     */
    private function logError(string $endpoint, array $params, Throwable $e): void
    {
        if ($e instanceof RequestException && $e->response->status() === 429) {
            Log::warning("IUCN API Rate Limited: {$endpoint}", [
                'params' => $params,
                'status' => 429,
            ]);
            return;
        }

        Log::error("IUCN API Error: {$endpoint}", [
            'params' => $params,
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
