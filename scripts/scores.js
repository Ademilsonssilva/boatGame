$(document).ready(function () {
	
	$("input[name='tipoPesquisa']").on('change', function () {
		
		$.post('http://www.adii.esy.es/boatGame/src/ajax.php', 
			{
				'action' : 'getRanking',
				'type' 	: $('.tipoPesquisa:checked').val(),
			},function (response) {
				$('#content').html(response);
			}
		)
		
	});
		
});