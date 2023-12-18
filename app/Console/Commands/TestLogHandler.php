<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Series;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\progress;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;

class TestLogHandler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-log-handler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the log handler from pre-established log files.';

    protected Team $teamA;

    protected Team $teamB;

    protected Event $event;

    protected Series $series;

    protected function selectTestMatch(): array
    {
        $testMatchDirs = File::directories(storage_path('test-data'));
        $testMatches = collect();

        foreach ($testMatchDirs as $testMatchDir) {
            if (File::exists($testMatchDir.'/meta.json')) {
                $testMatches->push([
                    ...json_decode(File::get($testMatchDir.'/meta.json'), true),
                    'dir' => $testMatchDir,
                    'log' => File::get($testMatchDir.'/log.txt'),
                ]);
            }
        }

        $selected = select('Select a test match', $testMatches->map(fn ($testMatch) => $testMatch['name'])->toArray());

        return $testMatches->firstWhere('name', $selected);
    }

    protected function createTeam($data): Team
    {
        $team = Team::create([
            'name' => $data['name'],
        ]);

        foreach ($data['players'] as $player) {
            $team->players()->create([
                'name' => $player['name'],
                'steam_id64' => $player['steam_id64'],
                'steam_id3' => $player['steam_id3'],
            ], [
                'start_date' => now()->subDay(),
            ]);
        }

        return $team;
    }

    protected function createSeries(): Series
    {
        $event = Event::create([
            'name' => 'Test Event',
            'start_date' => now()->subDay(),
        ]);

        return Series::create([
            'team_a_id' => $this->teamA->id,
            'team_b_id' => $this->teamB->id,
            'event_id' => $event->id,
            'status' => 'upcoming',
            'type' => 'bo1',
        ]);
    }

    protected function setupTestMatch(array $testMatch): void
    {
        $this->info('Test match: '.$testMatch['name']);
        $this->info('Log file: '.$testMatch['dir'].'/log.txt');

        $this->info('Creating Teams');
        $this->teamA = $this->createTeam($testMatch['teamA']);
        $this->teamB = $this->createTeam($testMatch['teamB']);
        $this->info('Teams Created');

        $this->info('Creating Series');
        $this->series = $this->createSeries();
        $this->info('Series Created');
    }

    protected function runLog(string $log): void
    {
        $this->info('Running log');

        $logLines = explode("\n", $log);

        // foreach ($logLines as $logLine) {
        //     if (empty($logLine)) {
        //         continue;
        //     }

        //     Http::post(env('APP_URL') . '/api/log-handler', [
        //         'log' => $logLine,
        //     ]);
        // }

        $instanceToken = str()->random(8);

        progress('Log Lines', $logLines, function ($logLine) use ($instanceToken) {
            if (empty($logLine)) {
                return;
            }

            Http::withBody($logLine)->withHeaders([
                'x-server-instance-token' => $instanceToken,
            ])->post(env('APP_URL').'/api/log-handler');
        });

        $this->info('Log complete');
    }

    protected function dumpOutput(): void
    {
        $this->series->refresh();
        $seriesMap = $this->series->seriesMaps()->first();

        table(
            ['Player', 'Kills', 'Deaths', 'ADR'],
            $seriesMap->players->map(function ($player) use ($seriesMap) {
                return [
                    $player->name,
                    $player->pivot->kills,
                    $player->pivot->deaths,
                    $player->pivot->damage / $seriesMap->rounds_played,
                ];
            })
        );

    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        config()->set('queue.default', 'sync');

        try {
            $testMatch = $this->selectTestMatch();
            $this->setupTestMatch($testMatch);
            $this->runLog($testMatch['log']);
            $this->dumpOutput();
            // $this->tearDown();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $this->tearDown();

            return;
        }
    }

    protected function tearDown(): void
    {
        $this->info('Deleting Teams');

        if ($this->teamA ?? false) {
            $this->teamA->delete();
        }

        if ($this->teamB ?? false) {
            $this->teamB->delete();
        }

        $this->info('Teams Deleted');

        if ($this->series ?? false) {
            $this->info('Deleting Series Logs');
            $this->series->logs()->delete();
            $this->info('Deleting Series');
            $this->series->delete();
            $this->info('Series Deleted');
        }
    }
}
