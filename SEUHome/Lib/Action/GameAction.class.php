<?php
class GameAction extends Action {
	public function doge(){
		$this->display('doge');
	}

	public function lottery(){
		echo '欢迎抽奖';
	}
}
?>