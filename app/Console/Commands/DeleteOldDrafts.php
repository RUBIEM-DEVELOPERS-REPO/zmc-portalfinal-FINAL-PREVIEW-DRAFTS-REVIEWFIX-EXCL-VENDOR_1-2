<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteOldDrafts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-drafts {--dry-run : Only show what would be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete application drafts older than 14 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoff = now()->subDays(14);
        $query = \App\Models\Application::where('status', \App\Models\Application::DRAFT)
            ->where('updated_at', '<', $cutoff);

        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("Dry run: {$count} drafts would be deleted (older than {$cutoff->toDateString()}).");
            return 0;
        }

        $query->delete();
        $this->info("Successfully deleted {$count} drafts older than 14 days.");

        return 0;
    }
}
