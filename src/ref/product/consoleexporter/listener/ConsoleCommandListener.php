<?php

/**            __   _____
 *  _ __ ___ / _| |_   _|__  __ _ _ __ ___
 * | '__/ _ \ |_    | |/ _ \/ _` | '_ ` _ \
 * | | |  __/  _|   | |  __/ (_| | | | | | |
 * |_|  \___|_|     |_|\___|\__,_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  ref-team
 * @link    https://github.com/refteams
 *
 *  &   ／l、
 *    （ﾟ､ ｡ ７
 *   　\、ﾞ ~ヽ   *
 *   　じしf_, )ノ
 *
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace ref\product\consoleexporter\listener;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat;
use ref\product\consoleexporter\Main;

use function str_starts_with;
use function trim;

/** The event listener for outputting command input */
class ConsoleCommandListener implements Listener{
    public function __construct(private Main $plugin){
    }

    /** @priority MONITOR */
    public function onCommandEvent(CommandEvent $event) : void{
        if(!$this->plugin->isRecording() || !($event->getSender() instanceof ConsoleCommandSender)){
            return;
        }

        $command = trim($event->getCommand());
        if(str_starts_with($command, "#")){
            $event->cancel();
            $this->plugin->writeBuffer($command === "#" ? PHP_EOL : TextFormat::GRAY . $command . Terminal::$FORMAT_RESET . PHP_EOL);
        }else{
            $this->plugin->writeBuffer(Terminal::$COLOR_GRAY . Terminal::$FORMAT_ITALIC . $command . Terminal::$FORMAT_RESET . PHP_EOL);
        }
    }
}