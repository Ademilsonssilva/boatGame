$(document).ready(function () {
	
	$("input[name='tipoPesquisa']").on('change', function () {
		var mostrarTodos = $('#mostrarTudo').is(':checked');
		
		$.post('http://www.adii.esy.es/boatGame/src/ajax.php', 
			{
				'action' : 'getRanking',
				'type' 	: $('.tipoPesquisa:checked').val(),
				'mostrarTodos' : mostrarTodos,
			},function (response) {
				$('#content').html(response);
			}
		)
		
	});
		
});