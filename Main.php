<?php

namespace Mentagess;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\inventory\FurnaceRecipe;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener
{

    public function onEnable() {
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."players/");
        $this->getLogger()->info("Plugin de job activé!");
        $this->getServer()->getCommandMap()->register("job", new Command($this, "job"));
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        if(!file_exists($this->getDataFolder()."players/".strtolower($event->getPlayer()->getName()).".yml")){

            $config = new Config($this->getDataFolder()."players/".strtolower($event->getPlayer()->getName()).".yml", Config::YAML);

            $config->set('miner-xp',0);
            $config->set('chasseur-xp',0);
            $config->set('farmer-xp',0);
            $config->set('bucheron-xp',0);
            $config->set('pilleur-xp',0);
            $config->set('assembleur-xp',0);

            $config->set('chasseur-lvl',1);
            $config->set('miner-lvl', 1);            
            $config->set('farmer-lvl',1);
            $config->set('bucheron-lvl',1);
			$config->set('pilleur-lvl',1);
			$config->set('assembleur-lvl',1);
            $config->save();

        }

    }

    public function onCraft(CraftItemEvent $event)
    {
    	$config = new Config($this->getDataFolder()."players/".strtolower($event->getPlayer()->getName()).".yml", Config::YAML);
    	$player = $event->getPlayer();

    	foreach($event->getOutputs() as $item){

//Partie d'interdiction des crafts (exemple)
            if($config->get('miner-lvl') < 2) {

                if($item->getId() === 344){

                    $event->setCancelled(true);
        			$player->sendMessage(TF::RED . "Tu ne peux pas fabriquer ceci car tu n'es pas niveau 2 en mineur, execute '/job' pour savoir ton niveau. ");

                }
			}
//gains d'xp craft
            if ($config->get('assembleur-lvl') < 20) {
                if ($config->get('assembleur-xp') < 500 * $config->get('assembleur-lvl')) {
					
					if($item->getId() === 300){
						$rand = mt_rand(10,20);
						$config->set('assembleur-xp', $config->get('assembleur-xp') + $rand);
						$config->save();
						$player->sendPopup("§2+ " . $rand . " xp");	
					}
                } else {

                    $config->set('assembleur-xp', 0);
                    $config->set('assembleur-lvl', $config->get('assembleur-lvl') + 1);
                    Server::GetInstance()->broadcastMessage($player->getName() . "§b est maintenant niveau §e" . $config->get('chasseur-lvl') . " §bau metier de assembleur.");
                    $config->save();
                }
			}				
    	}
    }
    public function getNetworkId(Entity $entity): int
    {
        return get_class($entity)::NETWORK_ID;
    }
    public function Chasseur(EntityDeathEvent $event){
        $entity = $event->getEntity();
        $last = $event->getEntity()->getLastDamageCause();
        if($last instanceof EntityDamageByEntityEvent) {
            $player = $event->getEntity()->getLastDamageCause()->getDamager();
            $config = new Config($this->getDataFolder()."players/".strtolower($player->getName()).".yml", Config::YAML);

            if ($event->getEntity() instanceof Entity) {

                if ($config->get('chasseur-lvl') < 20) {

                    if ($config->get('chasseur-xp') < 500 * $config->get('chasseur-lvl')) {

                        switch ($this->getNetworkId($entity)) {
                            case 13:
                            	$rand = mt_rand(10,20);
                                $config->set('chasseur-xp', $config->get('chasseur-xp') + $rand);
                                $config->save();
                                break;
                            case 11:
                           		$rand = mt_rand(20,30);
                                $config->set('chasseur-xp', $config->get('chasseur-xp') + $rand);
                                $config->save();
                                break;
                             case 10:
                           		$rand = mt_rand(10,20);
                                $config->set('chasseur-xp', $config->get('chasseur-xp') + $rand);
                                $config->save();
                             case 12:
                           		$rand = mt_rand(10,20);
                                $config->set('chasseur-xp', $config->get('chasseur-xp') + $rand);
                                $config->save();
                             case 32:
                           		$rand = mt_rand(15,25);
                                $config->set('chasseur-xp', $config->get('chasseur-xp') + $rand);
                                $config->save();
                             case 33:
                           		$rand = mt_rand(15,20);
                                $config->set('chasseur-xp', $config->get('chasseur-xp') + $rand);
                                $config->save();
                             case 34:
                           		$rand = mt_rand(10,15);
                                $config->set('chasseur-xp', $config->get('chasseur-xp') + $rand);
                                $config->save();
                             case 35:
                           		$rand = mt_rand(15,25);
                                $config->set('chasseur-xp', $config->get('chasseur-xp') + $rand);
                                $config->save();
                                break;

                        }

                    } else {

                        $config->set('chasseur-xp', 0);
                        $config->set('chasseur-lvl', $config->get('chasseur-lvl') + 1);
                        Server::GetInstance()->broadcastMessage($player->getName() . "§b est maintenant niveau §e" . $config->get('chasseur-lvl') . " §bau metier de chasseur.");
                        $config->save();

                    }
                }
            }
        
        if($config->get('chasseur-lvl') >= 20){

            if($config->get('chasseur-xp') < 10000){

                $config->set('chasseur-xp',$config->get('chasseur-xp')+0.0);
                $config->save();

            } else {

                $config->set('chasseur-lvl',0);
                $config->save();

            }

        }
    	}
    }

    public function onMiner(BlockBreakEvent $event)
    {
        $config = new Config($this->getDataFolder()."players/".strtolower($event->getPlayer()->getName()).".yml", Config::YAML);
        $player = $event->getPlayer();
        $name = $player->getName();
        $block = $event->getBlock();

        if($config->get('miner-lvl') < 20) {

            if ($config->get('miner-xp') < 500 * $config->get('miner-lvl')) {
                switch ($block->getID()) {

                    case 16:
						$config->set('miner-xp', $config->get('miner-xp') + 1);
                        $config->save();
                        $player->sendPopup("§2 + 1 xp");
                	    break;
                  
                    case 15:
						$rand = mt_rand(3,5);
                        $config->set('miner-xp', $config->get('miner-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
                  	    break;
                   
                    case 56:
                        $rand = mt_rand(10,20);
                        $config->set('miner-xp', $config->get('miner-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
                    	break;
						
                    case 129:
                     $rand = mt_rand(100,1000);
                        $config->set('miner-xp', $config->get('miner-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
                        break;
						
                    case 153:
                     $rand = mt_rand(10,20);
                        $config->set('miner-xp', $config->get('miner-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
                        break;


                }

            } else {

                $config->set('miner-xp', 0);
                $config->set('miner-lvl', $config->get('miner-lvl') + 1);
                Server::GetInstance()->broadcastMessage($player . "§b est maintenant niveau §e" . $config->get('miner-lvl') . " §bau metier de mineur.");
                $config->save();

            }

        }

        if($config->get('miner-lvl') > 20){

           	if($config->get('miner-xp') < 10000 ){

           	    $config->set('miner-xp',$config->get('miner-xp')+0.0);
           	    $config->save();

           	} else {

           	    $config->set('miner-lvl',0);
           	    $config->save();

           	}

        }
        if($config->get('farmer-lvl') < 20) {

            if ($config->get('farmer-xp') < 500 * $config->get('farmer-lvl')) {

                switch ($block->getID()) {

                    case 86:
                    	$rand = mt_rand(5,10);
                        $config->set('farmer-xp', $config->get('farmer-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
						break;
						 
                    case 103:
                    	$rand = mt_rand(5,10);
                        $config->set('farmer-xp', $config->get('farmer-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
                  	    break;                   

                    case 141:
                        $rand = mt_rand(6,12);
                        $config->set('farmer-xp', $config->get('farmer-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
                    	break;

                    case 142:
                        $rand = mt_rand(6,12);
                        $config->set('farmer-xp', $config->get('farmer-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
                    	break;

                    case 59:
                        $rand = mt_rand(6,12);
                        $config->set('farmer-xp', $config->get('farmer-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
                    	break;

                    case 244:
                     $rand = mt_rand(20,30);
                        $config->set('farmer-xp', $config->get('farmer-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
                        break;               

                }

            } else {

                $config->set('farmer-xp', 0);
                $config->set('farmer-lvl', $config->get('farmer-lvl') + 1);
                Server::GetInstance()->broadcastMessage($player . "§b est maintenant niveau §e" . $config->get('farmer-lvl') . " §bau metier de farmer.");
                $config->save();

            }

        }

        if($config->get('farmer-lvl') > 20){

           	if($config->get('farmer-xp') < 10000 ){

           	    $config->set('farmer-xp',$config->get('farmer-xp')+0.0);
           	    $config->save();

           	} else {

           	    $config->set('farmer-lvl',0);
           	    $config->save();

           	}

        }
        if($config->get('bucheron-lvl') < 20) {

            if ($config->get('bucheron-xp') < 500 * $config->get('bucheron-lvl')) {

                switch ($block->getID()) {

                    case 17:
                    	$rand = mt_rand(5,10);
                        $config->set('bucheron-xp', $config->get('bucheron-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
						break;            
                }

            } else {

                $config->set('bucheron-xp', 0);
                $config->set('bucheron-lvl', $config->get('bucheron-lvl') + 1);
                Server::GetInstance()->broadcastMessage($player . "§b est maintenant niveau §e" . $config->get('bucheron-lvl') . " §bau metier de bucheron.");
                $config->save();

            }

        }

        if($config->get('bucheron-lvl') > 20){

           	if($config->get('bucheron-xp') < 10000 ){

           	    $config->set('bucheron-xp',$config->get('bucheron-xp')+0.0);
           	    $config->save();

           	} else {

           	    $config->set('bucheron-lvl',0);
           	    $config->save();

           	}
        }
        if($config->get('pilleur-lvl') < 20) {

            if ($config->get('pilleur-xp') < 500 * $config->get('pilleur-lvl')) {

                switch ($block->getID()) {

                    case 49:
                    	$rand = mt_rand(5,10);
                        $config->set('pilleur-xp', $config->get('pilleur-xp') + $rand);
                        $config->save();
                        $player->sendPopup("§2 + " . $rand . " xp");
						break;            
                }

            } else {

                $config->set('pilleur-xp', 0);
                $config->set('pilleur-lvl', $config->get('pilleur-lvl') + 1);
                Server::GetInstance()->broadcastMessage($player . "§b est maintenant niveau §e" . $config->get('pilleur-lvl') . " §bau metier de pilleur.");
                $config->save();

            }

        }

        if($config->get('pilleur-lvl') > 20){

           	if($config->get('pilleur-xp') < 10000 ){

           	    $config->set('pilleur-xp',$config->get('pilleur-xp')+0.0);
           	    $config->save();

           	} else {

           	    $config->set('pilleur-lvl',0);
           	    $config->save();

           	}
        }
    }

    public function minerUI(Player $player)
    {
        $config = new Config($this->getDataFolder()."players/".strtolower($player->getName()).".yml", Config::YAML);
        $form = new SimpleForm(function (Player $player, $data){});
        $form->setTitle("§4Mineur");
        $form->setContent("Mineur information:");
        $form->addButton("Niveau(x):" . $config->get('miner-lvl'));
        $form->addButton("XP: " . $config->get('miner-xp') . "/" . 500 * $config->get('miner-lvl'));
		$form->addButton(500 * $config->get('miner-lvl') - $config->get('miner-xp') . "xp restant pour le prochain niveau");
        $form->addButton(TF::RED . ">> Retour");
		$form->sendToPlayer($player);
    }

    public function chasseurUI(Player $player)
    {
        $config = new Config($this->getDataFolder()."players/".strtolower($player->getName()).".yml", Config::YAML);
        $form = new SimpleForm(function (Player $player, $data){});
        $form->setTitle("§4Chasseur");
        $form->setContent("Chasseur information:");
        $form->addButton("Niveau(x):" . $config->get('chasseur-lvl'));
        $form->addButton("XP: " . $config->get('chasseur-xp') . "/" . 500 * $config->get('chasseur-lvl'));
		$form->addButton(500 * $config->get('chasseur-lvl') - $config->get('chasseur-xp') . "xp restant pour le prochain niveau");
        $form->addButton(TF::RED . ">> Retour");
		$form->sendToPlayer($player);
    }
    public function farmerUI(Player $player)
    {
        $config = new Config($this->getDataFolder()."players/".strtolower($player->getName()).".yml", Config::YAML);
        $form = new SimpleForm(function (Player $player, $data){});
        $form->setTitle("§4Farmer");
        $form->setContent("Farmer information:");
        $form->addButton("Niveau(x):" . $config->get('farmer-lvl'));
        $form->addButton("XP: " . $config->get('farmer-xp') . "/" . 500 * $config->get('farmer-lvl'));
		$form->addButton(500 * $config->get('farmer-lvl') - $config->get('farmer-xp') . "xp restant pour le prochain niveau");
        $form->addButton(TF::RED . ">> Retour");
		$form->sendToPlayer($player);
    }
    public function bucheronUI(Player $player)
    {
        $config = new Config($this->getDataFolder()."players/".strtolower($player->getName()).".yml", Config::YAML);
        $form = new SimpleForm(function (Player $player, $data){});
        $form->setTitle("§4Bucheron");
        $form->setContent("Bucheron information:");
        $form->addButton("Niveau(x):" . $config->get('farmer-lvl'));
        $form->addButton("XP: " . $config->get('farmer-xp') . "/" . 500 * $config->get('farmer-lvl'));
		$form->addButton(500 * $config->get('farmer-lvl') - $config->get('farmer-xp') . "xp restant pour le prochain niveau");
        $form->addButton(TF::RED . ">> Retour");
		$form->sendToPlayer($player);
    }
    public function pilleurUI(Player $player)
    {
        $config = new Config($this->getDataFolder()."players/".strtolower($player->getName()).".yml", Config::YAML);
        $form = new SimpleForm(function (Player $player, $data){});
        $form->setTitle("§4Pilleur");
        $form->setContent("Pilleur information:");
        $form->addButton("Niveau(x):" . $config->get('pilleur-lvl'));
        $form->addButton("XP: " . $config->get('pilleur-xp') . "/" . 500 * $config->get('pilleur-lvl'));
		$form->addButton(500 * $config->get('pilleur-lvl') - $config->get('pilleur-xp') . "xp restant pour le prochain niveau");
        $form->addButton(TF::RED . ">> Retour");
		$form->sendToPlayer($player);
    }
    public function assembleurUI(Player $player)
    {
        $config = new Config($this->getDataFolder()."players/".strtolower($player->getName()).".yml", Config::YAML);
        $form = new SimpleForm(function (Player $player, $data){});
        $form->setTitle("§4Assembleur");
        $form->setContent("Assembleur information:");
        $form->addButton("Niveau(x):" . $config->get('assembleur-lvl'));
        $form->addButton("XP: " . $config->get('assembleur-xp') . "/" . 500 * $config->get('assembleur-lvl'));
		$form->addButton(500 * $config->get('assembleur-lvl') - $config->get('assembleur-xp') . "xp restant pour le prochain niveau");
        $form->addButton(TF::RED . ">> Retour");
		$form->sendToPlayer($player);
    }
}
