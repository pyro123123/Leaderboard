<?php

namespace CodeGhost\How2Do\Manager;

use pocketmine\{
  utils\Config,
  world\Position,
  player\Player,
  Server
};

use CodeGhost\How2Do\Manager\customLB;
use CodeGhost\How2Do\Manager\moneyLB;
use CodeGhost\How2Do\Manager\configManager;
use CodeGhost\How2Do\Main;

/**
 * 
 * Class for normal leaderboard manager
 * Method:
 * - update
 * - create
 * - remove
 * - load
 * - get
 * 
 */
 
class lbManager {
  
  public function __construct($id = "",?Position $position = null,string $text = "") {
  
    $this->id = $id;
    $this->position = $position;
    $this->text = $text;
    
  }
  
  public function create() {
    $ids = $this->id;
    
    if(is_string($this->id)) {
    $ids = self::toId($this->id);
    }
    
    $customLB = new customLB($ids,$this->position,$this->text);
    
    $customLB->create();
      
    Main::getCfg()->createData($ids,$this->id,$this->position,$this->text);
    
    
  }
 
  public function remove(Player $sender) {
  
    $ids = self::toId($this->id);
    
    
   $exist = Main::getCfg()->real($ids);  
   
    if(!$exist) {
      $sender->sendMessage("§4leaderboard with id ($this->id) doesnt exist");
      return;
      
    }
    
    $clb = new customLB($ids,$sender->getLocation()->asPosition());
   
    $clb->remove();
    
    Main::getCfg()->remove($ids);
    
  }
  
  /**
   * 
   *  Load Normal Leaderboard
   * 
   * */
   
  public function load() {
    
    foreach (Main::getCfg()->allData() as $k => $v) {
     if(!$k) continue;
      $level = Server::getInstance()->getWorldManager()->getWorldByName($v["position"]["level"]);
      
      $pos = new Position($v["position"]["x"],$v["position"]["y"],$v["position"]["z"],$level);
      
    if(isset($v["type"])) continue;
     
    $clb = new customLB($k,$pos,$v["text"]);

    $clb->create();
      
      
    }
  }
  
    public function get($all) {
      
     if($all) {
       
       $result = ["§6All Leaderboard info"];
        
        foreach (Main::getCfg()->allData() as $k => $v) {
         
        if(isset($v["type"]) && $v["type"] === "money") continue;
          
          array_push($result,"§bID: ".$v["name"]."\nText: ".$v["text"]);
        }
       
        if(count($result) == 1) {
          array_push($result,"§4- No leaderboard");
        }
        
        return implode("\n",$result);
      } 
      
    $ids = 0;
    
    if(is_string($this->id)) {
    $ids = self::toId($this->id);
    }
    
     $get = Main::getCfg()->getData($ids);
     $exist = Main::getCfg()->real($ids);
     
      if(!$exist) {
        return "§4Leaderboard with id ($this->id) doesnt exist\n$ids";
      }
      
     
      $text = "§6Leaderboard info\n§bUnique ID: $ids\nID: ".$get["name"]."\nText: ".$get["text"];
      
      return $text;
     
    }
  
  // Change string to unique ID
  
  public static function toId($id) {
 
    $word = ["a" => 1,
      "b" => 2,
      "c" => 3,
      "d" => 4,
      "e" => 5,
      "f" => 6,
      "g" => 7,
      "h" => 8,
      "i" => 9,
      "j" => 1,
      "k" => 2,
      "l" => 3,
      "m" => 4,
      "n" => 5,
      "o" => 6,
      "p" => 7,
      "q" => 8,
      "r" => 9,
      "s" => 1,
      "t" => 2,
      "u" => 3,
      "v" => 4,
      "w" => 5,
      "x" => 6,
      "y" => 7,
      "z" => 8,
      "_" => 9];
      
    
    
      $newStr = str_split(strtolower($id));
      $newId = "";
      foreach ($newStr as $str) {
        if(!is_numeric($str)) {
        $newId .= $word[$str];
        } else {
          $newId .= $str;
        }
      }
      
      return intval($newId);
      
      
  }
  
  public function restart($id = "",?Position $position = null,string $text = "") {
    $this->id = $id;
    $this->position = $position;
    $this->text = $text;
    return $this;
  }
  
 
  
}