<?php

namespace skyss0fly\Quests\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use skyss0fly\Quests\QuestMain;

class AddQuestsCommand extends Command implements PluginOwned {

    public QuestMain $plugin;

    public function __construct(QuestMain $plugin) {
        parent::__construct("addquest", "Add a new quest", "/addquest <questName> <description> <rewards>");
        $this->setPermission("Quests.admin");
        $this->plugin = $plugin;
    }

    /**
     * @throws \JsonException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return true;
        }
        if (!$this->testPermission($sender)) {
            $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command.");
            return true;
        }
        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::RED . "Usage: /addquest <questName> <description> <rewards>");
            return true;
        }
        $questName = $args[0];
        $description = $args[1];
        $rewards = array_slice($args, 2);

        $questMain = $this->getOwningPlugin();
        $questMain->addQuest($sender, $questName, $description, $rewards);

        return true;
    }

    public function getOwningPlugin() : QuestMain {
        return $this->plugin;
    }
}
