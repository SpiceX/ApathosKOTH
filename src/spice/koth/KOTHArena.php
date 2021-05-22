<?php

namespace spice\koth;

use pocketmine\level\Position;
use pocketmine\Player;

class KOTHArena
{

	/** @var string */
	private string $name;

	/** @var Position */
	private Position $firstPosition;

	/** @var Position */
	private Position $secondPosition;

	/** @var null|Player */
	private ?Player $capturer;

	/** @var int */
	private int $captureProgress = 0;

	/** @var int */
	private int $objectiveTime;

	/**
	 * KOTHArena constructor.
	 *
	 * @param string $name
	 * @param Position $firstPosition
	 * @param Position $secondPosition
	 * @param int $objectiveTime
	 *
	 * @throws KOTHException
	 */
	public function __construct(string $name, Position $firstPosition, Position $secondPosition, int $objectiveTime)
	{
		$this->name = $name;
		$this->firstPosition = $firstPosition;
		$this->secondPosition = $secondPosition;
		if ($firstPosition->getLevel() === null || $secondPosition->getLevel() === null) {
			throw new KOTHException("KOTH arena \"$name\" position levels are invalid.");
		}
		if ($firstPosition->getLevel()->getName() !== $secondPosition->getLevel()->getName()) {
			throw new KOTHException("KOTH arena \"$name\" position levels are not the same.");
		}
		$this->objectiveTime = $objectiveTime;
	}


	public function tick(): void
	{
		if ($this->captureProgress >= $this->objectiveTime) {
			if (!$this->capturer->isOnline()) {
				$this->captureProgress = 0;
				$this->capturer = null;
				return;
			}
			$key = null; // Obtain key from piggy crates
			if ($this->capturer->getInventory()->canAddItem($key)) {
				$this->capturer->getInventory()->addItem($key);
			} else {
				$this->capturer->getLevel()->dropItem($this->capturer->asVector3(), $key);
			}

			ApathosKOTH::getInstance()->getKOTHManager()->endGame();
			ApathosKOTH::getInstance()->getServer()->broadcastMessage("koth end capturer name");
		}
		if ($this->capturer === null || (!$this->isPositionInside($this->capturer)) || (!$this->capturer->isOnline())) {
			$this->captureProgress = 0;
			$this->capturer = null;
			foreach ($this->firstPosition->getLevel()->getPlayers() as $player) {
				if (!$player instanceof Player) {
					continue;
				}
				if ($this->isPositionInside($player)) {
					if ($this->capturer !== null) {
						return;
					}
					$this->capturer = $player;
				}
			}
			if ($this->capturer !== null) {
				ApathosKOTH::getInstance()->getServer()->broadcastMessage("koth current capturer name");
			}
		}
		$this->captureProgress++;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param Player|null $player
	 */
	public function setCapturer(?Player $player = null): void
	{
		$this->capturer = $player;
	}

	/**
	 * @return Player|null
	 */
	public function getCapturer(): ?Player
	{
		return $this->capturer;
	}

	/**
	 * @param int $amount
	 */
	public function setCaptureProgress(int $amount): void
	{
		$this->captureProgress = $amount;
	}

	/**
	 * @return int
	 */
	public function getCaptureProgress(): int
	{
		return $this->captureProgress;
	}

	/**
	 * @return int
	 */
	public function getObjectiveTime(): int
	{
		return $this->objectiveTime;
	}

	/**
	 * @return Position
	 */
	public function getFirstPosition(): Position
	{
		return $this->firstPosition;
	}

	/**
	 * @return Position
	 */
	public function getSecondPosition(): Position
	{
		return $this->secondPosition;
	}

	/**
	 * @param Position $position
	 *
	 * @return bool
	 */
	public function isPositionInside(Position $position): bool
	{
		$level = $position->getLevel();
		$firstPosition = $this->firstPosition;
		$secondPosition = $this->secondPosition;
		$minX = min($firstPosition->getX(), $secondPosition->getX());
		$maxX = max($firstPosition->getX(), $secondPosition->getX());
		$minZ = min($firstPosition->getZ(), $secondPosition->getZ());
		$maxZ = max($firstPosition->getZ(), $secondPosition->getZ());
		return $minX <= $position->getX() and $maxX >= $position->getFloorX() and
			$minZ <= $position->getZ() and $maxZ >= $position->getFloorZ() and
			$this->firstPosition->getLevel()->getName() === $level->getName();
	}

}