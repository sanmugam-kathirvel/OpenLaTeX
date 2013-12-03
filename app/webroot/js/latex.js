$(document).ready(function() {
	/* fix window height and width */
	var webroot = 'http://'+location.hostname+'/latex/'
	var editor = CodeMirror.fromTextArea(document.getElementById("code"), {mode: "text/x-latex", lineNumbers: true, lineWrapping: true,});
	// compile the code

	$('#compile').click(function() {
		var filedata = editor.getValue();
		if(filedata == '')
			return;
		var filename = '';
		if($('.current-file-name').val())
			filename = $('.current-file-name').val();
		$.ajax({
			type : 'POST',
			url : webroot + "compile",
			data : {
				'code' :  $.base64('encode', filedata),
				'filename' : filename
			},
			success : function(data){
				output = data;
				output = JSON.parse(data);
				console.log(output);
				/* display warnings */
				var wcount = 0;
				var warning_html = "<ul>";
				for(var i in output.warnings) {
				  	warning_html += "<li>"+output.warnings[i]+"</li>";
				  	wcount ++;
				}
				warning_html += "</ul>";
				$('.warning-count').html(wcount);
				$('.warning-list').html(warning_html);
				
				/* display errors */
				var ecount = 0;
				var error_html = "<ul>";
				for(var i in output.errors) {
				  	error_html += "<li>"+output.errors[i]+"</li>";
				  	ecount ++;
				}
				error_html += "</ul>";
				$('.error-count').html(ecount);
				$('.error-list').html(error_html);
			}
		});
	});
	
	
	$('.add-file').click(function(){
		var fileName = prompt("Please enter file name","help.tex");
		
		/* validate filename and extentions */		
		if(fileName.indexOf('.')){
			/* TODO allow only alpa */
			/* validate extentions */
			var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
			if(ext != 'tex'){
				alert('tex files only allowed');
			}else{
				if(fileName.length < 20){
					$.ajax({
						type : 'POST',
						url : webroot + "addfile",
						data : {
							'filename' : fileName
						},
						success : function(data){
							output = data;
							output = JSON.parse(data);
							console.log(output);
							if(output.status){
								updateFileList();
							}
						}
					});
				}else{
					alert('size above 15');
				}
			}
		}else{
			alert('Please enter the file extentions');
		}
	});
	
	function updateFileList(){
		$.ajax({
			type : 'POST',
			url : webroot + "resources_list",
			data : {
				'type' : 'json'
			},
			success : function(data){
				output = data;
				output = JSON.parse(data);
				var fileListHtml = "<ul>";
				for(var i in output) {
				  	fileListHtml += "<li>"+output[i]+"</li>";
				}
				fileListHtml += "</ul>";
				$('.list_files').html(fileListHtml);
			}
		});
	}
	/*
	function updateCurrentFile(){
		$.ajax({
			type : 'POST',
			url : webroot + "resources_list",
			data : {
				'type' : 'json'
			},
			success : function(data){
				output = data;
				output = JSON.parse(data);
				var fileListHtml = "<ul>";
				for(var i in output) {
				  	fileListHtml += "<li>"+output[i]+"</li>";
				}
				fileListHtml += "</ul>";
				$('.list_files').html(fileListHtml);
			}
		});
	}*/
	/* auto save every 10 sec */
	setInterval(function() {
		autoSave();
	}, 5000);
	function autoSave(){
		var filename = '';
		var filedata = editor.getValue();
		if(filedata == '')
			return;
		var filedataMD5 = $.md5(filedata);
		if($('.data-checksum').val() == filedataMD5)
			return;
		if($('.current-file-name').val())
			filename = $('.current-file-name').val();
		$.ajax({
			type : 'POST',
			url : webroot + "autosave",
			data : {
				'filedata' : $.base64('encode', filedata),
				'filename' : filename
			},
			beforeSend: function() {
				$('.data-checksum').val(filedataMD5);
			},
			success : function(data){
				output = data;
				//output = JSON.parse(data);
				console.log(output);
			}
		});
	}

});

function setWidthHeight(){
	var windowHeight = $(window).height();
	//alert($(window).width());
	//var windowWidth = $(window).width();
	//var codeWidth = parseInt(windowWidth/2);
	//$('.code-container').css({'height':windowHeight+'px', 'width':windowWidth+'px'});
	//$('.code-input').css({'width':codeWidth+'px'});
	$('.CodeMirror-scroll, .CodeMirror').css({'height':(windowHeight-90)+'px'});
	//$('.code-output').css({'width':codeWidth+'px', 'height':(windowHeight-60)+'px', 'left':codeWidth+'px'});
	//$('.code-container').css({'height':windowHeight+'px', 'width':windowWidth+'px'});
	//$('.code-input').css({'width':codeWidth+'px'});
	//$('.CodeMirror-scroll, .CodeMirror').css({'height':(windowHeight-60)+'px'});
	$('.code-output iframe').css({'height':(windowHeight-90)+'px'});
}
$(window).bind("load", function() {
	setWidthHeight();
});

$(window).resize(function() {
	setWidthHeight();
});

function getCode(){
	var editor = CodeMirror.fromTextArea(document.getElementById("code"), {mode: "text/x-latex", lineNumbers: true, lineWrapping: true,});
	alert(editor.getValue());
	document.getElementById('originalCode').value = $.base64('encode', editor.getValue());
	
	return false;
}
