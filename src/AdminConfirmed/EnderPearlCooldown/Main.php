<?php

namespace AdminConfirmed\EnderPearlCooldown;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\EnderPearl;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;

class Main extends PluginBase implements Listener {

    private $pearlcd;
    private $config;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if($command->getName() === "pearlcooldown") {
            if(!$sender->hasPermission("pearlcooldown.change")) {
                $sender->sendMessage(TextFormat::RED . "You do not have permissions to use this command");
                return false;
            }
            if(isset($args[0])) {
                $newcooldown = $args[0];
                if(!is_numeric($newcooldown)){
                    $sender->sendMessage("Cooldown must be numeric");
                    return false;
                }
                $oldcooldown = $this->config->get("cooldown");
                $this->config->set("cooldown", $newcooldown);
                $this->config->save();
                $sender->sendMessage("Cooldown set to $newcooldown from $oldcooldown");
                return false;
            }
            $sender->sendMessage("Usage: /pearlcooldown <seconds>");
        }
        return true;
    }

    public function onEnderPearl(PlayerInteractEvent $event)
    {
        $item = $event->getItem();
        if($item instanceof EnderPearl) {
            $cooldown = $this->config->get("cooldown");
            $player = $event->getPlayer();
            if (isset($this->pearlcd[$player->getName()]) and time() - $this->pearlcd[$player->getName()] < $cooldown) {
                $event->setCancelled(true);
                $time = time() - $this->pearlcd[$player->getName()];
                $message = $this->config->get("message");
                $message = str_replace("{cooldown}", ($cooldown - $time), $message);
                $player->sendMessage($message);
            } else {
                $this->pearlcd[$player->getName()] = time();
            }
        }
    }

}
