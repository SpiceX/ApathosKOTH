<?php


namespace spice\koth;

use spice\koth\task\KOTHHeartbeatTask;
use spice\koth\task\KOTHStartGameTask;

class KOTHManager {

	/** @var ApathosKOTH */
	private ApathosKoth $plugin;

	/** @var KOTHArena[] */
	private array $arenas = [];

	/** @var null|KOTHArena */
	private ?KOTHArena $game = null;

	/**
	 * KOTHManager constructor.
	 *
	 * @param ApathosKoth $plugin
	 *
	 */
	public function __construct(ApathosKoth $plugin) {
		$this->plugin = $plugin;
		$this->init();
		$this->plugin->getScheduler()->scheduleRepeatingTask(new KOTHHeartbeatTask($this), 20);
		$this->plugin->getScheduler()->scheduleDelayedTask(new KOTHStartGameTask($this), 20);
	}


	public function init(): void {
		// register koths
	}

	/**
	 * @return KOTHArena[]
	 */
	public function getArenas(): array {
		return $this->arenas;
	}


	public function startEndOfTheWorldKOTH(): void {
		$eotwArena = null;
		foreach($this->arenas as $arena) {
			if($arena->getName() === "End") {
				$eotwArena = $arena;
			}
		}
		if($eotwArena === null) {
			return;
		}
		$this->game = $eotwArena;
		$this->plugin->getServer()->broadcastMessage("eotw koth begin");
	}


	public function startGame(): void {
		if(empty($this->arenas)) {
			return;
		}
		$arena = $this->arenas[array_rand($this->arenas)];
		$this->game = $arena;
		$this->plugin->getServer()->broadcastMessage("koth begin");
	}

	public function endGame(): void {
		$this->game = null;
		$this->plugin->getScheduler()->scheduleDelayedTask(new KOTHStartGameTask($this), 432000);
	}

	/**
	 * @return KOTHArena|null
	 */
	public function getGame(): ?KOTHArena {
		return $this->game;
	}
}