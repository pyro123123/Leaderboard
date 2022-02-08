<?php

declare(strict_types = 1);

namespace CodeGhost\How2Do;

use pocketmine\ {
  plugin\PluginBase,
  command\CommandSender,
  command\Command,
  event\Listener,
  event\player\PlayerChatEvent,
  item\Item,
  utils\Config,
  player\Player
};

use CodeGhost\How2Do\{
  Manager\lbManager,
  task\moneyTask,
  Manager\configManager,
  Manager\moneyLB
};

class Main extends PluginBase implements Listener {
  
  public static $cfg;
  public static $money;
  
  private static $path = "How2Do/leaderboard.json";

  public function onEnable():void {
    $this->getServer()->getLogger()->info("H2D plugin enabled");
   
    
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    
    self::$cfg = new configManager(new Config($this->getServer()->getPluginPath().self::$path));
    
    self::$money = new Config($this->getServer()->getPluginPath()."How2Do/money.json");
    
    // Update money leaderboard
    $this->getScheduler()->scheduleRepeatingTask(new moneyTask(),20);
  }
  
  // Return configManager class
  
  public static function getCfg() {
    return self::$cfg;
  }
  
  public static function getMoney() {
    return self::$money;
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool {
    
    if($sender instanceof Player) { 
      
      // Money Command
      
      if($label === "money") {
        
        if(!isset($args[0])) {
     
          $sender->sendMessage("§4/money <create,remove,get>");
         return true;
       }
       
        $CommandAction = $args[0];
        
        //remove the Command action type
        array_shift($args);
        
        switch ($commandAction) {
          
          case 'create':
           
           if((!isset($args[0])) || !is_numeric($args[0])|| !isset($args[1])) {
           $sender->sendMessage("§4/money create <amount> <playerName>");
            return true;
           }
           
           $amount = $args[0];
           
           array_shift($args);
           
           self::getMoney()->set(strtolower(implode(" ",$args)),$amount);
           self::getMoney()->save();
           
           $sender->sendMessage("§aCreated money account for ".implode(" ",$args));
           
            break;
          
          case "remove";
         
         if(!isset($args[0])) {
             $sender->sendMessage("§4/money remove <playerName>");
            return true;
          }
          
        self::getMoney()->remove(strtolower(implode(" ",$args)));
        self::getMoney()->save();
        
        $sender->sendMessage("§aRemoved money account for ".implode(" ",$args));
        
            break;
          
          case "get":
     
       if(!isset($args[0])) {
           $sender->sendMessage("§4/money get <playerName>");
            return true;
         }
         
        $sender->sendMessage($args[0]." have $".self::getMoney()->get(strtolower(implode(" ",$args))));
         
            break;
            
        }
        return true;
      }
      
      // Leaderboard Command 
      
      if($label === "lb") {
       if(!isset($args[0])) {
         $sender->sendMessage("§4/lb <create,edit,get,load,remove,list,money>");
         return true;
       }
       
       $choose = $args[0];
    
      // remove the Command action type

       array_shift($args);  
      
       $manager = new lbManager();
       
        switch ($choose) {
          case 'load':
          $manager->load();
          $sender->sendMessage("§aLoaded all leaderboard");
          return true;
            break;
          
          case "create": 
          
         if((!isset($args[0])) || !isset($args[1])) {
            $sender->sendMessage("§4/lb create <id> <text>");
            return true;
           }
           $id = $args[0];
          
           array_shift($args); // <text>
           
           $text = join(" ",$args);
           
          $manager = $manager->restart($id, $sender->getLocation()->asPosition(),$text);
           
          $manager->create();

          $sender->sendMessage("§aLeaderboard have been created");
          return true;
            break;
          
          case "get":
            
            if(!isset($args[0])) {
              $sender->sendMessage("§4/lb get <id>");
              return true;
            }
            
            $manager = $manager->restart($args[0]);
         $result = $manager->get(false);
         $sender->sendMessage($result);
         
            break;
          
          case "list": 
            $sender->sendMessage($manager->get(true));
            break;
            
            case "remove":
              
              if(!isset($args[0])) {
               $sender->sendMessage("§4/lb remove <id>");
              return true;
              }
              
              $manager = $manager->restart($args[0]);
              
              $manager->remove($sender);
              
              $sender->sendMessage("§aRemoved leaderboard");
              break;
            
            case "money":
              if(!isset($args[0])) {
               $sender->sendMessage("§4/lb money <id>");
              return true;
              }
        
         $eco = self::getMoney()->getAll();
   
         $moneyManager = new moneyLB($args[0],$sender->getLocation()->asPosition(),$eco);
              
              $moneyManager->createMoney();
              
              break;
              
              
        }
        
      }
      return true;
    }


        

      /*  
        if($args[0] == "money") {
          $text = "=============.\n";
          $allMoney = EconomyAPI::getInstance()->getAllMoney();
          $count = 1;
          foreach ($allMoney as $player => $money) {
            
            $text .= "$count) $player > $money.\n";
            $count++;
          }
          $text .= "=============";
          $manager = new lbManager($args[1],$sender->asPosition(),$text);
          $manager->createLB();
        }
        
        if ($args[0] == "remove") {
          array_shift($args);
          $manager = new lbManager($args[0]);
          $manager->removeLB($sender);
          return true;

        }

        if ($args[0] == "get") {
          array_shift($args); // or array_slice if u want
          $manager = new lbManager($args[0]);
          $lbInfo = $manager->getLB($sender);

          $sender->sendMessage($lbInfo);

          return true;
        }

        if (!isset($args[2])) {
          $sender->sendMessage("§4Please provide the leaderboard text");
          return true;
        }

        switch ($args[0]) {
          case 'create':
            $slice = array_slice($args,2);
            
            break;
            case 'edit':
              $text = array_slice($args,2);
              $manager = new lbManager($args[1],null,join(" ",$text));
              $manager->editLB($sender);
              break;

        }
      }
    }*/
    return true;
  }
  
}