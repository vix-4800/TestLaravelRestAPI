<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Collection;

class UserActivity extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:user-activity {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate user activity report for the last 30 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = (int) $this->argument('userId');
        $timestamp = now()->timestamp;

        /** @var User $user */
        $user = User::find($userId);

        if (! $user) {
            $this->error('User not found.');

            return;
        }

        $activities = $user->activities()
            ->forLastMonth()
            ->get();

        if ($activities->isEmpty()) {
            $this->warn('User has no activities in the last month. Nothing to report.');

            return;
        }

        $directoryPath = storage_path("activity-reports/{$userId}");

        if (! file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $filePath = "{$directoryPath}/report_{$timestamp}.csv";

        $this->writeActivitiesToCsv($filePath, $activities);

        $this->info('User activity report generated successfully.');
    }

    /**
     * Writes the given user activities to a CSV file.
     *
     * @param  string  $filePath  The path to the CSV file to be generated.
     * @param  array|Collection  $activities  A collection of user activities to be written to the CSV file.
     */
    private function writeActivitiesToCsv(string $filePath, array|Collection $activities): void
    {
        $handle = fopen($filePath, 'w');

        fputcsv($handle, ['Url', 'Method', 'Response Code', 'IP Address', 'Reported On']);

        if (is_array($activities)) {
            foreach ($activities as $activity) {
                fputcsv($handle, [
                    $activity['url'],
                    $activity['method'],
                    $activity['response_code'],
                    $activity['ip_address'],
                    $activity['created_at'],
                ]);
            }
        } else {
            foreach ($activities as $activity) {
                fputcsv($handle, [
                    $activity->url,
                    $activity->method,
                    $activity->response_code,
                    $activity->ip_address,
                    $activity->created_at->format('Y-m-d H:i:s'),
                ]);
            }
        }

        fclose($handle);
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'userId' => ['User ID for which you want to generate report', 'E.g. 1'],
        ];
    }
}
