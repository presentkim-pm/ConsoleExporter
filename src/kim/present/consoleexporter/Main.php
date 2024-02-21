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

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

use function count;
use function file_get_contents;
use function is_dir;
use function rmdir;
use function scandir;
use function time;

final class Main extends PluginBase{
	private ConsoleOutputInterceptor $interceptor;
	private ConsoleOutputExporter $exporter;

	protected function onLoad() : void{
		$this->interceptor = new ConsoleOutputInterceptor($this);
		$this->exporter = new ConsoleOutputExporter(
			file_get_contents($this->getResourcePath("template.html")),
			file_get_contents($this->getResourcePath("template.js")),
			file_get_contents($this->getResourcePath("template.css")),
			file_get_contents($this->getResourcePath("template.svg"))
		);
	}

	protected function onDisable() : void{
		$this->interceptor->disable();
		$this->interceptor->flushBuffer();
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if($this->interceptor->isEnabled()){
			$this->interceptor->disable();

			$path = $this->getDataFolder() . "console-exporter-" . time() . ".html";
			$buffer = $this->interceptor->flushBuffer();
			$this->exporter->export($path, $buffer);
			$sender->sendMessage(
				TextFormat::GREEN . "Console recoding stopped. File exported to " .
				TextFormat::DARK_GREEN . $path
			);
		}else{
			$this->interceptor->enable();
			$sender->sendMessage(TextFormat::GREEN . "Console recoding started");
		}
		return true;
	}
}
