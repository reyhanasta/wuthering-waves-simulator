<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeaponService
{
    public function getAllTypes()
    {
        try {
            $response = Http::get('https://api.resonance.rest/weapons');
            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('Failed to fetch weapon types');
            }
        } catch (\Exception $e) {
            throw new \Exception('Something went wrong');
        }
    }

    public function getWeaponsByType($type)
    {
        try {
            $response = Http::get("https://api.resonance.rest/weapons/{$type}");
            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('Failed to fetch weapons by type');
            }
        } catch (\Exception $e) {
            throw new \Exception('Something went wrong');
        }
    }

    public function getWeaponDetail($type, $name)
    {
        try {
            $response = Http::get("https://api.resonance.rest/weapons/{$type}/{$name}");
            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('Failed to fetch weapon detail');
            }
        } catch (\Exception $e) {
            throw new \Exception('Something went wrong');
        }
    }
}
