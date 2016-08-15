<?php
	header('Access-Control-Allow-Origin: *');  
	#error_reporting(E_ALL);
	include('connection.php');
	$conn = Connection::getConnection();
	
	$file = fopen("dblog.txt", "a+");
	
	if (!isset($_POST['action'])) {
		registerScore(dadosTeste());
	}
	else {
		switch ($_POST['action']) {
			case 'registerScore':
				registerScore($_POST);
				break;
			case 'getRanking':
				echo 'oi';
				break;
		}
	}
	
	fclose($file);
	exit;
	
	function _POSTToString($post) {
		return "('{$post['playerName']}', {$post['scoreGeral']}, {$post['qtdSubsDestruidos']}, {$post['scoreUnico']}, {$post['tempoJogo']}, '{$post['tipoDispositivo']}')";
	}
	
	function registerScore($post) {
		global $conn, $file;
		try {
			$query = "INSERT INTO scores (nome, scoreGeral, qtdSubsDestruidos, scoreUnico, tempoJogo, tipoDispositivo) " . 
						" VALUES ". _POSTToString($post);
						
			//pg_exec($conn, $query);
			if ($stmt = $conn->prepare($query)){
				$stmt->execute();echo 'salvou';
			}
                        else {
                                echo 'nao salvou';
                        }
			registraLog($file, 'registerScore', _POSTToString($post));
		} catch (Exception $e) {
			fwrite($file, $e->getMessage()."\n");
		}
	}
	
	function searchScoreBy($tipo){
		$query = " SELECT * FROM scores GROUP BY nome ORDER BY $tipo DESC LIMIT 15 ";
	}
	
	
	function registraLog(&$file, $type, $data) {
		fwrite($file, "action-> registerScore; time: " . Date('d/m/Y H:i:s') . "\n");
		switch ($type) {
			case 'registerScore':
				fwrite($file, "Dados -> {$data}\n\n");
				break;
		}
	}
	
	function dadosTeste() {
		return [
			'action' => 'registerScore',
			'playerName' => 'Player1',
			'scoreGeral' => '15000',
			'qtdSubsDestruidos' => '100',
			'tempoJogo' => '600',
			'scoreUnico' => '800',
			'tipoDispositivo' => 'PC',
		];
	}