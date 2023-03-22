<?php

namespace SVega9848\VegaLeaderboards\Tasks;

use pocketmine\scheduler\Task;
use SVega9848\VegaLeaderboards\Entities\FloatingEntity2;
use SVega9848\VegaLeaderboards\Loader;

class TopDeathsTask extends Task {

    private FloatingEntity2 $floatingEntity;

    public function __construct(FloatingEntity2 $floatingEntity) {
        $this->floatingEntity = $floatingEntity;
    }

    public function onRun(): void {
        $this->floatingEntity->setNameTag(Loader::getInstance()->displayDeathsLeaderboard());
    }
}