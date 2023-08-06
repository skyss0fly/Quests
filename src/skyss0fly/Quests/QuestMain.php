<?php

namespace skyss0fly\Quests;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\Server;
use skyss0fly\Quests\Commands\AddQuestsCommand;
use BeeAZ\HumanNPC\HumanNPC;

class QuestMain extends PluginBase implements Listener {
	
	public function onEnable(): void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->registerCommands();
		
		if(!is_dir($this->getDataFolder())){
			mkdir($this->getDataFolder());
		}
		
        	$this->quests = new Config($this->getDataFolder() . "quests.yml", Config::YAML);

        	$this->getServer()->getPluginManager()->registerEvents(new QuestsListener(), $this);
        	$this->getLogger()->info("QuestsAPI has been enabled!");
	}

    	public function registerCommands() {
            $this->getServer()->getCommandMap()->registerAll("quests", [
            	new AddQuestsCommand($this)
            ]);
        }
	
	public function getQuests(){
		return $this->quests->getAll();
	}
	
    	/**
     	* @throws \JsonException
     	*/
	public function addQuest(Player $player, $questName, $questDescription, array $rewards){
		$playerName = $player->getName();
		
        	if ($this->quests->exists($questName)) {
            	    $player->sendMessage("A quest with the named '" . $questName . "' already exists.");
            	    return false;
        	}

        	if (empty($questName) || empty($questDescription || empty($rewards))) {
                    $player->sendMessage("Quest data is incomplete. Provide a valid quest name, description and rewards.");
                    return false;
                }

		$this->quests->set($questName, [
			"player" => $playerName,
			"description" => $questDescription,
			"rewards" => $rewards
		]);
		
		$this->quests->save();
		
		$player->sendMessage("You have added a new quest: '$questName'!");
		
		$npc = new HumanNPC($player->getLocation(), $player->getSkin());
		$npc->setNameTag("Quest NPC: $questName");
		$npc->setInvisible(false);
        	$npc->spawnTo($player);
        	return true;
	}
	
    /**
     * @throws \JsonException
     */
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
        foreach ($questData["rewards"] as $reward) {
            $command = "give " . $player->getName() . " " . $reward;
            $commandSender = new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage());
            $result = Server::getInstance()->dispatchCommand($commandSender, $command);
            if ($result === false) {
                $this->getLogger()->error("Error executing reward command: $command");
                $player->sendMessage("An error occurred while giving rewards. Please contact an administrator.");
                return;
            }
        }
		
	$this->quests->remove($questName);
	$this->quests->save();
		
	$player->sendMessage("You have completed the quest '$questName'!");
   }

    /**
     * @throws \JsonException
     */
    public function editQuest(Player $player, $questName, $newDescription, array $newRewards){
        $questData = $this->quests->get($questName);
        if (!$questData) {
            $player->sendMessage("The data for this quest '" . $questName . "' does not exist.");
            return false;
        }

        if ($questData["player"] !== $player->getName()) {
            $player->sendMessage("You can only edit the quests created by you.");
            return false;
        }

        $questData["description"] = $newDescription;
        $questData["rewards"] = $newRewards;

        $this->quests->set($questName, $questData);
        $this->quests->save();
        $player->sendMessage("The quest '" . $questName . "' has been successfully edited.");
        return true;
    }
}
