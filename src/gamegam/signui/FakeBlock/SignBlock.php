<?php

namespace gamegam\signui\FakeBlock;

use pocketmine\block\VanillaBlocks;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\OpenSignPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;

class SignBlock
{

	use SingletonTrait;
	private array $pp = [];

    private array $types = [];

	// 플러그인
	private ?PluginBase $pluginBase = null;

	public function register(PluginBase $plugin): void{
		if ($this->pluginBase == null){
			$this->pluginBase = $plugin;
			$this->pluginBase->getServer()->getPluginManager()->registerEvents(new EventListener(), $this->pluginBase);
		}
	}
	/**
	public function getRegister(?PluginBase $pluginBase):bool|PluginBase{
		return ($this->pluginBase === null) ? false : $pluginBase;
	} **/

	protected function onLoad(): void {
		self::$instance = $this;
	}

    public function getSignType(Player $player): string {
        return $this->types[$player->getName()] ?? "";
    }

	public function SignCreate(Player $p, $type = "")
	{
		if ($this->pluginBase === null){
			$p->sendMessage("§c표지판 UI를 열 수 없습니다. 관리자에게 문의하세요.");
			return;
		}
		$pos = $p->getPosition();
		$x = $pos->getX();
		$y = $pos->getY();
		$z = $pos->getZ();
		if ($y > $p->getWorld()->getMaxY()){
			$y = 0;
		}else if ($y > $p->getWorld()->getMinY()){
			$y = $p->getWorld()->getMaxY();
		}
		$this->pp[$p->getName()] = [
			"x" => $x,
			"y" => $y,
			"z" => $z
		];
		$blockPos = new BlockPosition($x, $y, $z);
		$block = VanillaBlocks::OAK_SIGN();

		$blockTranslator = TypeConverter::getInstance()->getBlockTranslator();
		$networkId = $blockTranslator->internalIdToNetworkId($block->getStateId());

		$updateBlockPk = UpdateBlockPacket::create(
			$blockPos,
			$networkId,
			UpdateBlockPacket::FLAG_PRIORITY,
			UpdateBlockPacket::DATA_LAYER_NORMAL
		);
		$p->getNetworkSession()->sendDataPacket($updateBlockPk);
		$nbt = CompoundTag::create()
			->setString("id", "Sign")
			->setInt("x", $x)
			->setInt("y", $y)
			->setInt("z", $z)
			->setTag("FrontText", CompoundTag::create()
				->setString("Text", "")
				->setString("OwnerMSUID", "")
			);

		$blockActorPk = BlockActorDataPacket::create(
			$blockPos,
			new CacheableNbt($nbt)
		);
		$p->getNetworkSession()->sendDataPacket($blockActorPk);

		$this->pluginBase->getScheduler()->scheduleDelayedTask(
			new ClosureTask(function() use ($p, $x, $y, $z, $type){
				if($p->isOnline()){
					$pk = OpenSignPacket::create(new BlockPosition($x, $y, $z), true);
					$p->getNetworkSession()->sendDataPacket($pk);
                    $this->types[$p->getName()] = $type;
				}
			}),
			10
		);
	}

	public function removeFakeSign(Player $player): void {

		$x = $this->pp[$player->getName()]["x"] ?? 0;
		$y = $this->pp[$player->getName()]["y"] ?? 0;
		$z = $this->pp[$player->getName()]["z"] ?? 0;

		$realBlock = $player->getWorld()->getBlockAt($x, $y, $z);
		$blockTranslator = TypeConverter::getInstance()->getBlockTranslator();
		$networkId = $blockTranslator->internalIdToNetworkId($realBlock->getStateId());

		$pk = UpdateBlockPacket::create(
			new BlockPosition($x, $y, $z),
			$networkId,
			UpdateBlockPacket::FLAG_PRIORITY,
			UpdateBlockPacket::DATA_LAYER_NORMAL
		);
        unset($this->pp[$player->getName()], $this->types[$player->getName()]);

		$player->getNetworkSession()->sendDataPacket($pk);
	}
}
