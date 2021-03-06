<?php

namespace FFA;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\math\Vector3;
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
	 * @param Vector3 $position
	 */
	public function addSpawn(Vector3 $position){
		$this->config->set("Spawn", [$position->getLevel(), $position->getX(), $position->getY(), $position->getZ()]);
		$this->config->save();
	}
	
	/**
	 * @return Vector3
	 */
	public function getPositionSpawn() : Vector3 {
		$spawn = $this->config->get("Spawn");
		return new Vector3($spawn[1], $spawn[2], $spawn[3], $spawn[0]);
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
	 * @param Player $player
	 */
	public function joinToArena(Player $player){
		$player->teleport($this->getPositionSpawn());
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
				case "setspawn":
				if(!$this->isArena($args[1])){
					$sender->sendMessage(TE::RED."La arena {$args[1]} nunca fue creada!");
					return;
				}
				$this->addSpawn($sender->getPosition());
				$sender->sendMessage(TE::GREEN."Spawn fue registrado en las coordenadas: ".TE::AQUA.$sender->getX().TE::GRAY.", ".TE::AQUA.$sender->getY().TE::GRAY.", ".TE::AQUA.$sender->getZ());
				break;
				case "join":
				$this->joinToArena($sender);
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