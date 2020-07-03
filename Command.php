<?php

namespace Mentagess;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

class Command extends PluginCommand {
	
    private $plugin;
    
    public function __construct(Main $plugin, $name) {
        parent::__construct($name, $plugin);
        $this->plugin = $plugin;
    }
    
    public function execute(CommandSender $sender, $currentAlias, array $args) : bool
    {
        if ($sender instanceof Player) {
            $form = new SimpleForm(function (Player $sender, $data){
                $result = $data;
                if($result == null){
                } else {
                    switch($result){
                        case 1:
                            $this->plugin->minerUI($sender);
                            break;
                        case 2:
							$this->plugin->chasseurUI($sender);
                            break;
                        case 3:
							$this->plugin->farmerUI($sender);
                            break;                     
                        case 4:
							$this->plugin->bucheronUI($sender);
                            break;
                        case 5:
							$this->plugin->pilleurUI($sender);
                            break;
                        case 6:
							$this->plugin->assembleurUI($sender);
                            break;
                      }
                  }
            });

            $form->setTitle("§4JobUI");
            $form->setContent("Choisi ton job");
            $form->addButton("Bienvenue ". $sender->getName());
            $form->addButton("Mineur");
            $form->addButton("Chasseur");
            $form->addButton("Farmer");
            $form->addButton("Bucheron");
            $form->addButton("Pilleur");
			$form->addButton("Assembleur");
            $form->sendToPlayer($sender);

        }
		return true;
    }
}
