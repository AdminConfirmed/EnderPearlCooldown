<?php

namespace Ender\EnderPearl;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\entity\ProjectileLaunchEvent;

class Main extends PluginBase implements Listener {

    private $pearlcd;

    public function onEnable() {

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->saveResource("config.yml");  

		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);

    }

    public function enderPearl(ProjectileLaunchEvent $event) {

        $pearl = $event->getEntity();

        if($pearl instanceof EnderPearl) {

            $player = $pearl->getOwningEntity();

            $cooldown = $this->config->get("cooldown");

            if(!isset($this->pearlcd[strtolower($player->getName())])) {

                $this->pearlcd[strtolower($player->getName())] = time();

            } else {

                if(time() - $this->pearlcd[strtolower($player->getName())] < $cooldown) {

                    $event->setCancelled(true);

                    $time = time() - $this->pearlcd[strtolower($player->getName())];

                    $player->sendMessage("You cannot use a pearl until " . ($cooldown - $time) . " seconds");

                } else {

                    $this->pearlcd[strtolower($player->getName())] = time();

                }

            }

        }

    }

}
