<?php

namespace skyss0fly\Quests;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\scheduler\Task;
use pocketmine\Player;
use DateTime;

class Main extends PluginBase implements Listener{
    private $playerData = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new TimeUpdateTask($this), 20 * 60); // Update time every minute
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        $this->playerData[$playerName] =
