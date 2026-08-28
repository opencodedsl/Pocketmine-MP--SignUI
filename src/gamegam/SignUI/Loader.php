<?php

namespace gamegam\SignUI;

use gamegam\SignUI\FakeBlock\EventListener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase {

	use SingletonTrait;

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}

	public function onLoad() : void{
		self::setInstance($this);
	}
}