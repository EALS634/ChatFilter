<?php
namespace ChatFilter;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if (!file_exists($this->getDataFolder())){
            mkdir($this-dataFolder, 0744, true);
        }
        $this->words = new Config($this->getDataFolder() . "words.txt", Config::ENUM);
    }

    public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool
    {
        if ($command->getName() == "words") {
            $argscommand = strtolower(current($args));
            switch ($argscommand) {
                case "":
                    $sender->sendMessage("[使い方] \n/words add 語句: 禁止語句を追加します。\n/words delete 語句:禁止語句を削除します。");
                    return true;
                case "add":
                    if (!isset($args[1])) {
                        $sender->sendMessage("[使い方] /words add 追加したい語句");
                        return true;
                    } else {
                        $this->words->set($args[1]);
                        $this->words->save();
                        $sender->sendMessage("§a禁止語句として${args[1]}が追加されました。");
                        return true;
                    }
                    break;
                case "del":
                    if (!isset($args[1])) {
                        $sender->sendMessage("[使い方] /words del 追加したい語句");
                        return true;
                    } elseif (!$this->words->exists($args[1])) {
                        $sender->sendMessage("§4その語句は禁止語句として追加されていません。");
                        return true;
                    } else {
                        $this->words->remove($args[1]);
                        $this->words->save();
                        $sender->sendMessage("§a禁止語句${args[1]}が削除されました。");
                        return true;
                    }
                    break;
            }

        }
    }

    public function onChat(PlayerChatEvent $event)
    {
        $blockwords = $this->words->getAll();
        $chat = $event->getMessage();
        $newchat = str_ireplace(array_keys($blockwords),"*",$chat);
        var_dump(array_keys($blockwords));
	      $event->setMessage($newchat);
    }
}
