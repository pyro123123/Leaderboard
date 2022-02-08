<?php

namespace CodeGhost\How2Do\Manager;

use pocketmine\{
  world\Position,
  utils\Config
};

 use onebone\economyapi\EconomyAPI;

class configManager {
  
  public static $config;
  
  public function __construct(Config $cfg) {
    self::$config = $cfg;
  }
  
  // Create leaderboard data
  
  public function createData(int $id,String $name,?Position $pos,String $text,$money = null) {
    
    $saveData = !is_null($money) ? $this->baseData($name,$pos,$text,$money) : $this->baseData($name,$pos,$text);
    
    self::$config->set($id,$saveData);
    self::$config->save();
  }
  
  
  // Remove leaderboard data
  
  public function remove($id) {
   
    if(!$this->real($id)) return false;
    
    self::$config->remove($id);
    self::$config->save();
    return true;
    
  }
  
  
  // Get leaderboard data
  
  public function getData($id) {
    if(!$this->real($id)) return;
    $result = self::$config->get($id);
    
    return $result;
  }
  
  
  // Get all leaderboard data
  
  public function allData() {
   $result = self::$config->getAll();
    return $result;
  }
  
  
  // Check if leaderboard ID is exist
  
  public function real($id) {
   
    if(self::$config->exists($id)) return true;
    
    return false;
  }
  
  

  
  // Create layout for leaderboard data (money / normal)
  
  private function baseData($name,$pos,$text,$money = null) {
    
    $position = null;
    
    if($pos) {
    $position = self::toPos($pos);
    }
    
    if(!is_null($money)) {
      
     $assignData = [
       "name" => $name,
       "type" => "money",
       "text" => "money leaderboard",
       "position" => $position,
       "money" => $money
       ];
     
      return $assignData;
    }
    
    $assignData = [
      "name" => $name,
      "position" => $position,
      "text" => $text,
      ];
      
      return $assignData;
  }
  
  // Change position to key value array
  
 public static function toPos(Position $pos) {
   
    $vector = $pos->asVector3();
    
    return [
      "x" => round($vector->getX(),2),
      "y" => round($vector->getY(),2),
      "z" => round($vector->getZ(),2),
      "level" => $pos->getWorld()->getFolderName()
      ];
      
  }
  
}