<?php

/**
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author       PresentKim (debe3721@gmail.com)
 * @link         https://github.com/PresentKim
 * @license      https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace kim\present\consoleexporter;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\HandlerListManager;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

use function ob_end_flush;
use function ob_start;
use function trim;

use const PHP_EOL;

final class ConsoleOutputInterceptor implements Listener{
    use PluginOwnedTrait;

    private bool $enabled = false;
    private string $buffer = "";
    private ?TaskHandler $taskHandler = null;

    public function flushBuffer() : string{
        $buffer = $this->buffer;
        $this->buffer = "";

        return trim($buffer);
    }

    public function writeBuffer(string $buffer) : string{
        $this->buffer .= $buffer;

        return $buffer;
    }

    public function isEnabled() : bool{
        return $this->enabled;
    }

    public function enable() : void{
        if($this->enabled){
            return;
        }

        // Start capturing output buffer
        ob_start($this->writeBuffer(...));
        Server::getInstance()->getPluginManager()->registerEvents($this, $this->owningPlugin);

        // Start flushing output buffer for avoid console messages delayed
        $this->taskHandler = $this->owningPlugin->getScheduler()->scheduleRepeatingTask(
            new ClosureTask(ob_flush(...)),
            1
        );

        $this->enabled = true;
    }

    /** @priority HIGHEST */
    public function onCommandEvent(CommandEvent $event) : void{
        if(!$this->enabled || !($event->getSender() instanceof ConsoleCommandSender)){
            return;
        }

        $command = trim($event->getCommand());
        if($command[0] === "#"){ // If command starts with "#", Ignore command (comment line)
            $event->cancel();
        }else{ // Else, Apply italic effect to make a distinction from comments
            $this->writeBuffer(TextFormat::ITALIC);
        }
        $this->writeBuffer(TextFormat::GRAY . $command . TextFormat::RESET . PHP_EOL);
    }

    public function disable() : void{
        if(!$this->enabled){
            return;
        }

        // Stop capturing output buffer
        ob_end_flush();
        HandlerListManager::global()->unregisterAll($this);

        // Stop flushing output buffer
        $this->taskHandler->cancel();
        $this->taskHandler = null;

        $this->enabled = false;
    }
}
