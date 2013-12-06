$(document).ready(function(){
	var webroot = 'http://'+location.hostname+'/OpenLaTeX/'
	$('.edit-project-title').click(function(){
		var htmlData = $(this).prev('.project-title').html();
		var projectObj = $(this).prev('.project-title');
		var projectName = $(this).prev('.project-title').children('a').html();
		var projectid = $(this).siblings('.projectid').val();
		projectObj.html("<input type='text' name='lname' class='new-project-name' value='"+projectName+"'>");
		$('.new-project-name').focus();
		$('.new-project-name').focusout(function(){
			if(projectName != $(this).val()){
				$.ajax({
						type : 'POST',
						url : webroot + "settitle",
						data : {
							'title' : $(this).val(),
							'projectid' : projectid
						},
						success : function(data){
							output = data;
							output = JSON.parse(data);
							if(output.status)
								location.reload();
						}
					});
			}else{
				projectObj.html(htmlData);
			}
		})
	});
});
