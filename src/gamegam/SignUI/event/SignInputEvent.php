<?php

namespace gamegam\SignUI\event;

use pocketmine\event\Event;
use pocketmine\player\Player;

class SignInputEvent extends Event {

	private Player $p;
	private string $text, $type;

	public function __construct(Player $p, string $text, $type) {
		$this->p = $p;
		$this->text = $text;
        $this->type = $type;
	}

	public function getPlayer(): Player {
		return $this->p;
	}

	public function getText(): string {
		return $this->text;
	}

	public function getSign(): array {
		$lines = explode("\n", $this->text);
		return array_pad(array_slice($lines, 0, 4), 4, "");
	}

    public function getType(): string {
        return $this->type;
    }

	public function getLine(): int {
		return substr_count($this->text, "\n") + 1;
	}
}
