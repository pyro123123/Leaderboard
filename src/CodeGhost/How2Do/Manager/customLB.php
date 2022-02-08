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
  utils\TextFormat

};

use Ramsey\Uuid\Uuid;

 /**
  * 
  * Create Normal Leaderboard Class
  * 
  */
  
class customLB {
  
  public function __construct($id,?Position $position = null,string $text = "") {
    $this->id = $id;
    $this->position = $position;
    $this->text = $text;
  }
  
  public function create(?Player $player = null) {
  
   $flags =
    1 <<  EntityMetadataFlags::CAN_SHOW_NAMETAG |
    1 << EntityMetadataFlags::ALWAYS_SHOW_NAMETAG |
    1 << EntityMetadataFlags::IMMOBILE;
   
  $uuid = Uuid::uuid4();
  $adventure = AdventureSettingsPacket::create(0,AdventureSettingsPacket::PERMISSION_NORMAL,-1,1,0,$this->id);
    
  $pk = AddPlayerPacket::create($uuid,
   $this->new_line($this->text),
   $this->id,
   $this->id,
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
    
      if(is_null($player)) {
       $all = $this->position->getWorld()->getPlayers();
      
      foreach ($all as $p) {
        $p->getNetworkSession()->sendDataPacket($pk);
      }
      
        return true;
      }
      $player->getNetworkSession()->sendDataPacket($pk);
  }
  
  public function remove() {
  
    $pk = RemoveActorPacket::create($this->id);
    
     $all = $this->position->getWorld()->getPlayers();
    
    foreach ($all as $v) {
      $v->getNetworkSession()->sendDataPacket($pk);
    }
      
    
  }
  
  public function getPos() {
    return $this->position;
  }
  
  public function getId() {
    return $this->id;
  }
  
   public function new_line($text) {
    $result = str_replace("_",TextFormat::EOL,$text);
    
    return $result;
  }
  
}