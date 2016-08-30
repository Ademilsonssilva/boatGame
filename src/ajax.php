<?php

header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Sao_Paulo');
#error_reporting(E_ALL);
include('connection.php');
$conn = Connection::getConnection();

$file = fopen("dblog.txt", "a+");

if (!isset($_POST['action'])) {
   registerScore(dadosTeste());
} else {
   switch ($_POST['action']) {
      case 'registerScore':
         registerScore($_POST);
         break;
      case 'getRanking':
         echo searchScoreBy($_POST['type'], $_POST['mostrarTodos']);
         break;
   }
}

fclose($file);
exit;

function _POSTToString($post)
{
   $data = Date('Y-m-d H:i:s', time());
   return "('{$post['playerName']}', {$post['scoreGeral']}, "
         . "{$post['qtdSubsDestruidos']}, {$post['scoreUnico']}, "
         . "{$post['tempoJogo']}, '{$post['tipoDispositivo']}', '$data')";
}

function registerScore($post)
{
   global $conn, $file;
   try {
      $query = "INSERT INTO scores (nome, scoreGeral, qtdSubsDestruidos, scoreUnico, tempoJogo, tipoDispositivo, datahora) " .
            " VALUES " . _POSTToString($post);

      if ($stmt = $conn->prepare($query)) {
         $stmt->execute();
      }
      registraLog($file, 'registerScore', _POSTToString($post));
   } catch (Exception $e) {
      fwrite($file, $e->getMessage() . "\n");
   }
}

function searchScoreBy($tipo, $mostrarTodos)
{
   global $conn;
   try {

      $query = "  SELECT nome, scoreGeral, qtdSubsDestruidos, scoreUnico, "
            . "scoreGeral/tempoJogo as tempoJogo, tipoDispositivo "
            . "FROM scores ORDER BY $tipo DESC ";

      $array = [];
      if ($stmt = $conn->prepare($query)) {
         $stmt->execute();
         $stmt->bind_result(
               $name, $scoreGeral, $qtdSubsDestruidos, $scoreUnico, $tempoJogo, $tipoDispositivo
         );

         while ($stmt->fetch()) {
            $row = [
               'nome' => $name,
               'scoreGeral' => $scoreGeral,
               'qtdSubsDestruidos' => $qtdSubsDestruidos,
               'scoreUnico' => $scoreUnico,
               'tempoJogo' => $tempoJogo,
               'tipoDispositivo' => $tipoDispositivo,
            ];

            $passou = true;

            if ($mostrarTodos == 'false') {
               if (empty($array)) {
                  $array[] = $row;
               } else {
                  foreach ($array as $arr) {
                     if (strtoupper($row['nome']) == strtoupper($arr['nome'])) {
                        $passou = false;
                     }
                  }
                  if ($passou) {
                     $array[] = $row;
                  }
               }
            } else {
               $array[] = $row;
            }
         }
      }
      return json_encode($array);
   } catch (Exception $e) {
      fwrite($file, $e->getMessage() . "\n");
   }
}

function registraLog(&$file, $type, $data)
{
   fwrite($file, "action-> registerScore; time: " . Date('d/m/Y H:i:s') . "\n");
   switch ($type) {
      case 'registerScore':
         fwrite($file, "Dados -> {$data}\n\n");
         break;
   }
}

function dadosTeste()
{
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
