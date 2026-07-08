<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\EmailExists;
use Illuminate\Http\Request;

class FirebaseTestController extends Controller
{
    public function test()
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(storage_path('app/firebase-auth.json'))
                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

            $database = $factory->createDatabase();

            $ref = $database->getReference('test/laravel_connection');
            $ref->set([
                'status'    => 'ok',
                'time'      => now()->toDateTimeString(),
                'message'   => 'Connection verified from Laravel',
                'php'       => phpversion(),
                'laravel'   => app()->version(),
            ]);

            return response()->json([
                'success' => true,
                'data_written' => $ref->getValue(),
                'url_used' => env('FIREBASE_DATABASE_URL')
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}