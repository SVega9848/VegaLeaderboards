<?php

namespace SVega9848\VegaLeaderboards\Commands;

use pocketmine\entity\Location;
use SVega9848\VegaLeaderboards\Entities\FloatingEntity;
use SVega9848\VegaLeaderboards\Entities\FloatingEntity2;
use SVega9848\VegaLeaderboards\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SVega9848\VegaLeaderboards\Tasks\TopDeathsTask;
use SVega9848\VegaLeaderboards\Tasks\TopKillsTask;

class LeaderboardsCommand extends Command {

    private Loader $loader;

    public function __construct() {
        parent::__construct("leaderboards", "Display leaderboards on your server!", "", ["ldb"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {

        $coords = [];

        if ($sender instanceof Player) {
            if($sender->hasPermission("leaderboards.cmd")) {
                if(isset($args[0])) {
                    switch($args[0]) {
                        case "help":
                            $sender->sendMessage(TextFormat::GREEN. "----- VegaLeaderboard Commands -----". TextFormat::EOL.
                            "- /leaderboards spawn [kills/deaths]". TextFormat::EOL.
                            "- /leaderboards remove [kills/deaths]". TextFormat::EOL.
                            "- /leaderboards edit [kills/deaths]");
                            break;
                        case "edit":
                            if(isset($args[1])) {
                                if(Loader::getInstance()->isSetup($sender->getName())) {
                                    $sender->sendMessage(TextFormat::RED. "You are already on a setup mode. Write 'leave' to continue");
                                } else {
                                    switch($args[1]) {
                                        case "kills":
                                            Loader::getInstance()->addSetupList($sender->getName(), "kills");
                                            $sender->sendMessage(TextFormat::GREEN. "You are now on kills setup!");
                                            break;
                                        case "deaths":
                                            Loader::getInstance()->addSetupList($sender->getName(), "deaths");
                                            $sender->sendMessage(TextFormat::GREEN. "You are now on deaths setup!");
                                            break;
                                    }
                                }
                            } else {
                                $sender->sendMessage(TextFormat::RED. "Few arguments! Use /leaderboards edit [kills/deaths]");
                            }
                        break;
                        case "remove":
                            if(isset($args[1])) {
                                switch($args[1]) {
                                    case "kills":
                                        if(!Loader::getInstance()->kills_leaderboard_config->get("world") && !Loader::getInstance()->kills_leaderboard_config->get("coords")) {
                                            $sender->sendMessage(TextFormat::RED. "There are no floating texts to remove");
                                        } else {

                                            if(!Loader::getInstance()->getServer()->getWorldManager()->getWorldByName(Loader::getInstance()->kills_leaderboard_config->get("world"))->isLoaded()) {
                                                Loader::getInstance()->getServer()->getWorldManager()->loadWorld(Loader::getInstance()->kills_leaderboard_config->get("world"));
                                            }
                                                foreach(Loader::getInstance()->getServer()->getWorldManager()->getWorldByName(Loader::getInstance()->kills_leaderboard_config->get("world"))->getEntities() as $entity) {
                                                    if($entity instanceof FloatingEntity) {
                                                        if($entity->getFloatingTextId() == Loader::getInstance()->fkills_id) {
                                                            $entity->despawnFromAll();
                                                            Loader::getInstance()->fkills_id = 0;
                                                            Loader::getInstance()->kills_leaderboard_config->remove("coords");
                                                            Loader::getInstance()->kills_leaderboard_config->remove("world");
                                                            Loader::getInstance()->kills_leaderboard_config->save();
                                                            Loader::getInstance()->topkillstask->remove();
                                                            $sender->sendMessage(TextFormat::GREEN. "Done!");
                                                        }
                                                    }
                                                }

                                        }
                                        break;
                                    case "deaths":
                                        if(!Loader::getInstance()->deaths_leaderboard_config->get("world") && !Loader::getInstance()->deaths_leaderboard_config->get("coords")) {
                                            $sender->sendMessage(TextFormat::RED. "There are no floating texts to remove");
                                        } else {

                                            if(!Loader::getInstance()->getServer()->getWorldManager()->getWorldByName(Loader::getInstance()->deaths_leaderboard_config->get("world"))->isLoaded()) {
                                                Loader::getInstance()->getServer()->getWorldManager()->loadWorld(Loader::getInstance()->deaths_leaderboard_config->get("world"));
                                            }
                                            foreach(Loader::getInstance()->getServer()->getWorldManager()->getWorldByName(Loader::getInstance()->deaths_leaderboard_config->get("world"))->getEntities() as $entity) {
                                                if($entity instanceof FloatingEntity2) {
                                                    if($entity->getFloatingTextId() == Loader::getInstance()->fdeaths_id) {
                                                        $entity->despawnFromAll();
                                                        Loader::getInstance()->fdeaths_id = 0;
                                                        Loader::getInstance()->deaths_leaderboard_config->remove("coords");
                                                        Loader::getInstance()->deaths_leaderboard_config->remove("world");
                                                        Loader::getInstance()->deaths_leaderboard_config->save();
                                                        Loader::getInstance()->topdeathstask->remove();
                                                        $sender->sendMessage(TextFormat::GREEN. "Done!");
                                                    }
                                                }
                                            }

                                        }
                                        break;
                                    default:
                                        $sender->sendMessage(TextFormat::RED. "Few arguments! Use /leaderboards spawn [kills/deaths]");
                                }
                            } else {
                                $sender->sendMessage(TextFormat::RED. "Few arguments! Use /leaderboards remove [kills/deaths]");
                            }
                        break;
                        case "spawn":
                            if(isset($args[1])) {
                                switch($args[1]) {
                                    case "kills":

                                        if(!Loader::getInstance()->kills_leaderboard_config->get("coords") && !Loader::getInstance()->kills_leaderboard_config->get("world")) {
                                            Loader::getInstance()->floatingEntity = new FloatingEntity(new Location($sender->getPosition()->getX(), $sender->getPosition()->getY()+2, $sender->getPosition()->getZ(), $sender->getWorld(), 0.0, 0.0), $sender->getWorld(), Loader::getInstance()->displayKillsLeaderboard());
                                            Loader::getInstance()->floatingEntity->spawnToAll();

                                            $coords[] = $sender->getPosition()->getX();
                                            $coords[] = $sender->getPosition()->getY()+2;
                                            $coords[] = $sender->getPosition()->getZ();

                                            Loader::getInstance()->kills_leaderboard_config->set("coords", $coords);
                                            Loader::getInstance()->kills_leaderboard_config->set("world", $sender->getWorld()->getFolderName());
                                            Loader::getInstance()->kills_leaderboard_config->save();
                                            Loader::getInstance()->topkillstask = Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new TopKillsTask(Loader::getInstance()->floatingEntity), 20);

                                            $sender->sendMessage(TextFormat::GREEN. "Done!");
                                        } else {
                                            $sender->sendMessage(TextFormat::RED. "It already exists a kill leaderboard! Use /leaderboards remove kills");
                                        }

                                        break;
                                    case "deaths":

                                        if(!Loader::getInstance()->deaths_leaderboard_config->get("coords") && !Loader::getInstance()->deaths_leaderboard_config->get("world")) {
                                            Loader::getInstance()->floatingEntity2 = new FloatingEntity2(new Location($sender->getPosition()->getX(), $sender->getPosition()->getY()+2, $sender->getPosition()->getZ(), $sender->getWorld(), 0.0, 0.0), $sender->getWorld(), Loader::getInstance()->displayDeathsLeaderboard());
                                            Loader::getInstance()->floatingEntity2->spawnToAll();

                                            $coords[] = $sender->getPosition()->getX();
                                            $coords[] = $sender->getPosition()->getY()+2;
                                            $coords[] = $sender->getPosition()->getZ();

                                            Loader::getInstance()->deaths_leaderboard_config->set("coords", $coords);
                                            Loader::getInstance()->deaths_leaderboard_config->set("world", $sender->getWorld()->getFolderName());
                                            Loader::getInstance()->deaths_leaderboard_config->save();
                                            Loader::getInstance()->topdeathstask = Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new TopDeathsTask(Loader::getInstance()->floatingEntity2), 20);

                                            $sender->sendMessage(TextFormat::GREEN. "Done!");
                                        } else {
                                            $sender->sendMessage(TextFormat::RED. "It already exists a death leaderboard! Use /leaderboards remove deaths");
                                        }

                                        break;
                                    default:
                                        $sender->sendMessage(TextFormat::RED. "Few arguments! Use /leaderboards spawn [kills/deaths]");
                                }
                            } else {
                                $sender->sendMessage(TextFormat::RED. "Few arguments! Use /leaderboards spawn [kills/deaths]");
                            }
                            break;
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED. "Few arguments! Use /leaderboards help");
                }
            }
        } else {
            $sender->sendMessage(TextFormat::RED. "Execute the command in-game");
        }
    }

}