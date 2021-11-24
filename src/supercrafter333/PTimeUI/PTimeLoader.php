<?php

namespace supercrafter333\PTimeUI;

use supercrafter333\PTimeUI\Command\PTimeCMD;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use supercrafter333\PTimeUI\Tasks\PTimeTask;

class PTimeLoader extends PluginBase {

    public $prefix =  "§f[§bP§eTime§7UI§f] §8»§r ";
    public $config;
    public $ptime = [];
    public static $instance;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->saveResource("config.yml");
        self::$instance = $this;
        $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $this->config = $config;
        $this->getServer()->getCommandMap()->register("PTimeUI", new PTimeCMD("PTime"));
        if (!is_int($config->get("resume-ticks"))) {
            $config->set("resume-ticks", 5);
            $this->getLogger()->warning("[PTimeUI] --- DEBUG: Your resume-tick configuration was not an integer. It was automatically set to 5.");
        }
    }

    public function ptimeui(Player $s) {
        $form = new SimpleForm(function (Player $s, int $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->setPTimeUI($s);
                    break;
                case 1:
                    $this->reset($s);
                    break;
                case 2:
                    $this->close($s);
                    break;
            }
        });
        $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $form->setTitle("§l§bP§eTime§7UI");
        $form->setContent($config->get("UI-content"));
        $form->addButton($config->get("button-time-set-text"));
        $form->addButton($config->get("button-reset-text"));
        $form->addButton($config->get("button-leave-text"));
        $form->sendToPlayer($s);
        return true;
    }

    public function setPTimeUI(Player $s)
    {
        $form = new CustomForm(function (Player $s, array $data = null) {
            if($data === null) {
                return true;
            }
            $time = $data[1];
            $this->setPTimeFromUI($s, $time);
        });
        $config = $this->config;
        $form->setTitle("§l§bP§eTime§7UI");
        $form->addLabel($config->get("Set-Time-UI-content"));
        $form->addSlider("Time", 0, 24000);
        $form->sendToPlayer($s);
        return $form;
    }

    private function setPTimeFromUI(Player $s, $time)
    {
        if (!in_array($s->getName(), $this->ptime)) {
            $this->setPTime($s, $time);
            $s->sendMessage($this->prefix . $this->config->get("time-set-message"));
        } else {
            $s->sendMessage($this->prefix . $this->config->get("time-is-already-set"));
        }
    }

    private function reset(Player $s)
    {
        $this->resetPTime($s);
        $s->sendMessage($this->prefix . $this->config->get("reset-message"));
    }

    private function close(Player $s) {
        $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $s->sendMessage($this->prefix . $config->get("leave-message"));
    }

    /*API part*/
    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function setPTime(Player $player, int $time)
    {
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->ptime[] = $player->getName();
        $this->getScheduler()->scheduleRepeatingTask(new PTimeTask($this, $player, $time), $config->get("resume-ticks"));
    }

    public function resetPTime(Player $player)
    {
        if (!in_array($player->getName(), $this->ptime)) {
            return true;
        }
        unset($this->ptime[array_search($player->getName(), $this->ptime)]);
        $player->getLevel()->sendTime($player);
        $this->getScheduler()->cancelTask(PTimeTask::$id);
        $player->kick($this->prefix . $this->config->get("reset-kick-reason"), false);
        return true;
    }

    public function resetPTimeWithoutKick(Player $player)
    {
        if (!in_array($player->getName(), $this->ptime)) {
            return true;
        }
        unset($this->ptime[array_search($player->getName(), $this->ptime)]);
        $player->getLevel()->sendTime($player);
        $this->getScheduler()->cancelTask(PTimeTask::$id);
        return true;
    }

    public function isPTimeSet(Player $player): bool
    {
        if (in_array($player->getName(), $this->ptime)) {
            return true;
        } else {
            return false;
        }
    }
    /*API part - End*/
}
