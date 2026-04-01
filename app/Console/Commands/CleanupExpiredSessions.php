<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sessions:cleanup 
                           {--force : Force cleanup without confirmation}
                           {--days=7 : Number of days to keep expired sessions}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up expired sessions and related data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $force = $this->option('force');
        
        if (!$force && !$this->confirm("This will delete sessions older than {$days} days. Continue?")) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $this->info('Starting session cleanup...');
        
        // Calculate cutoff timestamp
        $cutoff = now()->subDays($days)->timestamp;
        
        // Clean up database sessions
        $deletedSessions = $this->cleanupDatabaseSessions($cutoff);
        
        // Clean up file sessions if using file driver
        $deletedFiles = $this->cleanupFileSessions($cutoff);
        
        // Clean up related activity logs (optional)
        $deletedLogs = $this->cleanupActivityLogs($days);
        
        $this->info("Cleanup completed:");
        $this->line("- Database sessions deleted: {$deletedSessions}");
        $this->line("- File sessions deleted: {$deletedFiles}");
        $this->line("- Activity logs deleted: {$deletedLogs}");
        
        return 0;
    }
    
    /**
     * Clean up database sessions
     */
    private function cleanupDatabaseSessions($cutoff)
    {
        try {
            $deleted = DB::table('sessions')
                ->where('last_activity', '<', $cutoff)
                ->delete();
                
            return $deleted;
        } catch (\Exception $e) {
            $this->error("Failed to cleanup database sessions: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Clean up file sessions
     */
    private function cleanupFileSessions($cutoff)
    {
        $deleted = 0;
        
        try {
            $sessionPath = config('session.files', storage_path('framework/sessions'));
            
            if (!is_dir($sessionPath)) {
                return 0;
            }
            
            $files = glob($sessionPath . '/sess_*');
            
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoff) {
                    if (unlink($file)) {
                        $deleted++;
                    }
                }
            }
            
            return $deleted;
        } catch (\Exception $e) {
            $this->error("Failed to cleanup file sessions: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Clean up related activity logs
     */
    private function cleanupActivityLogs($days)
    {
        try {
            // Only cleanup if ActivityLog model exists
            if (!class_exists('\App\Models\ActivityLog')) {
                return 0;
            }
            
            $deleted = DB::table('activity_logs')
                ->where('created_at', '<', now()->subDays($days * 2)) // Keep logs longer than sessions
                ->whereIn('action', ['session_extended', 'session_timeout_logout'])
                ->delete();
                
            return $deleted;
        } catch (\Exception $e) {
            $this->error("Failed to cleanup activity logs: " . $e->getMessage());
            return 0;
        }
    }
}