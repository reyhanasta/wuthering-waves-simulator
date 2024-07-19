<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CharacterService
{
    public function getData()
    {
        try {
            $response = Http::get('https://api.resonance.rest/characters');
            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('Failed to fetch data');
            }
        } catch (\Exception $e) {
            throw new \Exception('Something went wrong');
        }
    }

    public function getCharacterDetail($name)
    {
        try {
            $response = Http::get('https://api.resonance.rest/characters/' . $name);
            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('Failed to fetch character detail');
            }
        } catch (\Exception $e) {
            throw new \Exception('Something went wrong');
        }
    }
}
