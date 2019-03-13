<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use function ceil;
use function microtime;
use function str_repeat;

class CooldownBarTask extends BlockSniperTask{

	/** @var Player */
	private $player;
	/** @var Brush */
	private $brush;
	/** @var int */
	private $useTime;

	public function __construct(Loader $loader, Brush $brush, Player $player){
		parent::__construct($loader);
		$this->player = $player;
		$this->brush = $brush;
		// Time of usage in seconds.
		$this->useTime = microtime(true);
	}

	public function onRun(int $currentTick) : void{
		if($this->player->isClosed()){
			$this->getHandler()->cancel();
			return;
		}
		do {
			if($this->loader->config->cooldownSeconds === 0.0){
				break;
			}
			$progress = (int) ceil((microtime(true) - $this->useTime) / $this->loader->config->cooldownSeconds * 20);
			if($progress > 20){
				$progress = 20;
			}
			$this->player->sendPopup(TextFormat::AQUA . str_repeat("|", $progress) . TextFormat::GRAY . str_repeat("|", 20 - $progress));

			if($progress === 20){
				break;
			}
			return;
		}while(false);

		$this->brush->unlock();
		$this->getHandler()->cancel();
	}
}