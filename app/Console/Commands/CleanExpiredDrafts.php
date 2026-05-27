<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use Carbon\Carbon;

class CleanExpiredDrafts extends Command
{
    protected $signature = 'drafts:clean-expired {--days=14 : Number of days after which drafts expire}';
    protected $description = 'Delete draft applications older than the specified number of days (default: 14)';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = Carbon::now()->subDays($days);

        $count = Application::where('is_draft', true)
            ->where('created_at', '<', $cutoff)
            ->count();

        if ($count === 0) {
            $this->info('No expired drafts found.');
            return 0;
        }

        Application::where('is_draft', true)
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$count} expired draft(s) older than {$days} days.");

        if (class_exists(\App\Support\AuditTrail::class)) {
            \App\Support\AuditTrail::log('drafts_cleaned', null, [
                'count' => $count,
                'cutoff_days' => $days,
                'cutoff_date' => $cutoff->toDateTimeString(),
            ]);
        }

        return 0;
    }
}
