<?php

namespace supercrafter333\PTimeUI;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use supercrafter333\YourTimeUI\PTimeLoader;

class EventListener implements Listener
{

    public $plugin;

    public function __construct(PTimeLoader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $plugin = $this->plugin;
        $player = $event->getPlayer();
        $name = $player->getName();
        if (in_array($name, $plugin->ptime)) {
            $plugin->resetPTime($player);
        }
    }
}