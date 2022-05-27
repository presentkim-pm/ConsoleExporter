<!-- PROJECT BADGES -->
<div align="center">

[![Poggit CI][poggit-ci-badge]][poggit-ci-url]
[![Poggit Version][poggit-version-badge]][poggit-release-url]
[![Poggit Downloads][poggit-downloads-badge]][poggit-release-url]
[![Stars][stars-badge]][stars-url]
[![License][license-badge]][license-url]

</div>


<!-- PROJECT LOGO -->
<br />
<div align="center">
  <img src="https://raw.githubusercontent.com/presentkim-pm/ConsoleExporter/main/assets/icon.png" alt="Logo" width="80" height="80">
  <h3>ConsoleExporter</h3>
  <p align="center">
    Recording console and exports to html!

[View in Poggit][poggit-ci-url] · [Report a bug][issues-url] · [Request a feature][issues-url]

  </p>
</div>


<!-- ABOUT THE PROJECT -->
## About The Project
![Project Preview][project-preview]

This plugin is useful for creating test images of console-based plugins.
It records the console and outputs it as an html file.  
You can download it as a png through the controls button of the html file.

#### Commands and Permissions
|            |         name          | description                                         | extra                                               |
|------------|:---------------------:|-----------------------------------------------------|:----------------------------------------------------|
| Command    |    `consoleexport`    | -Start console recording <br/> -Export as html file | aliases: `ce`<br/>permission: `consoleexporter.cmd` |
| Permission | `consoleexporter.cmd` | Allows use consoleexport command                    | default: `op`                                       |


-----

## How To Use:
1. Type `/consoleexporter`(or `/ce`) to start recoding.
2. Do anything you want to record.
3. Retype `/consoleexporter`(or `/ce`) to stop recording.
4. The result is output as an HTML file.
- It'll be export to `{plugin_data}/ConsoleExporter/console-exporter-{timestamp}.html`

### Advanced usage:
##### `annotation` : Type anything starts with `#`. It will be not executing as command.
##### `line-break` : If you type only `#`. It will be replace to empty line.

-----

## Target software:
This plugin officially only works with [`Pocketmine-MP`](https://github.com/pmmp/PocketMine-MP/).

-----

## Installation
1) Download `.phar` from [Poggit release][poggit-release-url]
2) Move downloaded `.phar` file to server's **/plugins/** folder
3) Restart the server

-----

## Downloads
> **All released versions [here][poggit-release-url]**

> **All built versions [here][poggit-ci-url]**

-----

## License
Distributed under the **LGPL 3.0**. See [LICENSE][license-url] for more information


[poggit-ci-badge]: https://poggit.pmmp.io/ci.shield/presentkim-pm/ConsoleExporter/ConsoleExporter?style=for-the-badge
[poggit-version-badge]: https://poggit.pmmp.io/shield.api/ConsoleExporter?style=for-the-badge
[poggit-downloads-badge]: https://poggit.pmmp.io/shield.dl.total/ConsoleExporter?style=for-the-badge
[stars-badge]: https://img.shields.io/github/stars/presentkim-pm/ConsoleExporter.svg?style=for-the-badge
[license-badge]: https://img.shields.io/github/license/presentkim-pm/ConsoleExporter.svg?style=for-the-badge

[poggit-ci-url]: https://poggit.pmmp.io/ci/presentkim-pm/ConsoleExporter/ConsoleExporter
[poggit-release-url]: https://poggit.pmmp.io/p/ConsoleExporter
[stars-url]: https://github.com/presentkim-pm/ConsoleExporter/stargazers
[releases-url]: https://github.com/presentkim-pm/ConsoleExporter/releases
[issues-url]: https://github.com/presentkim-pm/ConsoleExporter/issues
[license-url]: https://github.com/presentkim-pm/ConsoleExporter/blob/main/LICENSE

[project-icon]: https://raw.githubusercontent.com/presentkim-pm/ConsoleExporter/main/assets/icon.png
[project-preview]: https://raw.githubusercontent.com/presentkim-pm/ConsoleExporter/main/assets/project-preview.png
