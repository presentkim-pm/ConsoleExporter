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

use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat;

use function file_put_contents;
use function implode;
use function ord;
use function preg_match;
use function preg_replace;
use function rtrim;

final class ConsoleOutputExporter{

    private readonly string $template;

    public function __construct(
        string $templateHtml,
        string $templateScript,
        string $templateStyle,
        string $templateControls
    ){
        $this->template = strtr($templateHtml, [
            "<!--[SCRIPT]-->" => $this->wrapTag("script", $templateScript),
            "<!--[STYLE]-->" => $this->wrapTag("style", $templateStyle),
            "<!--[CONTROLS]-->" => $templateControls,
        ]);
    }

    public function export(string $path, string $buffer) : void{
        $buffer = rtrim($buffer);
        $buffer = $this->convertHtmlEntities($buffer);
        $buffer = $this->convertTerminalCodes($buffer);

        $contents = "";
        $classes = [];
        foreach(TextFormat::tokenize($buffer) as $token){
            if($token === TextFormat::RESET){
                $classes = [];
            }elseif(preg_match("/" . TextFormat::ESCAPE . "([0-9a-g])/u", $token, $matches) > 0){
                $classes[0] = "t$matches[1]";
            }elseif(preg_match("/" . TextFormat::ESCAPE . "([k-o])/u", $token, $matches) > 0){
                $classes[ord($matches[1])] = "t$matches[1]";
            }elseif(!empty($classes)){
                $contents .= $this->wrapTag("span", $token, implode(" ", $classes));
            }else{
                $contents .= $token;
            }
        }

        $contents = strtr($this->template, ["<!--[CONTENTS]-->" => $contents]);
        $contents = preg_replace("/\s+\r*\n*\s*/", " ", $contents);
        file_put_contents($path, $contents);
    }

    /**
     * Convert special characters to HTML entities
     * In addition to the 'htmlspecialchars()' it contains spaces (" ") and line breaks ("\r", "\n").
     */
    private function convertHtmlEntities(string $str) : string{
        return strtr($str, [
            " " => "&nbsp;", // space
            "\r" => "",      // carrge return
            "\n" => "<br/>", // line feed
            "&" => "&amp",   // ampersand
            "\"" => "&quot", // double quote
            "'" => "&apos",  // single quote
            "<" => "&lt",    // less than
            ">" => "&gt"     // greater than
        ]);
    }

    /**
     * Convert terminal codes to text format codes
     */
    private function convertTerminalCodes(string $str) : string{
        return strtr($str, [
            Terminal::$FORMAT_RESET => TextFormat::RESET,
            Terminal::$FORMAT_BOLD => TextFormat::BOLD,
            Terminal::$FORMAT_OBFUSCATED ?: TextFormat::OBFUSCATED => TextFormat::OBFUSCATED,
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
        ]);
    }

    private function wrapTag(string $tag, string $innerHTML, string $classList = "") : string{
        if($classList === ""){
            return "<$tag>$innerHTML</$tag>";
        }
        return "<$tag class=\"$classList\">$innerHTML</$tag>";
    }
}
