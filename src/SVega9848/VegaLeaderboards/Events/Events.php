<?php

namespace SVega9848\VegaLeaderboards\Events;

use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SVega9848\VegaLeaderboards\Loader;

class Events implements Listener {

    private Loader $loader;

    public function __construct(Loader $loader) {
        $this->loader = $loader;
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();

        if(!$this->loader->kills_leaderboard->get($player->getName())) {
        $this->loader->kills_leaderboard->set($player->getName(), 0);
        $this->loader->kills_leaderboard->save();
        }

        if(!$this->loader->deaths_leaderboard->get($player->getName())) {
            $this->loader->deaths_leaderboard->set($player->getName(), 0);
            $this->loader->deaths_leaderboard->save();
        }
    }

    public function onDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        $cause = $event->getPlayer()->getLastDamageCause();

        $this->loader->addDeath($player);

        if($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if($damager instanceof Player) {
                $this->loader->addKill($damager);
            }
        }
    }

    public function onChat(PlayerChatEvent $event) {
        $player = $event->getPlayer();
        $message = explode(" ", $event->getMessage());

        if(Loader::getInstance()->isSetup($player->getName())) {
            switch(Loader::getInstance()->getSetupType($player)) {
                case "deaths":
                    switch($message[0]) {
                        case "help":
                            $player->sendMessage(TextFormat::GREEN. "----- VegaLeaderboard Edit Commands -----". TextFormat::EOL.
                                "- /leave -> Leave edit mode". TextFormat::EOL.
                                "- /limit -> Modify top limit". TextFormat::EOL.
                                "- /top1 -> Modify top1 line". TextFormat::EOL.
                                "- /top2 -> Modify top2 line". TextFormat::EOL.
                                "- /top3 -> Modify top3 line". TextFormat::EOL.
                                "- /topbelow3 -> Modify tops below 3rd position line". TextFormat::EOL.
                                "- /firstline -> Modify first line". TextFormat::EOL.
                                "- /finalline -> Modify last line");
                            break;
                        case "leave":
                            Loader::getInstance()->deleteSetuplist($player->getName());
                            $player->sendMessage(TextFormat::GREEN. "You succesfully left setup mode!");
                            break;
                        case "limit":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->deaths_leaderboard_config->set("leaderboard-limit", $message[1]);
                                Loader::getInstance()->deaths_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use limit [top-limit]");
                            }
                            break;
                        case "top1":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->deaths_leaderboard_config->set("top1", implode(" ", $message));
                                Loader::getInstance()->deaths_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use top1 [text]");
                            }
                            break;
                        case "top2":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->deaths_leaderboard_config->set("top2", implode(" ", $message));
                                Loader::getInstance()->deaths_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use top2 [text]");
                            }
                            break;
                        case "top3":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->deaths_leaderboard_config->set("top3", implode(" ", $message));
                                Loader::getInstance()->deaths_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use top3 [text]");
                            }
                            break;
                        case "topbelow3":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->deaths_leaderboard_config->set("topbelow3", implode(" ", $message));
                                Loader::getInstance()->deaths_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use topbelow3 [text]");
                            }
                            break;
                        case "finalline":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->deaths_leaderboard_config->set("finalline", implode(" ", $message));
                                Loader::getInstance()->deaths_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use finalline [text]");
                            }
                            break;
                        case "firstline":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->deaths_leaderboard_config->set("firstline", implode(" ", $message));
                                Loader::getInstance()->deaths_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use firstline [text]");
                            }
                            break;
                        default:
                            $player->sendMessage(TextFormat::RED. "Invalid command! Type 'help' to see the command list");
                            break;
                    }
                    break;
                case "kills":
                    switch($message[0]) {
                        case "help":
                            $player->sendMessage(TextFormat::GREEN. "----- VegaLeaderboard Edit Commands -----". TextFormat::EOL.
                                "- /leave -> Leave edit mode". TextFormat::EOL.
                                "- /limit -> Modify top limit". TextFormat::EOL.
                                "- /top1 -> Modify top1 line". TextFormat::EOL.
                                "- /top2 -> Modify top2 line". TextFormat::EOL.
                                "- /top3 -> Modify top3 line". TextFormat::EOL.
                                "- /topbelow3 -> Modify tops below 3rd position line". TextFormat::EOL.
                                "- /firstline -> Modify first line". TextFormat::EOL.
                                "- /finalline -> Modify last line");
                            break;
                        case "leave":
                            Loader::getInstance()->deleteSetuplist($player->getName());
                            $player->sendMessage(TextFormat::GREEN. "You succesfully left setup mode!");
                            break;
                        case "limit":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->kills_leaderboard_config->set("leaderboard-limit", $message[1]);
                                Loader::getInstance()->kills_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use limit [top-limit]");
                            }
                            break;
                        case "top1":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->kills_leaderboard_config->set("top1", implode(" ", $message));
                                Loader::getInstance()->kills_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use top1 [text]");
                            }
                            break;
                        case "top2":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->kills_leaderboard_config->set("top2", implode(" ", $message));
                                Loader::getInstance()->kills_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use top2 [text]");
                            }
                            break;
                        case "top3":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->kills_leaderboard_config->set("top3", implode(" ", $message));
                                Loader::getInstance()->kills_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use top3 [text]");
                            }
                            break;
                        case "topbelow3":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->kills_leaderboard_config->set("topbelow3", implode(" ", $message));
                                Loader::getInstance()->kills_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use topbelow3 [text]");
                            }
                            break;
                        case "finalline":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->kills_leaderboard_config->set("finalline", implode(" ", $message));
                                Loader::getInstance()->kills_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use finalline [text]");
                            }
                            break;
                        case "firstline":
                            if(isset($message[1])) {
                                unset($message[0]);
                                Loader::getInstance()->kills_leaderboard_config->set("firstline", implode(" ", $message));
                                Loader::getInstance()->kills_leaderboard_config->save();
                                $player->sendMessage(TextFormat::GREEN. "Done!");
                            } else {
                                $player->sendMessage(TextFormat::RED. "Few arguments! Use firstline [text]");
                            }
                            break;
                        default:
                            $player->sendMessage(TextFormat::RED. "Invalid command! Type 'help' to see the command list");
                            break;
                    }
                    break;
            }
            $event->cancel();
        }
    }

}