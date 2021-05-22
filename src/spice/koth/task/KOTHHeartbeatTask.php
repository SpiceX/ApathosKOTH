<?php

namespace spice\koth\task;

use pocketmine\scheduler\Task;
use spice\koth\KOTHManager;

class KOTHHeartbeatTask extends Task
{
	private KOTHManager $manager;

	/**
	 * KOTHHeartbeatTask constructor.
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
		if($this->manager->getGame() !== null) {
			$this->manager->getGame()->tick();
		}
	}
}