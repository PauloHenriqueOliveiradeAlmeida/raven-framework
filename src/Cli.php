<?php

namespace Raven;

class Cli
{
	private const appDir = __DIR__ . "/../../../app";

	public function start(?int $port = null)
	{
		$host = "127.0.0.1";
		$port = $port ?? 8000;

		$command =
			"php -S $host:$port " .
			escapeshellarg(self::appDir . "/bootstrap/app.php") .
			" short_open_tag=On";

		echo "Starting Raven server...\n";

		passthru("composer dump-autoload");
		passthru($command);
	}

	public function createControllerFile(string $name)
	{
		$originalName = $name;
		$name = strtoupper($name[0]) . substr($name, 1);

		$controllerExample = file_get_contents(
			__DIR__ . "/Examples/ControllerExample.txt"
		);
		$content = str_replace(
			['{$name}', '{$originalName}'],
			[$name, $originalName],
			$controllerExample
		);

		$this->makeDirectory($name);
		file_put_contents(
			self::appDir . "/Api/$name/$name" . "Controller.php",
			$content
		);

		$appFile = file(self::appDir . "/bootstrap/app.php");

		$controllerForInsert = $name . "Controller::class";
		array_splice($appFile, 6, 0, [
			"use App\\Api\\$name\\$name" . "Controller;\n",
		]);
		for ($i = 0; $i < count($appFile); $i++) {
			if ($appFile[$i] === $controllerForInsert) {
				echo "File is already inserted on app bootstrap\n";
				break;
			}
			if (preg_match("/\b\w+::class\b/", $appFile[$i])) {
				$appFile[$i] = str_replace(["\r", "\n"], "", $appFile[$i]);
				if (
					$appFile[$i + 1] &&
					!preg_match("/\b\w+::class\b/", $appFile[$i + 1])
				) {
					$appFile[$i] = str_replace("]", "", $appFile[$i]);
					$appFile[$i] .=
						$appFile[$i][strlen($appFile[$i]) - 1] !== "," ? ", " : " ";
					$appFile[$i] .= $controllerForInsert . "]";

					break;
				}
			}
		}
		file_put_contents(self::appDir . "/bootstrap/app.php", implode($appFile));

		exec('npm run format "app/bootstrap/app.php" --write');
		echo "\033[1;32mController created succefully!\033[0m";

		return true;
	}

	private function makeDirectory(string $dirName)
	{
		return mkdir(self::appDir . "/Api/$dirName");
	}
}
