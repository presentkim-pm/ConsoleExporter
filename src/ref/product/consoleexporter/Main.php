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

namespace ref\product\consoleexporter;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat;
use ref\product\consoleexporter\listener\ConsoleCommandListener;
use ref\product\consoleexporter\task\FlushingBufferTask;

use function array_keys;
use function array_map;
use function array_values;
use function explode;
use function file_put_contents;
use function htmlspecialchars;
use function implode;
use function ob_end_flush;
use function ob_start;
use function ord;
use function preg_match;
use function preg_replace;
use function rtrim;
use function str_contains;
use function str_replace;
use function stream_get_contents;
use function strlen;
use function strrchr;
use function substr;
use function time;
use function wordwrap;

final class Main extends PluginBase{
    private const HTML_EOL = "<br/>";
    private const HTML_SPACE = "&nbsp;";
    private const HTML_TAB = self::HTML_SPACE . self::HTML_SPACE . self::HTML_SPACE . self::HTML_SPACE;

    private string $template = "";

    private bool $recording = false;
    private ?TaskHandler $taskHandler;
    private string $buffer = "";

    protected function onLoad() : void{
        $template = stream_get_contents($this->getResource("template.html"));
        $templateScript = stream_get_contents($this->getResource("template.js"));
        $templateStyle = stream_get_contents($this->getResource("template.css"));
        $templateControls = stream_get_contents($this->getResource("template.svg"));

        $this->template = str_replace(
            ["<!--[SCRIPT]-->", "<!--[STYLE]-->", "<!--[CONTROLS]-->"],
            array_map(static fn(string $src) : string => preg_replace("/\s+\r*\n*\s*/", " ", $src),
                [self::wrapTag("script", $templateScript), self::wrapTag("style", $templateStyle), $templateControls]
            ),
            $template
        );
    }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new ConsoleCommandListener($this), $this);
    }

    protected function onDisable() : void{
        $exportsPath = $this->stopRecoding();
        if($exportsPath !== null){
            $this->getLogger()->info(TextFormat::GREEN . "Console recoding stoped. Exports to " . TextFormat::DARK_GREEN . $exportsPath);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($this->recording){
            $sender->sendMessage(TextFormat::GREEN . "Console recoding stoped. Exports to " . TextFormat::DARK_GREEN . $this->stopRecoding());
        }else{
            $sender->sendMessage(TextFormat::GREEN . "Console recoding started");
            $this->startRecoding();
        }
        return true;
    }

    public function isRecording() : bool{
        return $this->recording;
    }

    public function writeBuffer(string $buffer) : string{
        if($this->recording){
            $this->buffer .= str_replace("\r\n", PHP_EOL, $buffer);
        }

        return $buffer;
    }

    private function startRecoding() : void{
        $this->recording = true;
        $this->buffer = "";
        ob_start([$this, "writeBuffer"]);
        $this->taskHandler = $this->getScheduler()->scheduleRepeatingTask(new FlushingBufferTask($this), 1);
    }

    private function stopRecoding() : ?string{
        if(!$this->recording){
            return null;
        }
        ob_end_flush();
        $this->recording = false;
        $this->taskHandler->cancel();
        $this->taskHandler = null;

        $buffer = rtrim(str_replace("\r\n", PHP_EOL, $this->buffer));
        if(str_contains($buffer, PHP_EOL)){
            $buffer = substr($buffer, 0, (strlen($buffer)) - (strlen(strrchr($buffer, PHP_EOL))));
        }else{
            $buffer = "";
        }
        $this->buffer = $buffer;
        $title = "console-exporter-" . time();
        $filename = "{$this->getDataFolder()}$title.html";

        $replacements = [
            PHP_EOL => self::HTML_EOL,
            " " => self::HTML_SPACE,
            Terminal::$FORMAT_RESET => TextFormat::RESET,
            Terminal::$FORMAT_BOLD => TextFormat::BOLD,
            Terminal::$FORMAT_OBFUSCATED => TextFormat::OBFUSCATED,
            Terminal::$FORMAT_ITALIC => TextFormat::ITALIC,
            Terminal::$FORMAT_UNDERLINE => TextFormat::UNDERLINE,
            Terminal::$FORMAT_STRIKETHROUGH => TextFormat::STRIKETHROUGH,
            Terminal::$COLOR_BLACK => TextFormat::BLACK,
            Terminal::$COLOR_DARK_BLUE => TextFormat::DARK_BLUE,
            Terminal::$COLOR_DARK_GREEN => TextFormat::DARK_GREEN,
            Terminal::$COLOR_DARK_AQUA => TextFormat::DARK_AQUA,
            Terminal::$COLOR_DARK_RED => TextFormat::DARK_RED,
            Terminal::$COLOR_PURPLE => TextFormat::DARK_PURPLE,
            Terminal::$COLOR_GOLD => TextFormat::GOLD,
            Terminal::$COLOR_GRAY => TextFormat::GRAY,
            Terminal::$COLOR_DARK_GRAY => TextFormat::DARK_GRAY,
            Terminal::$COLOR_BLUE => TextFormat::BLUE,
            Terminal::$COLOR_GREEN => TextFormat::GREEN,
            Terminal::$COLOR_AQUA => TextFormat::AQUA,
            Terminal::$COLOR_RED => TextFormat::RED,
            Terminal::$COLOR_LIGHT_PURPLE => TextFormat::LIGHT_PURPLE,
            Terminal::$COLOR_YELLOW => TextFormat::YELLOW,
            Terminal::$COLOR_WHITE => TextFormat::WHITE,
            Terminal::$COLOR_MINECOIN_GOLD => TextFormat::MINECOIN_GOLD,
        ];
        $replacedLines = explode(self::HTML_EOL, str_replace(array_keys($replacements), array_values($replacements), htmlspecialchars($buffer)));
        $wrappedLines = array_map(static fn(string $line) : string => wordwrap($line, 180, self::HTML_EOL . self::HTML_TAB), $replacedLines);
        $htmlContents = "";
        $classes = [];
        foreach(TextFormat::tokenize(implode(self::HTML_EOL, $wrappedLines)) as $token){
            if($token === TextFormat::RESET){
                $classes = [];
            }elseif(preg_match("/" . TextFormat::ESCAPE . "([0-9a-g])/u", $token, $matches) > 0){
                $classes[0] = "t$matches[1]";
            }elseif(preg_match("/" . TextFormat::ESCAPE . "([k-o])/u", $token, $matches) > 0){
                $classes[ord($matches[1])] = "t$matches[1]";
            }elseif(!empty($classes)){
                $htmlContents .= self::wrapTag("span", $token, implode(" ", $classes));
            }else{
                $htmlContents .= $token;
            }
        }

        file_put_contents($filename, str_replace(["<!--[TITLE]-->", "<!--[CONTENTS]-->"], [
            self::wrapTag("title", $title),
            $htmlContents
        ], $this->template));
        return $filename;
    }

    private static function wrapTag(string $tag, string $innerHTML, string $classList = "") : string{
        if($classList === ""){
            return "<$tag>$innerHTML</$tag>";
        }
        return "<$tag class=\"$classList\">$innerHTML</$tag>";
    }
}