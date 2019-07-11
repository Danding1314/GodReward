<?php
 
namespace sexd;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\command\ConsoleCommandSender;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener {
    
    private $timer = [];

	public function onEnable () {
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
	}
	
	public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();
        $inv = $player->getInventory();
        if ($event->isCancelled()) return false;
        $random = rand(1, 500); //機率
        switch ($random) {
            case 1:
                $player->addTitle("§e你挖到\n§d與神有約 X1");
                $this->getServer()->broadcastMessage("§d恭喜§a {$name} §d挖到 §e與神有約 X1 §b< §e運氣很好喔 §b>");
                $item = Item::get(Item::TRIPWIRE_HOOK, 0, 1);
                $item->setCustomName("§b與神有約");
                $inv->addItem($item);
                break;
        }
	}
	
	 /**
     * @param Player $player
     */
    public function onRewards(Player $player)
    {
        ###########################
        $money = mt_rand(1, 1000);
        $exp = mt_rand(1,50);
        $mi = mt_rand(1,70);
        $point = mt_rand(1,10);
        ###########################
        $inv = $player->getInventory();
        $name = $player->getName();
        $names = strtolower($name);
        switch (mt_rand(1, 3)) {//選項1~3隨機一個
            case 1:
                $player->addTitle("§a你獲得了", "§d$" . $money, 5, 40, 5);
                EconomyAPI::getInstance()->addMoney($player, $money);
                break;
            case 2: //選項2中的1~4
                switch (mt_rand(1, 4)) {
                    case 1:
                        $player->addTitle("§a你獲得了", "§d鑽石", 5, 40, 5);
                        $inv->addItem(Item::get(264,0,3));
                        break;
                    case 2:
                        $player->addTitle("§a你獲得了", "§d鐵錠", 5, 40, 5);
                        $inv->addItem(Item::get(265,0,8));
                        break;
                    case 3:
                        $player->addTitle("§a你獲得了", "§d金錠", 5, 40, 5);
                        $inv->addItem(Item::get(266,0,4));
                        break;
                    case 4:
                        $player->addTitle("§a你獲得了", "§d煤炭", 5, 40, 5);
                        $inv->addItem(Item::get(263,0,8));
                        break;
                }
                break;
            case 3:
                switch (mt_rand(1, 3)) {			
                    case 1:
                        $player->addTitle("§a你獲得了\n§a{$exp}經驗值");
                        $this->getServer()->getPluginManager()->getPlugin("Grade")->addEXP($player, $exp)
                        break;
                    case 2:
                        $player->addTitle("§a你獲得了\n§a{$mi}積分");
                        $this->getServer()->getPluginManager()->getPlugin("Grade")->addMI($player, $mi)
                        break;	
                    case 3:
                        $player->addTitle("§a你獲得了\n§a{$point}S券");
                        $this->getServer()->getPluginManager()->getPlugin("KPoints")->addPoint($names, $point)
                        break;						
						}
                break;			
        }
	}
	
	 /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $inv = $player->getInventory();
        $name = $player->getName();
        if ($inv->getItemInHand()->getCustomName() == "§b與神有約") {
            if (!isset($this->timer[$name]) or time() > $this->timer[$name]) {
                $this->timer[$name] = time() + 4;
                $this->onRewards($player);
                $inv->removeItem(Item::get(Item::TRIPWIRE_HOOK, 0, 1));
            } else {
                $player->sendMessage("§b與神有約 > §d請等待 " . round($this->timer[$name] - time()) . "秒，後才可以使用§e與神有約§d!");
            }
        }
	}
	
	public function onDisable () {
	}
}