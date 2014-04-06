<?php
class GameAction extends Action {
	public function doge(){
		$this->display('doge');
	}

	public function lottery(){
		$this->display('lottery');
	}
	
	public function login(){
		$this->display("login");
	}

	public function register(){
		$this->display("register");
	}
}
?>