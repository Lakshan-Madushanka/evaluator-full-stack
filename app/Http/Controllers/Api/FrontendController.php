<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use Illuminate\Http\Request;
use Illuminate\Support\Stringable;

class FrontendController extends Controller
{
    public function index(): array
    {
        $dbData = Frontend::query()->first()?->toArray() ?? [];

      return [...$this->getDataFromConfig(), ...$dbData];
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validate([
            'base_url' => ['required', 'url'],
            'preset' => ['required', 'string'],
            'color_scheme' => ['required', 'string'],
        ]);

        $baseUrl = $validatedData['base_url'];

        unset($validatedData['base_url']);

        $frontend =  Frontend::query()
            ->updateOrCreate(
                ['id' => 1],
                [...$validatedData, ...$this->getUrlData($baseUrl)]
            );

        return response()->json($frontend->toArray(), 201);
    }

    public function getDataFromConfig(): array
    {
        $baseUrl =  $this->sanitizeUrl(config('app.url'));

        return [
            ...$this->getUrlData($baseUrl),
            'preset' => 'aura',
            'color_scheme' => 'purple',
        ];
    }

    protected function sanitizeUrl(string $url): string
    {
        return  str($url)
            ->whenEndsWith('/', fn(Stringable $str) => $str->replaceLast('/', ''))
            ->toString();
    }

    protected function getApiUrl(string $baseUrl): string
    {
        return  $baseUrl . '/api';
    }

    protected function getApiV1Url(string $baseUrl): string
    {
        return  $baseUrl . '/api/v1';
    }

    protected function getUrlData(string $baseUrl): array
    {
        return [
            'base_url' => $baseUrl,
            'api_url' => $this->getApiUrl($baseUrl),
            'api_v1_url' => $this->getApiV1Url($baseUrl),
        ];
    }
}
