<?php 

namespace CodeGhost\How2Do\Manager;

use pocketmine\{
  world\Position,
  network\mcpe\protocol\AddPlayerPacket,
  network\mcpe\protocol\RemoveActorPacket,
  network\mcpe\protocol\types\inventory\ItemStackWrapper,
  network\mcpe\protocol\types\inventory\ItemStack,
  network\mcpe\protocol\types\entity\EntityMetadataFlags,
  network\mcpe\protocol\types\entity\EntityMetadataProperties,
  network\mcpe\protocol\types\entity\LongMetadataProperty,
  network\mcpe\protocol\types\entity\FloatMetadataProperty,
  player\Player,
  network\mcpe\protocol\AdventureSettingsPacket,
  item\Item,
  utils\TextFormat,
  Server

};

use Ramsey\Uuid\Uuid;
use CodeGhost\How2Do\Main;

 /**
  * 
  * Class for money leaderboard manager
  * Method:
  * - create
  * - remove
  * - update
  * 
  */
  
class moneyLB {
  
  public function __construct($id = "",?Position $position = null,$money = []) {
    
      $this->id = $id;
      $this->position = $position;
      $this->money = $money;
      
    }
  
  // Create money leaderboard 
  
  public function createMoney() {
    $flags =
    1 <<  EntityMetadataFlags::CAN_SHOW_NAMETAG |
    1 << EntityMetadataFlags::ALWAYS_SHOW_NAMETAG |
    1 << EntityMetadataFlags::IMMOBILE;
   
       
       $ids = self::toId($this->id);
    
    
  $uuid = Uuid::uuid4();
  $adventure = AdventureSettingsPacket::create(0,AdventureSettingsPacket::PERMISSION_NORMAL,-1,1,0,$ids);
  
   
  arsort($this->money);
    
  $pk = AddPlayerPacket::create($uuid,
   implode("\n",$this->formatMoney($this->money)),
   $ids,
   $ids,
   "",
   $this->position,
   null,
   0.0,
   0.0,
   0.0,
   ItemStackWrapper::legacy(ItemStack::null()),
   [
         EntityMetadataProperties::FLAGS => new LongMetadataProperty($flags)  ,
           EntityMetadataProperties::SCALE =>
           new FloatMetadataProperty(0.01)
           
      ],
      $adventure,
      [],
      "",
      2
   );
   
       
       $all = $this->position->getWorld()->getPlayers();
      
      foreach ($all as $p) {
        if(!$p) continue;
        
        $p->getNetworkSession()->sendDataPacket($pk);
      }
      
     Main::getCfg()->createData($ids,$this->id,$this->position,"",$this->money);
    
  }
  
  public function updateMoney() {
   
   foreach (Main::getCfg()->allData() as $k => $v) {
     
     if(isset($v["type"]) && $v["type"] == "money") {
       
       $this->id = $k;
       $pos = $v["position"];
       $level = Server::getInstance()->getWorldManager()->getWorldByName($pos["level"]);
      
       $this->position = new Position($pos["x"],$pos["y"],$pos["z"],$level);
       
      $m = Main::getMoney()->getAll();
      arsort($m);
      $this->money = $m;
       
       $this->createMoney();
       continue;
     }
      
    }
    
  }
  
  public function formatMoney($money) {
    $result = ["§lMoney leaderboard"];
    $count = 1;
    arsort($money);
    
    foreach($money as $name => $bal) {
      array_push($result,"§c$count) §g$name ⟩ §6[$$bal]");
      $count++;
    }
   
    return $result;
  }
  
  
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

}