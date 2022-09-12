<?php

namespace App\Console\Commands;

use App\Models\AuditEvent;
use App\Models\FeatureFlag;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class ProcessFlags extends Command
{
    protected $signature = 'app:process';

    public function handle()
    {
        $events = AuditEvent::whereRaw('LOWER(custom_message) LIKE "%created feature flag%"')
                            ->orWhereRaw('LOWER(custom_message) LIKE "%deleted feature flag%"')
                            ->get();

        foreach ($events as $event) {
            if (str_contains(strtolower($event['custom_message']), 'created')) {
                Model::unguard();
                FeatureFlag::firstOrNew([
                    'id' => $event['target_id'],
                ])->forceFill([
                    'name' => $event['target_details'],
                    'author' => $event['author_name'],
                    'created_at' => $event['created_at'],
                ])->save();
            }

            if (str_contains(strtolower($event['custom_message']), 'deleted')) {
                FeatureFlag::findOrFail($event['target_id'])->forceFill([
                    'status' => 'DELETED',
                    'deleted_by' => $event['author_name'],
                    'deleted_at' => $event['created_at'],
                ])->save();
            }
        }

        $this->info('Done');
    }
}
