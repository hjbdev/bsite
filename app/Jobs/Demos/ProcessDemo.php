<?php

namespace App\Jobs\Demos;

use App\Enums\DemoStatus;
use App\Exceptions\Demos\FailedToCompressDemoException;
use App\Exceptions\Demos\FailedToParseDemoException;
use App\Models\Demo;
use App\Models\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ProcessDemo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Demo $demo
    ) {
        //
    }

    public function handle(): void
    {
        $localDisk = Storage::disk('local');
        $localFilePath = $this->demo->path;
        $originalFileSize = Storage::disk('local')->size($localFilePath);

        $data = $this->analyse();

        if ($this->demo->seriesMap) {
            $seriesMap = $this->demo->seriesMap;

            $seriesMap->players()->detach();

            foreach ($data['players'] as $steamId64 => $playerData) {
                $player = Player::where('steam_id64', $steamId64)->first();

                if ($player) {
                    $seriesMap->players()->attach($player, [
                        'kills' => $playerData['killCount'],
                        'deaths' => $playerData['deathCount'],
                        'assists' => $playerData['assistCount'],
                        'damage' => $playerData['healthDamage'],
                        'traded' => $playerData['tradeDeathCount'],
                        'kast' => $playerData['kast'],
                        'rating' => $playerData['hltvRating2'],
                        'crosshair_share_code' => $playerData['crosshairShareCode'],
                        'inspect_weapon_count' => $playerData['inspectWeaponCount'],
                        'kill_death_ratio' => $playerData['killDeathRatio'],
                        'bomb_defused_count' => $playerData['bombDefusedCount'],
                        'bomb_planted_count' => $playerData['bombPlantedCount'],
                        'armor_damage' => $playerData['armorDamage'],
                        'utility_damage' => $playerData['utilityDamage'],
                        'headshot_count' => $playerData['headshotCount'],
                        'headshot_percent' => $playerData['headshotPercent'],
                        'one_vs_one_count' => $playerData['oneVsOneCount'],
                        'one_vs_one_won_count' => $playerData['oneVsOneWonCount'],
                        'one_vs_one_lost_count' => $playerData['oneVsOneLostCount'],
                        'one_vs_two_count' => $playerData['oneVsTwoCount'],
                        'one_vs_two_won_count' => $playerData['oneVsTwoWonCount'],
                        'one_vs_two_lost_count' => $playerData['oneVsTwoLostCount'],
                        'one_vs_three_count' => $playerData['oneVsThreeCount'],
                        'one_vs_three_won_count' => $playerData['oneVsThreeWonCount'],
                        'one_vs_three_lost_count' => $playerData['oneVsThreeLostCount'],
                        'one_vs_four_count' => $playerData['oneVsFourCount'],
                        'one_vs_four_won_count' => $playerData['oneVsFourWonCount'],
                        'one_vs_four_lost_count' => $playerData['oneVsFourLostCount'],
                        'one_vs_five_count' => $playerData['oneVsFiveCount'],
                        'one_vs_five_won_count' => $playerData['oneVsFiveWonCount'],
                        'one_vs_five_lost_count' => $playerData['oneVsFiveLostCount'],
                        'average_kill_per_round' => $playerData['averageKillPerRound'],
                        'average_death_per_round' => $playerData['averageDeathPerRound'],
                        'average_damage_per_round' => $playerData['averageDamagePerRound'],
                        'utility_damage_per_round' => $playerData['utilityDamagePerRound'],
                        'first_kill_count' => $playerData['firstKillCount'],
                        'first_death_count' => $playerData['firstDeathCount'],
                        'first_trade_death_count' => $playerData['firstTradeDeathCount'],
                        'trade_death_count' => $playerData['tradeDeathCount'],
                        'trade_kill_count' => $playerData['tradeKillCount'],
                        'first_trade_kill_count' => $playerData['firstTradeKillCount'],
                        'one_kill_count' => $playerData['oneKillCount'],
                        'two_kill_count' => $playerData['twoKillCount'],
                        'three_kill_count' => $playerData['threeKillCount'],
                        'four_kill_count' => $playerData['fourKillCount'],
                        'five_kill_count' => $playerData['fiveKillCount'],
                        'hltv_rating' => $playerData['hltvRating'],
                        'hltv_rating2' => $playerData['hltvRating2'],
                    ]);
                }
            }
        }

        // Compress the file with gzip
        $result = Process::run(['gzip', storage_path('app/' . $localFilePath)]);

        if (!$result->successful()) {
            if ($localDisk->exists($localFilePath . '.gz')) {
                $localDisk->delete($localFilePath . '.gz');
            }

            if ($localDisk->exists($localFilePath)) {
                $localDisk->delete($localFilePath);
            }

            throw new FailedToCompressDemoException('Failed to compress the demo file');
        }

        // Set the file name and size
        $fileName = pathinfo($localFilePath, PATHINFO_BASENAME) . '.gz';
        $this->demo->size = $originalFileSize;
        $this->demo->compressed_size = filesize(storage_path('app/demo-tmp/' . $fileName));
        $this->demo->disk = config('media-library.disk_name');

        if (File::exists($localFilePath)) {
            File::delete($localFilePath);
        }

        $this->demo->path = 'demos/' . $this->demo->id . '/' . $fileName;

        // Move the file to the media disk
        Storage::disk(config('media-library.disk_name'))
            ->writeStream(
                $this->demo->path,
                Storage::disk('local')->readStream($localFilePath . '.gz'),
                ['visibility' => 'public']
            );

        Storage::disk(config('media-library.disk_name'))
            ->put($this->demo->path . '.json', json_encode($data, JSON_PRETTY_PRINT));

        // Delete the local file
        Storage::disk('local')->delete($localFilePath . '.gz');

        $this->demo->status = DemoStatus::PARSED;
        // dispatch job here
        $this->demo->save();
    }

    protected function analyse(): array
    {
        $demoName = pathinfo($this->demo->path, PATHINFO_BASENAME);

        $name = php_uname('m');
        // workaround bug
        if (strlen($name) > 20 and stripos($name, 'linux') !== false) {
            $name = `uname -m`;
        }
        $cpu = trim($name);

        $csda = $cpu === 'aarch64' ?
            base_path('lib/csda-arm64') :
            base_path('lib/csda-x64');

        $result = Process::run([
            $csda,
            '-demo-path='.storage_path('app/demo-tmp/' . $demoName),
            '-output='.storage_path('app/demo-tmp/'),
            '-format=json',
        ]);
        
        logger($result->command());

        if (! $result->successful()) {
            logger($result->output());
            Storage::disk('local')->delete('demo-tmp/' . str($demoName)->append('.json')->toString());
            throw new FailedToParseDemoException();
        }

        // Read the JSON file 
        $filename = pathinfo($this->demo->path, PATHINFO_FILENAME);

        $json = Storage::disk('local')->get('demo-tmp/' . str($filename)->append('.json')->toString());
        Storage::disk('local')->delete('demo-tmp/' . str($filename)->append('.json')->toString());
        $json = json_decode($json, true);

        return $json;
    }
}
