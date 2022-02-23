<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BugReport;

class BugReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BugReport::factory(20)->create();
    }
}
