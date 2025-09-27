<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TestController
{
    public function testDatabaseConenction()
    {
        try {
            DB::connection('pgsql')->getPdo();
            $version = DB::connection('pgsql')->select("select version()")[0]->version ?? 'bilinmeyen versiyon';

            return [
                'success' => true,
                'message' => 'PostgreSQL bağlantısı başarılı.',
                'version' => $version,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'PostgreSQL bağlantı HATASI: ' . $e->getMessage(),
            ];
        }
    }
}
