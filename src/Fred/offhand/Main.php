<?php

declare(strict_types=1);

namespace Fred\offhand;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\block\VanillaBlocks;

class Main extends PluginBase implements Listener {

    /** @var array<string, bool> */
    private $offHandStatus = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender instanceof Player) {
            $playerName = $sender->getName();
            switch ($command->getName()) {
                case "offhand":
                    if (isset($args[0])) {
                        if ($args[0] === "on") {
                            $this->offHandStatus[$playerName] = true;
                            $sender->sendMessage(TF::GREEN . "Offhand feature enabled.");
                            return true;
                        } elseif ($args[0] === "off") {
                            $this->offHandStatus[$playerName] = false;
                            $sender->sendMessage(TF::RED . "Offhand feature disabled.");
                            return true;
                        }
                    }
                    return false;
            }
        } else {
            $sender->sendMessage(TF::RED . "This command can only be used in-game.");
            return true;
        }
        return false;
    }

    public function onPlayerToggleSneak(PlayerToggleSneakEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();

        if (isset($this->offHandStatus[$playerName]) && $this->offHandStatus[$playerName] && $event->isSneaking()) {
            $item = $player->getInventory()->getItemInHand();
            $offHandInventory = $player->getOffHandInventory();
            $offHandItem = $offHandInventory->getItem(0);

            if (!$item->isNull() && $item instanceof Item) {
                if ($offHandItem->isNull()) {
                    $offHandInventory->setItem(0, $item);

                    $player->getInventory()->setItemInHand(VanillaBlocks::AIR()->asItem());

                    $player->sendMessage(TF::GREEN . "Item moved to offhand.");
                }
            }
        }
    }
}
