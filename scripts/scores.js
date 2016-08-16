$(document).ready(function () {
	atualizaTable();
	$("input[name='tipoPesquisa']").on('change', function () {
		atualizaTable();
	});
	
	$("#mostrarTodos").on('change', function () {
		atualizaTable();
	});
		
	function atualizaTable() {
		try {
			var mostrarTodos = $('#mostrarTodos').is(':checked');
			$("input[name='tipoPesquisa']").each(function () {
				$(this).prop('disabled', 'disabled');
			});
			$('#mostrarTodos').prop('disabled', 'disabled');
			
			$.post('http://www.adii.esy.es/boatGame/src/ajax.php', 
				{
					'action' : 'getRanking',
					'type' 	: $('.tipoPesquisa:checked').val(),
					'mostrarTodos' : mostrarTodos,
				},function (response) {
					var tableRanking = $("<table class='tableRanking' border='2'></table>");
					var thead = "<thead><th>Ranking</th><th>player</th><th>score Geral</th>" +
								"<th>score Unico </th><th>Subs Destruidos</th>" +
								"<th>Pont. por minuto </th> <th> Dispositivo </th></thead>";
								
					tableRanking.append(thead);
					var json = $.parseJSON(response);
					for(var it = 0; it < json.length; it++) {
						var ranking = it+1;
						var tr = "<tr> <td>" + ranking + "</td>" +
							"<td>" + json[it].nome + "</td>" +
							"<td>" + json[it].scoreGeral + "</td>" +
							"<td>" + json[it].scoreUnico + "</td>" +
							"<td>" + json[it].qtdSubsDestruidos + "</td>" +
							"<td>" + json[it].tempoJogo + "</td>" +
							"<td>" + json[it].tipoDispositivo + "</td>" +
						"</tr>";
						tableRanking.append(tr);
					}
					$('#content').html(tableRanking);
					$('.tableRanking tr:eq(1)').css('background-color', 'gold');
					$('.tableRanking tr:eq(2)').css('background-color', 'silver');
					$('.tableRanking tr:eq(3)').css('background-color', '#DB9370');
					$("input[name='tipoPesquisa']").each(function () {
						$(this).prop('disabled', '');
					});
					$('#mostrarTodos').prop('disabled', '');
				}
			)
		} catch ( e ) {
			$('#content').html("<h1>Não foi possivel conectar com a base de dados! Tente novamente!</h1>");
		}
	}
});