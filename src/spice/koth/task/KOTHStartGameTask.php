<?php

namespace spice\koth\task;

use pocketmine\scheduler\Task;
use spice\koth\KOTHManager;

class KOTHStartGameTask  extends Task
{
	private KOTHManager $manager;

	/**
	 * KOTHStartGameTask constructor.
	 * @param KOTHManager $manager
	 */
	public function __construct(KOTHManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * @param int $currentTick
	 *
	 */
	public function onRun(int $currentTick) {
		$this->manager->startGame();
	}
}