<?php

namespace gamegam\signui\FakeBlock;

use gamegam\SignUI\event\SignInputEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;

class EventListener implements Listener
{

	public function onPacketReceive(DataPacketReceiveEvent $event): void {
		$packet = $event->getPacket();
		$p = $event->getOrigin()->getPlayer();

		if ($packet instanceof BlockActorDataPacket) {
			$nbt = $packet->nbt->getRoot();

			if ($nbt instanceof CompoundTag && $nbt->getString("id", "") === "Sign") {
				$frontText = $nbt->getCompoundTag("FrontText");
				if ($frontText !== null) {
					$text = $frontText->getString("Text", "");
					$sign_event = new SignInputEvent($p, $text, SignBlock::getInstance()->getSignType($p));
					$sign_event->call();
					SignBlock::getInstance()->removeFakeSign($p);
				}
			}
		}
	}
}