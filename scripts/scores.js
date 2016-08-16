$(document).ready(function () {
	
	$("input[name='tipoPesquisa']").on('change', function () {
		var mostrarTodos = $('#mostrarTudo').is(':checked');
		
		$.post('http://www.adii.esy.es/boatGame/src/ajax.php', 
			{
				'action' : 'getRanking',
				'type' 	: $('.tipoPesquisa:checked').val(),
				'mostrarTodos' : mostrarTodos,
			},function (response) {
				var tableRanking = $("<table class='tableRanking'></table>");
				var json = $.parseJSON(response);
				for(var it = 0; it < json.length; it++) {
					var tr = "<tr><td>"+json[it].nome+"<td></tr>";
					tableRanking.html(tr);
				}
				$('#content').html(tableRanking);
			}
		)
		
	});
		
});