<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use Illuminate\Console\Command;

class CheckSubscriptions extends Command
{
    protected $signature = 'clinics:check-subscriptions';
    protected $description = 'Bloquer automatiquement les cliniques dont l\'abonnement a expiré';

    public function handle(): int
    {
        $expiredClinics = Clinic::where('is_blocked', false)
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<', now())
            ->get();

        foreach ($expiredClinics as $clinic) {
            $clinic->update([
                'is_blocked' => true,
                'blocked_reason' => 'Abonnement expiré le ' . $clinic->subscription_expires_at->format('d/m/Y'),
                'blocked_at' => now(),
            ]);

            $this->info("Clinique bloquée : {$clinic->name}");
        }

        $count = $expiredClinics->count();
        $this->info("{$count} clinique(s) bloquée(s) pour abonnement expiré.");

        return Command::SUCCESS;
    }
}
