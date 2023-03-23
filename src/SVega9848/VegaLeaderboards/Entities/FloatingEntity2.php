<?php

declare(strict_types=1);

namespace SVega9848\VegaLeaderboards\Entities;

use Closure;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\world\World;
use SVega9848\VegaLeaderboards\Loader;

class FloatingEntity2 extends Entity {

    public static function getNetworkTypeId() : string{
        return EntityIds::FALLING_BLOCK;
    }

    public $gravity = 0.0;
    public $canCollide = true;
    public $keepMovement = true;
    protected $gravityEnabled = false;
    protected $drag = 0.0;
    protected $immobile = true;

    private int $floating_text_id;

    /** @var array<int, Closure> */
    private array $despawn_callbacks = [];

    public function __construct(Location $location, World $world, string $nametag){
        $this->setCanSaveWithChunk(false);
        $id = Entity::nextRuntimeId();
        $this->floating_text_id = $id;
        Loader::getInstance()->fdeaths_id = $id;
        $this->setNameTag($nametag);
        //parent::__construct(new Location($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $world, 0.0, 0.0));
        parent::__construct($location);    }

    protected function initEntity(CompoundTag $nbt) : void{
        parent::initEntity($nbt);
        $this->setNameTagAlwaysVisible(true);
    }

    protected function getInitialSizeInfo() : EntitySizeInfo{
        return new EntitySizeInfo(0.01, 0.01);
    }

    protected function syncNetworkData(EntityMetadataCollection $properties) : void{
        parent::syncNetworkData($properties);
        $properties->setInt(EntityMetadataProperties::VARIANT, RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::AIR()->getFullId()));
    }

    public function getFloatingTextId() : int{
        return $this->floating_text_id;
    }

    public function isFireProof() : bool{
        return true;
    }

    public function attack(EntityDamageEvent $source) : void{
        $source->cancel();
    }
}