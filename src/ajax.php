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
				echo searchScoreBy($_POST['type']);
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
				$stmt->execute();
			}
			registraLog($file, 'registerScore', _POSTToString($post));
		} catch (Exception $e) {
			fwrite($file, $e->getMessage()."\n");
		}
	}
	
	function searchScoreBy($tipo){
		global $conn;
		try {
			$query = "  SELECT nome, scoreGeral, qtdSubsDestruidos, scoreUnico, tempoJogo, tipoDispositivo
						FROM scores GROUP BY nome ORDER BY $tipo DESC ";
					
			$array = [];
			if ($stmt = $conn->prepare($query)) {
				$stmt->execute();
				$stmt->bind_result(
					$name, 
					$scoreGeral, 
					$qtdSubsDestruidos, 
					$scoreUnico, 
					$tempoJogo, 
					$tipoDispositivo
				);
				
				while ($stmt->fetch()) {
					$row = [
						'nome' => $name,
						'scoreTotal' => $scoreTotal,
						'qtdSubsDestruidos' => $qtdSubsDestruidos,
						'scoreUnico' => $scoreUnico,
						'tempoJogo' => $tempoJogo,
						'tipoDispositivo' => $tipoDispositivo,
					];
					
					$array[] = $row;
				}
			}
			return json_encode($array);
		}catch (Exception $e) {
			fwrite($file, $e->getMessage()."\n");
		}

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