<?php
 
 namespace CodeGhost\How2Do\task;
 
 use pocketmine\{
   scheduler\Task
 };
 
 use CodeGhost\How2Do\manager\moneyLB;
 
 // Update money leaderboard
 
 class moneyTask extends Task {
   
   public function onRun(): void {
     
   $manager = new moneyLB();
  
   $manager->updateMoney();
    
  
   }
   
 }