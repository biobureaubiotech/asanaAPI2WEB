<? require 'config.php'; ?>
<?

$acao = $_POST['acao'];

if($acao=='pesquisar'){
	$dataInicial = preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $_POST['dataInicial'] ) ? $_POST['dataInicial'] : null;
	$dataFinal = preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $_POST['dataFinal'] ) ? $_POST['dataFinal'] : null;
	$tagSelecionada = $_POST['tagId'];
	if($dataInicial==null){
		$dataInicial = date("d/m/Y", strtotime( "previous monday" ) );
	}
	if($dataFinal==null){
		$dataFinal = date("d/m/Y");
	}
}else{
	$dataInicial = date("d/m/Y", strtotime( "previous monday" ) );
	$dataFinal = date("d/m/Y");
	$tagSelecionada = 0;
	
}


?>
<html>
<head>
	<title>BioBureau / Asana / Tasks</title>
	<meta charset="UTF-8"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="js/themes/default/style.css" />
	<link href= "js/thickbox.css" rel="stylesheet" type="text/css"/>
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/thickbox.js"></script>
	<script src="js/jstree.js"></script> 
	<script>

		function resumo(check, user){
			if(check.checked){
				//alert($('#tagId option:selected').val());
				check.disabled = true;
				$('#tasks_'+user).html( $('#dv_loading').html() );
				$('#tasks_'+user).load('_resumo_tasks.php?user='+user+'&tag='+$('#tagId option:selected').val()+'&dtIni=<? echo $dataInicial; ?>&dtFim=<? echo $dataFinal; ?>', function(){ check.disabled= false; }  );
			}else{
				
			}
		}

		function listarTasks(titulo, user){
			tb_show(titulo,'#TB_inline?width=660&height=350&inlineId=dv_tasks_'+user+'&modal=false', false);
			$('#dv_tree_'+user).jstree();
		}

		function enviar(btn){
			btn.disabled = true;
			btn.value = 'Aguarde...';
			document.forms[0].acao.value = 'pesquisar';
			document.forms[0].submit();
			$('#conteudo').hide();
		}
		
		window.onload = function(){
							// $('#jstree').jstree();
						};
						
	</script>
	<style type="text/css">
		body {
		    font-family: Arial, Verdana, Helvetica, sans-serif; 
		    font-size: 14px;
		    color: #333333;
		    background-color: #dfeee7;
		    margin: 10px;
		}
		table.lista { border: solid 1px #dbdbdb; }
	
	</style>
</head>
<body>
<div id="dv_loading" style="display: none;">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td><img src="images/ajax-loader3.gif"/></td>
			<td style="padding-left: 10px; font-size:11px; color:#666666;">Consultando o Asana, pode levar alguns segundos...</td>
		</tr>
	</table>
</div>



<table width="100%" cellpadding="5" cellspacing="5" style="background-color: #ffffff;">
	<tr>
		<td>
	<form action="" method="post">
	<input name="acao" type="hidden" value="" />
		
	<div id="pesquisa">
		<table align="center" cellpadding="5" cellspacing="5" style="background-color: #f5f5f5; border: solid 2px #cccccc;">
		<tr>
			<td>Data Inicial</td>
			<td><input type="text" name="dataInicial" style="width: 100px;" value="<? echo $dataInicial; ?>" /></td>
			<td style="padding-left: 10px;">Data Final</td>
			<td><input type="text" name="dataFinal" style="width: 100px;" value="<? echo $dataFinal; ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td>Tag</td>
			<td colspan="3">
				<select name="tagId" id="tagId" onchange="$('#conteudo').hide();">
					<option value=""></option>
				<?
				$stdArray = getJSON('tags');
				$rs = $stdArray->data;  
				//print_r($rs);
		        foreach($rs as $key => $value){
					?>
					<option <? echo $tagSelecionada==$value->id?'selected':''; ?> value="<? echo $value->id; ?>"><? echo $value->name; ?></option>
					<?
				}
				?>
				</select></td>
				<td><input type="button" value="Pesquisar" onclick="enviar(this);" /></td>
		</tr>
		</table>
	</div>
	</form>
	
<?

if($acao=='pesquisar'){
	?>	
	<div id="conteudo">
	<br>
	<table width="100%" cellpadding="4" cellspacing="0" border="0" style="border: solid 1px #cccccc;">
		<tr>
			<td style="width:30px; background-color:#dbdbdb;"></td>
			<td style="width:400px; font-size:10px; color:#ffffff;font-weight:bold; background-color:#dbdbdb;">NOME</td>			
			<td style="font-size:10px; color:#ffffff;font-weight:bold; background-color:#dbdbdb;">RESUMO DE TASKS</td>
		</tr>
		<?
		$stdArray = getJSON('users');
		$rsLista = $stdArray->data;  
        
		$bgcolor="#f5f5f5";
		$bg_int = 0;
		foreach ($rsLista as $key => $value){
			?>
			<tr style="background-color:<? echo ($bg_int++ % 2 == 0)?$bgcolor:""; ?>;">
			<td align="center"><input type="checkbox" id="chk_<? echo $value->id; ?>" value="1" onclick="resumo(this, '<? echo $value->id; ?>');"/></td>
			<td style="padding-left: 5px;"><label for="chk_<? echo $value->id; ?>" ><? echo $value->name; ?></label></td>
			<td style="font-size:11px; color:#666666;"><div id="tasks_<? echo $value->id; ?>"></div></td>
			</tr>
			<?
		} ?>
	</table>
	</div>
	<?
}
?>	
	<!-- 
	<br>
	<br>
	
	<div id="jstree">
		<ul>
		<?
		$stdArray = getJSON('users');
		$rs = $stdArray->data;  
        //print_r($rs);
        $i = 0;
		foreach($rs as $key => $value){
			//$i++;
			if($value->id == 10179900243881){
			?>
			<li id="<? echo $value->id; ?>"><? echo $value->name; ?>
			<ul>
				<?
				/*
				$stdSub = getJSON('tasks?workspace=10179896658126&assignee='.$value->id.'&completed_since=2015-02-09T00:00:00.000Z');
				$rsSub = $stdSub->data;  
		        //print_r($rsSub);
		        $totalhs = 0;
				foreach($rsSub as $keySub => $valueSub){ 	
					
					$stdTask = getJSON('tasks/'.$valueSub->id);
					$task = $stdTask->data;  
		        	if($task->completed){	
		        		$nomeTask = $valueSub->name;
		        		$pattern = "/^\[(\d+([\.,]\d{1,2})?) ?h\]/";
					    preg_match_all($pattern, $nomeTask, $matches);
					    //print_r($matches);
					    $hs = str_replace(",",".",$matches[1][0]);
					    
					    $totalhs += $hs;
						?>
						<li id="<? echo $valueSub->id; ?>"><? echo $nomeTask; ?></li>
						<?
					}
					
				}
				*/
				?>
			</ul>
			</li>
			<?
			}
			//if($i>2)break;
		}
		?>  
	    </ul>
	</div>
	
	<br>
	 -->
		</td>
	</tr>
</table>
</body>
</html>