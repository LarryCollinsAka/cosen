<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('prompts')->insert([
            [
                'incident_category' => 'Infrastructure',
                'question' => 'Could you provide more details about the specific infrastructure issue you encountered?',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incident_category' => 'Safety',
                'question' => 'Please describe the safety hazard in more detail. Is anyone currently at risk?',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incident_category' => 'Waste',
                'question' => 'Can you specify the type and location of the waste you observed?',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incident_category' => 'Other',
                'question' => 'Thank you for your report. What other information should we know?',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
