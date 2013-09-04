<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>XBMC Remote</title>
</head>
<body>
	<button type="button" name="Input" value="Back">Back</button><br>
	<button type="button" name="Input" value="ContextMenu">ContextMenu</button><br>
	<button type="button" name="Input" value="Down">Down</button><br>
	<button type="button" name="Input" value="ExecuteAction">ExecuteAction</button><br>
	<button type="button" name="Input" value="Home">Home</button><br>
	<button type="button" name="Input" value="Info">Info</button><br>
	<button type="button" name="Input" value="Left">Left</button><br>
	<button type="button" name="Input" value="Right">Right</button><br>
	<button type="button" name="Input" value="Select">Select</button><br>
	<button type="button" name="Input" value="SendText">SendText</button><br>
	<button type="button" name="Input" value="ShowCodec">ShowCodec</button><br>
	<button type="button" name="Input" value="ShowOSD">ShowOSD</button><br>
	<button type="button" name="Input" value="Up">Up</button><br>
	<br>
	<button type="button" name="Player" value="PlayPause">Play/Pause</button><br>
	<button type="button" name="Player" value="Stop">Stop</button><br>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script>
	function request(action, params, objid, process){
		if(typeof params != 'object'){
			objid = params;
			params = {};
		}
		if(typeof objid == 'function'){
			process = objid;
			objid = null;
		}
	
		jQuery.ajax({
			url: 'do.php',
			type: 'POST',
			data:{
				action: action,
				params: params,
				objid: objid
			},
			dataType: 'json',
			success: function(json){
				console.log(json);
				
				if(!json) return;
				
				if(json.error){
					alert(json.error.message);
				}else if(typeof process == 'function'){
					process(json);
				}
			},
			error: function(jqXHR){
				console.log(jqXHR.responseText);
			}
		});
	}
	
	jQuery(function($){
		$('button').click(function(){
			request($(this).attr('name')+'.'+$(this).val());
		});
		
		request('VideoLibrary.GetTVShows', {properties: ["title", "plot", "sorttitle", "art"]});
		
		//request('VideoLibrary', 'GetTVShowDetails', 38);
	});
	</script>
</body>
</html>