<?php

namespace FFA;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\{Config, TextFormat as TE};
use pocketmine\command\{Command, CommandSender};

class Loader extends PluginBase implements Listener {
	
	public function onEnable(){
		//TODO
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
	}
	
	public function onDisable(){
		//TODO
	}
	
	/**
	 * @param String $arenaName
	 * @return bool
	 */
	public function isArena(String $arenaName) : bool {
		if($this->config->exists($arenaName)){
			return true;
		}else{
			return false;
		}
		return false;
	}
	
	/**
	 * @param String $arenaName
	 */
	public function addArena(String $arenaName){
		$this->config->set("Arena", $arenaName);
		$this->config->save();
	}
	
	/**
	 * @param String $arenaName
	 */
	public function deleteArena(String $arenaName){
		$this->config->remove($arenaName);
		$this->config->save();
	}
	
	/**
	 * @param CommandSender $sender
	 * @param Command $command
	 * @param String $commandLabel
	 * @param Array $args
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, Command $command, String $commandLabel, Array $args) : bool {
		if(strtolower($command->getName()) === "ffa"){
			if(!$sender instanceof Player){
				$sender->sendMessage(TE::RED."Utiliza ese comando en juego!");
				return;
			}
			if(count($args) === 0){
				$sender->sendMessage(TE::RED."No hay suficientes argumentos!");
				return;
			}
			if($sender->hasPermission("use.command.ffa")){
				$sender->sendMessage(TE::RED."No tienes permisos para usar este comando!");
				return;
			}
			switch($args[0]){
				case "create":
				if(empty($args[1])){
					$sender->sendMessage(TE::GRAY."Debes utilizar el comando: ".TE::YELLOW."/ffa create <arenaName>");
					return;
				}
				if($this->isArena($args[1])){
					$sender->sendMessage(TE::RED."La arena {$args[1]} ya fue creada anteriormente!");
					return;
				}
				$this->addArena($args[1]);
				$sender->sendMessage(TE::GREEN."La arena {$args[1]} fue creada correctamente!");
				break;
				case "delete":
				if(empty($args[1])){
					$sender->sendMessage(TE::GRAY."Debes utilizar el comando: ".TE::YELLOW."/ffa delete <arenaName>");
					return;
				}
				if(!$this->isArena($args[1])){
					$sender->sendMessage(TE::RED."La arena {$args[1]} nunca fue creada!");
					return;
				}
				$this->deleteArena($args[1]);
				$sender->sendMessage(TE::GREEN."La arena {$args[1]} fue borrada correctamente!");
				break;
				case "help":
				case "?":
				$sender->sendMessage(TE::GRAY."=======================");
				$sender->sendMessage(TE::GREEN."/ffa create <arenaName> ".TE::GRAY."(Para crear una nueva arena)");
				$sender->sendMessage(TE::GREEN."/ffa delete <arenaName> ".TE::GRAY."(Para eliminar una arena)");
				$sender->sendMessage(TE::GREEN."/ffa setspawn ".TE::GRAY."(Para crear el punto de aparicion de los jugadores)");
				$sender->sendMessage(TE::GRAY."=======================");
				break;
			}
		}
		return false;
	}
}

?>