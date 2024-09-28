<?php
session_start();

require_once __DIR__ . "/../packages/http-response/http-response.php";

class Auth {


	public static function check() {
		if (!isset($_SESSION["SESSION_AUTH"]) || !$_SESSION["SESSION_AUTH"]) {
			$_SESSION["SESSION_AUTH"] = false;
			$_SESSION["PHP_AUTH_USER"] = null;
			return false;
		}

		return true;
	}

	public static function getUserAuthenticated() {
		if (!isset($_SESSION["SESSION_AUTH"]) || !$_SESSION["SESSION_AUTH"]) {
			$_SESSION["SESSION_AUTH"] = false;
			$_SESSION["PHP_AUTH_USER"] = null;
			HttpResponse::sendBody([
				"message" => "Você não está autenticado no sistema, faça login antes de prosseguir"
			], HttpResponse::UNAUTHORIZED);
		}

		return [
			"authenticated" => $_SESSION["SESSION_AUTH"],
			"user" => $_SESSION["PHP_AUTH_USER"]
		];
	}

	public static function login(array $user_data, string $password) {
		$verified_password = password_verify($password, $user_data["password"]);
		if ($verified_password) {
			$_SESSION["PHP_AUTH_USER"] = $user_data;
			$_SESSION["SESSION_AUTH"] = true;
			return true;
		}
		else {
			$_SESSION["SESSION_AUTH"] = false;
			$_SESSION["PHP_AUTH_USER"] = null;
			HttpResponse::sendBody([
				"message" => "Senha incorreta, verifique a senha e tente novamente"
			], HttpResponse::UNAUTHORIZED);
		}
	}

	public static function logout() {
		$_SESSION["SESSION_AUTH"] = false;
		$_SESSION["PHP_AUTH_USER"] = null;
		return true;
	}
}
