<?php

namespace SVega9848\VegaLeaderboards;

use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use SVega9848\VegaLeaderboards\Commands\LeaderboardsCommand;
use SVega9848\VegaLeaderboards\Entities\FloatingEntity;
use SVega9848\VegaLeaderboards\Entities\FloatingEntity2;
use SVega9848\VegaLeaderboards\Events\Events;
use SVega9848\VegaLeaderboards\Tasks\TopDeathsTask;
use SVega9848\VegaLeaderboards\Tasks\TopKillsTask;

class Loader extends PluginBase {

    public Config $kills_leaderboard;
    public Config $kills_leaderboard_config;
    public Config $deaths_leaderboard_config;
    public Config $deaths_leaderboard;
    public array $players = [];
    public int $fkills_id = 0;
    public int $fdeaths_id = 0;
    public FloatingEntity $floatingEntity;
    public FloatingEntity2 $floatingEntity2;
    public TaskHandler $topkillstask;
    public TaskHandler $topdeathstask;
    private array $setup = [];
    private static Loader $loader;

    public function onDisable(): void {
        if($this->kills_leaderboard_config->get("coords") && $this->kills_leaderboard_config->get("world")) {
            $this->floatingEntity->despawnFromAll();
        }
        if($this->deaths_leaderboard_config->get("coords") && $this->deaths_leaderboard_config->get("world")) {
            $this->floatingEntity2->despawnFromAll();
        }
    }

    public function onEnable() : void {

        $this->saveResource("kills_leaderboard_config.yml");
        $this->saveResource("deaths_leaderboard_config.yml");
        $this->kills_leaderboard = new Config($this->getDataFolder(). "/kills_leaderboard.yml", Config::YAML);
        $this->deaths_leaderboard = new Config($this->getDataFolder(). "/deaths_leaderboard.yml", Config::YAML);
        $this->kills_leaderboard_config = new Config($this->getDataFolder(). "/kills_leaderboard_config.yml", Config::YAML);
        $this->deaths_leaderboard_config = new Config($this->getDataFolder(). "/deaths_leaderboard_config.yml", Config::YAML);
		$this->getServer()->getPluginManager()->registerEvents(new Events($this), $this);
        $this->getServer()->getCommandMap()->register("leaderboards", new LeaderboardsCommand());
        self::$loader = $this;

        if($this->kills_leaderboard_config->get("coords") && $this->kills_leaderboard_config->get("world")) {
                $this->getServer()->getWorldManager()->loadWorld($this->kills_leaderboard_config->get("world"));

            $coords = $this->kills_leaderboard_config->get("coords");

            $this->floatingEntity = new FloatingEntity(new Location($coords[0], $coords[1]-1, $coords[2], $this->getServer()->getWorldManager()->getWorldByName($this->kills_leaderboard_config->get("world")), 0.0, 0.0), $this->getServer()->getWorldManager()->getWorldByName($this->kills_leaderboard_config->get("world")), $this->displayKillsLeaderboard());
            $this->floatingEntity->spawnToAll();

            $this->topkillstask = $this->getScheduler()->scheduleRepeatingTask(new TopKillsTask($this->floatingEntity), 20);

        }

        if($this->deaths_leaderboard_config->get("coords") && $this->deaths_leaderboard_config->get("world")) {
            $this->getServer()->getWorldManager()->loadWorld($this->deaths_leaderboard_config->get("world"));

            $coords = $this->deaths_leaderboard_config->get("coords");

            $this->floatingEntity2 = new FloatingEntity2(new Location($coords[0], $coords[1]-1, $coords[2], $this->getServer()->getWorldManager()->getWorldByName($this->deaths_leaderboard_config->get("world")), 0.0, 0.0), $this->getServer()->getWorldManager()->getWorldByName($this->deaths_leaderboard_config->get("world")), $this->displayDeathsLeaderboard());
            $this->floatingEntity2->spawnToAll();

            $this->topdeathstask = $this->getScheduler()->scheduleRepeatingTask(new TopDeathsTask($this->floatingEntity2), 20);

        }

	}

    public function replaceVars(string $str, array $vars): string {
        foreach ($vars as $key => $value) {
            $str = str_replace("{" . $key . "}", $value, $str);
        }
        return $str;
    }

    public function addKill(Player $player) : void {
        $leaderboard = $this->kills_leaderboard;
        $leaderboard->set($player->getName(), $leaderboard->get($player->getName()) + 1);
        $leaderboard->save();

        $array = [];

        foreach($leaderboard->getAll() as $key => $value) {
            $array[$key] = $value;
            $leaderboard->remove($key);
        }

        arsort($array, SORT_NUMERIC);
        foreach($array as $key => $value) {
            $leaderboard->set($key, $value);
            $leaderboard->save();
        }

    }

    public function addDeath(Player $player) : void {
        $leaderboard = $this->deaths_leaderboard;
        $leaderboard->set($player->getName(), $leaderboard->get($player->getName()) + 1);
        $leaderboard->save();

        $array = [];

        foreach($leaderboard->getAll() as $key => $value) {
            $array[$key] = $value;
            $leaderboard->remove($key);
        }

        arsort($array, SORT_NUMERIC);
        foreach($array as $key => $value) {
            $leaderboard->set($key, $value);
            $leaderboard->save();
        }

    }

    public function displayKillsLeaderboard() : string {
        $list = [];
        $count = 1;

        foreach($this->kills_leaderboard->getAll() as $key => $value) {
            switch($count) {
                case 1:
                    $list[] = TextFormat::colorize($this->replaceVars($this->kills_leaderboard_config->get("top1"), [
                        "PLAYER" => $key,
                        "KILLS" => $value
                    ]));
                    break;
                case 2:
                    $list[] = TextFormat::colorize($this->replaceVars($this->kills_leaderboard_config->get("top2"), [
                        "PLAYER" => $key,
                        "KILLS" => $value
                    ]));
                    break;
                case 3:
                    $list[] = TextFormat::colorize($this->replaceVars($this->kills_leaderboard_config->get("top3"), [
                        "PLAYER" => $key,
                        "KILLS" => $value
                    ]));
                    break;
                default:
                    $list[] = TextFormat::colorize($this->replaceVars($this->kills_leaderboard_config->get("topbelow3"), [
                        "PLAYER" => $key,
                        "KILLS" => $value,
                        "POSITION" => $count
                    ]));
                    break;
            }
            $count = $count+1;
        }

        if(count($list) > 10) {
            for($i = count($list); $i > $this->kills_leaderboard_config->get("leaderboard-limit"); $i--) {
                array_pop($list);
            }
        }

        array_unshift($list, $list[] = TextFormat::colorize($this->kills_leaderboard_config->get("firstline")));
        array_pop($list);

        if($this->kills_leaderboard_config->get("finalline")) {
            $list[] = TextFormat::colorize($this->kills_leaderboard_config->get("finalline"));
        }

        return implode(TextFormat::EOL, $list);
    }

    public function displayDeathsLeaderboard() : string {
        $list = [];
        $count = 1;

        foreach($this->deaths_leaderboard->getAll() as $key => $value) {
            switch($count) {
                case 1:
                    $list[] = TextFormat::colorize($this->replaceVars($this->deaths_leaderboard_config->get("top1"), [
                        "PLAYER" => $key,
                        "DEATHS" => $value
                    ]));
                    break;
                case 2:
                    $list[] = TextFormat::colorize($this->replaceVars($this->deaths_leaderboard_config->get("top2"), [
                        "PLAYER" => $key,
                        "DEATHS" => $value
                    ]));
                    break;
                case 3:
                    $list[] = TextFormat::colorize($this->replaceVars($this->deaths_leaderboard_config->get("top3"), [
                        "PLAYER" => $key,
                        "DEATHS" => $value
                    ]));
                    break;
                default:
                    $list[] = TextFormat::colorize($this->replaceVars($this->deaths_leaderboard_config->get("topbelow3"), [
                        "PLAYER" => $key,
                        "DEATHS" => $value,
                        "POSITION" => $count
                    ]));
                    break;
            }
            $count = $count+1;
        }

        if(count($list) > 10) {
            for($i = count($list); $i > $this->deaths_leaderboard_config->get("leaderboard-limit"); $i--) {
                array_pop($list);
            }
        }

        array_unshift($list, $list[] = TextFormat::colorize($this->deaths_leaderboard_config->get("firstline")));
        array_pop($list);

        if($this->deaths_leaderboard_config->get("finalline")) {
            $list[] = TextFormat::colorize($this->deaths_leaderboard_config->get("finalline"));
        }

        return implode(TextFormat::EOL, $list);
    }

    public function isSetup(string $name) : bool {
        return array_key_exists($name, $this->setup);
    }

    public function addSetupList(string $name, string $leaderboard) {
        $this->setup[$name] = $leaderboard;
    }

    public function deleteSetuplist(string $name) {
        unset($this->setup[$name]);
    }

    public function getSetupType(Player $player) : string {
        return $this->setup[$player->getName()];
    }

    public static function getInstance() : Loader {
        return self::$loader;
    }

}
