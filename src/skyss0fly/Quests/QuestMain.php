<?php

namespace skyss0fly\Quests;
use DateTime;
use DateInterval;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use supercrafter333\PlayedTime;
use brokiem\SimpleNPC;

class QuestMain extends PluginBase implements Listener {

  public function onLoad(): void {
$this->saveDefaultConfig();
  }

  public function PlayerTime(DateTime $time){
  $player = $this->getServer()->getPlayer();
  $customConfig = $this->getFolder("Resources")->getFiles();
    if ($player != $customConfig) {
$this->getFolder("Resources")->addFile($player, $this->playerData());
      
    }
    
}

  public function onPlayerJoin(Listener $event, Player $player): void {
  $this->PlayerTime();  
}
  public function playerData(DateTime $time){
$timenow = $time->timeNow();
  }
}
