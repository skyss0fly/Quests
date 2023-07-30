<?php

namespace skyss0fly\Quests;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use SimpleNPC\NPC;

class Main extends PluginBase {
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
		if(!is_dir($this->getDataFolder())){
			mkdir($this->getDataFolder());
		}
		
		if (!file_exists($this->getDataFolder() . "quests.yml")) {
			$this->quests = new Config($this->getDataFolder() . "quests.yml", Config::YAML);
		}
		
		$this->getServer()->getPluginManager()->registerEvents(new QuestsListener($this), $this);
		$this->getLogger()->info("QuestsAPI has been enabled!");
	}
	
	public function getQuests(){
		return $this->quests->getAll();
	}
	
	public function addQuest(Player $player, $questName, $questDescription, array $rewards){
		$playerName = $player->getName();
		
		$this->quests->set($questName, [
			"player" => $playerName,
			"description" => $questDescription,
			"rewards" => $rewards
		]);
		
		$this->quests->save();
		
		$player->sendMessage("You have added a new quest: '$questName'!");
		
		$npc = new NPC($player);
		$npc->setName("Quest NPC: $questName");
		$npc->showTo($player);
	}
	
	public function completeQuest(Player $player, $questName){
		$questData = $this->quests->get($questName);
		if(!$questData){
			$player->sendMessage("This quest does not exist!");
			return;
		}
		
		if($questData["player"] !== $player->getName()){
			$player->sendMessage("You can't complete this quest!");
			return;
		}
		
		foreach($questData["rewards"] as $reward){
			Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "give " . $player->getName() . " " . $reward);
		}
		
		$this->quests->remove($questName);
		$this->quests->save();
		
		$player->sendMessage("You have completed the quest '$questName'!");
	}
	
}
