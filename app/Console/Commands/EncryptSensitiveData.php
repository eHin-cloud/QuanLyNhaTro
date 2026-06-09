<?php

namespace App\Console\Commands;

use App\Models\LandlordProfile;
use App\Models\LandlordVerificationRequest;
use App\Models\Resident;
use App\Models\ResidentRelative;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class EncryptSensitiveData extends Command
{
    protected $signature = 'security:encrypt-sensitive-data {--dry-run : Count affected records without writing changes}';

    protected $description = 'Backfill AES-256-GCM encryption and blind indexes for existing sensitive data.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $total = 0;
        $total += $this->process(User::class, ['phone'], $dryRun);
        $total += $this->process(Tenant::class, ['phone', 'bank_account_no'], $dryRun);
        $total += $this->process(Resident::class, ['phone', 'cccd'], $dryRun);
        $total += $this->process(ResidentRelative::class, ['phone', 'cccd'], $dryRun);
        $total += $this->process(LandlordProfile::class, ['phone'], $dryRun);
        $total += $this->process(LandlordVerificationRequest::class, ['cccd_number'], $dryRun);

        $this->info(($dryRun ? 'Would process ' : 'Processed ') . $total . ' records.');

        return self::SUCCESS;
    }

    /**
     * @param class-string<\Illuminate\Database\Eloquent\Model> $modelClass
     * @param array<int, string> $fields
     */
    private function process(string $modelClass, array $fields, bool $dryRun): int
    {
        $count = 0;

        $modelClass::query()
            ->orderBy('id')
            ->chunkById(100, function ($records) use ($fields, $dryRun, &$count): void {
                foreach ($records as $record) {
                    $hasSensitiveData = false;

                    foreach ($fields as $field) {
                        if ($record->{$field} !== null && $record->{$field} !== '') {
                            $hasSensitiveData = true;
                            $record->{$field} = $record->{$field};
                        }
                    }

                    if (!$hasSensitiveData) {
                        continue;
                    }

                    $count++;

                    if (!$dryRun) {
                        $record->save();
                    }
                }
            });

        $this->line(class_basename($modelClass) . ': ' . $count);

        return $count;
    }
}
