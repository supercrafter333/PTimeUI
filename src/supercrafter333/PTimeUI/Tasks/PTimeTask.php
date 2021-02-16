<?php

namespace supercrafter333\PTimeUI\Tasks;

use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\scheduler\Task;
use supercrafter333\PTimeUI\PTimeLoader;


class PTimeTask extends Task
{

    public $plugin;
    public $player;
    public $time;
    public static $id;

    public function __construct(PTimeLoader $plugin, Player $player, int $time)
    {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->time = $time;
        self::$id = $this->getTaskId();
    }

    public function onRun(int $currentTick)
    {
        $config = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
        $plugin = $this->plugin;
        $time = $this->time;
        $player = $this->player;
        $name = $player->getName();
        if (in_array($name, $plugin->ptime)) {
            $packet = new SetTimePacket();
            $packet->time = $time;
            $player->sendDataPacket($packet);
        } else {
            $plugin->resetPTime($player);
        }
    }
}