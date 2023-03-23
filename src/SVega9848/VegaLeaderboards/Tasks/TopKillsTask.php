<?php

namespace SVega9848\VegaLeaderboards\Tasks;

use pocketmine\scheduler\Task;
use SVega9848\VegaLeaderboards\Entities\FloatingEntity;
use SVega9848\VegaLeaderboards\Loader;

class TopKillsTask extends Task {

    private FloatingEntity $floatingEntity;

    public function __construct(FloatingEntity $floatingEntity) {
        $this->floatingEntity = $floatingEntity;
    }

    public function onRun(): void {
        $this->floatingEntity->setNameTag(Loader::getInstance()->displayKillsLeaderboard());
    }
}