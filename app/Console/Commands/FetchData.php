<?php

namespace App\Console\Commands;

use App\Models\AuditEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchData extends Command
{
    protected $signature = 'app:fetch';

    public function handle()
    {
        $page = 1;
        $inserted = 0;

        while ($page) {
            $http = Http::withHeaders([
                'PRIVATE-TOKEN' => config('services.gitlab.token'),
            ])->get('https://gitlab.com/api/v4/projects/'.config('services.gitlab.project_id').'/audit_events',
                [
                    'per_page' => 100,
                    'page' => $page,
                ]);

            $events = $http->json();

            $this->info('Fetched page: '.$page.' with rows: '.count($events));

            $page += 1;

            foreach ($events as $event) {
                if (AuditEvent::where(['id' => $event['id']])->exists()) {
                    $page = false;
                    break;
                }

                AuditEvent::forceCreate([
                    'id' => $event['id'],
                    'author_id' => $event['author_id'],
                    'entity_id' => $event['entity_id'],
                    'entity_type' => $event['entity_type'],
                    'custom_message' => $event['details']['custom_message'] ?? '',
                    'target_id' => $event['details']['target_id'],
                    'target_type' => $event['details']['target_type'],
                    'target_details' => $event['details']['target_details'],
                    'ip_address' => $event['details']['ip_address'],
                    'entity_path' => $event['details']['entity_path'],
                    'author_name' => $event['details']['author_name'],
                    'details' => $event['details'],
                    'created_at' => $event['created_at'],
                ]);

                $inserted += 1;
            }

            if (count($events) === 0) {
                $page = false;
            }
        }

        $this->info('Finished');
        $this->info('Inserted events: '.$inserted);
    }
}
