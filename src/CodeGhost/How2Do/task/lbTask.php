<?php
 
 namespace CodeGhost\How2Do\task;
 
 use pocketmine\{
   scheduler\Task
 };
 
 use CodeGhost\How2Do\manager\lbManager;

 // Update Normal leaderboard
 
 class lbTask extends Task {
   
   public function onRun():void {
     $manager = new lbManager();
     $manager->load();
   }
   
 }