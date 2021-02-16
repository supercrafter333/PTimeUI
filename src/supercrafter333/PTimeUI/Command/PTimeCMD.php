<?php

namespace supercrafter333\PTimeUI\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use supercrafter333\YourTimeUI\PTimeLoader;

class PTimeCMD extends Command
{

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct("ptime", "Set your own time!", "/ptime <time>", ["playertime"]);
    }

    public function execute(CommandSender $s, string $commandLabel, array $args)
    {
        $plugin = PTimeLoader::getInstance();
        $config = new Config($plugin->getDataFolder()."config.yml", Config::YAML);
        if ($s instanceof Player) {
            if ($s->hasPermission("ptimeui.cmd")) {
                
            }
        } else {
            $s->sendMessage($config->get("command-only-ingame-message"));
        }
    }
}