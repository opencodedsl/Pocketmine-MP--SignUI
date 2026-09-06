<?php

namespace signtest;

use gamegam\signui\event\SignInputEvent;
use gamegam\SignUI\FakeBlock\SignBlock;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

/**
 * @name signtest
 * @main signtest\Main
 * @version 1.0.0
 * @api 5.0.0
 */

class Main extends PluginBase implements Listener{

	public function onEnable(): void
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		// 등록 이벤트
		SignBlock::getInstance()->register($this);
	}

	public function onJoin(PlayerJoinEvent $ev)
	{
		$p = $ev->getPlayer();
		SignBlock::getInstance()->SignCreate($p, "test");
	}

	public function SignEvent(SignInputEvent $ev){
		$p = $ev->getPlayer();
		$msg = $ev->getSign()[0];
		$p->sendMessage($msg);
	}
}