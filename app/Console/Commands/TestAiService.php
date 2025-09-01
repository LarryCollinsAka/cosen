<?php

namespace App\Console\Commands;

use App\Services\AiService;
use Illuminate\Console\Command;

class TestAiService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the AI service by sending a sample report.';

    /**
     * Execute the console command.
     */
    public function handle(AiService $aiService)
    {
        $this->info('Starting AI service test...');

        $reportDescription = "There's a massive pothole in the middle of the road on Rue 1056. It is causing a lot of traffic.";
        
        $this->info("Analyzing report: \"" . $reportDescription . "\"");

        $category = $aiService->categorizeReport($reportDescription);

        $this->info("AI classified the report as: " . $category);
        
        if ($category === 'road') {
            $this->info('Test passed! The AI correctly categorized the report.');
        } else {
            $this->error('Test failed. The AI returned an unexpected category.');
        }
    }
}
