$(document).ready(function () {
	
	$("input[name='tipoPesquisa']").on('change', function () {
		var mostrarTodos = $('#mostrarTudo').is(':checked');
		$("input[name='tipoPesquisa']").each(function () {
			$(this).prop('disabled', 'disabled');
		});
		
		$.post('http://www.adii.esy.es/boatGame/src/ajax.php', 
			{
				'action' : 'getRanking',
				'type' 	: $('.tipoPesquisa:checked').val(),
				'mostrarTodos' : mostrarTodos,
			},function (response) {
				var tableRanking = $("<table class='tableRanking'></table>");
				var thead = "<thead><th>player</th><th>score Geral</th>" +
							"<th>score Unico </th><th>Quantidade de Submarinos Destruidos</th>" +
							"<th>Pontuação por minuto </th> <th> Dispositivo </th></thead>";
							
				tableRanking.append(thead);
				var json = $.parseJSON(response);
				for(var it = 0; it < json.length; it++) {
					var tr = "<tr> " +
						"<td>" + json[it].nome + "<td>" +
						"<td>" + json[it].scoreGeral + "<td>" +
						"<td>" + json[it].scoreUnico + "<td>" +
						"<td>" + json[it].qtdSubsDestruidos + "<td>" +
						"<td>" + json[it].tempoJogo + "<td>" +
						"<td>" + json[it].tipoDispositivo + "<td>" +
					"</tr>";
					tableRanking.append(tr);
				}
				$('#content').html(tableRanking);
				$("input[name='tipoPesquisa']").each(function () {
					$(this).prop('disabled', '');
				});
			}
		)
		
	});
		
});