<?php
namespace AvionMoine10575\AFly;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;

class Main extends PluginBase implements Listener{
    /** @var Config */
    private $cfg;
    public function onEnable() : void{
        if(is_dir(($dir = $this->getDataFolder())) == false) mkdir($dir);
        $this->cfg = new Config($dir."config.yml", Config::YAML, [
	        "fly_command.on" => '&aYou just Enabled Your &6Fly!',
	        "fly_command.off" => '&6Fly &cdisabled!',
	        "fly_noPermission" => "&cYou don't have permission to use this command!",
        ]);
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "fly"){
            if(!$sender instanceof Player){
                $sender->sendMessage(TextFormat::RED . 'This game is only to be used in-game!');
                return false;
            }
            if(!$sender->hasPermission("fly.command")){
                $sender->sendMessage(TextFormat::colorize($this->cfg->get("fly_noPermission")));
                return false;
            }
            $value = !$sender->getAllowFlight();
            $sender->setAllowFlight($value);
            $sender->setFlying($value);
            $table = [true => "on", false => "off"];
            $sender->sendMessage(TextFormat::colorize($this->cfg->get('fly_command.' . $table[$value])));
            return true;
        }
        return true;
    /**
     * @param EntityDamageEvent $event
     */
    }
    public function onDamage(EntityDamageEvent $event) : void{
    	$entity = $event->getEntity();
        if($entity instanceof Player){
        	$rejectedCauses = [$event::CAUSE_STARVATION, $event::CAUSE_VOID, $event::CAUSE_FALL];
        	if(!in_array($event->getCause(), $rejectedCauses)){
		        $entity->setFlying(false);
		        $entity->setAllowFlight(false);
            }
        }
    }
}
