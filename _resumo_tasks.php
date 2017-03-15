<? require 'config.php'; ?>
<?

$id_user = preg_match("/^[0-9]+$/", $_GET['user'] ) ? $_GET['user'] : null;
$id_tag =  preg_match("/^[0-9]+$/", $_GET['tag'] ) ? $_GET['tag'] : null;

$dataInicial = preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $_GET['dtIni'] ) ? $_GET['dtIni'] : null;
$dataFinal = preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $_GET['dtFim'] ) ? $_GET['dtFim'] : null;
if($dataInicial==null){
	$dataInicial = date("d/m/Y", strtotime( "previous monday" ) );
}
if($dataFinal==null){
	$dataFinal = date("d/m/Y");
}

if($dataInicial!=null){
	$md = explode("/", $dataInicial); // split the array
	$nd = $md[2]."-".$md[1]."-".$md[0]; // join them together
	$dtIni = date("Y-m-d", strtotime($nd));
}
if($dataFinal!=null){
	$md = explode("/", $dataFinal); // split the array
	$nd = $md[2]."-".$md[1]."-".$md[0]; // join them together
	$dtFim = date("Y-m-d", strtotime($nd));
}

$stdSub = getJSON( ($id_tag==null?'':'tags/'.$id_tag.'/').'tasks?opt_fields=id,name,completed,assignee,completed_at&workspace=10179896658126&assignee='.$id_user.'&completed_since='.$dtIni.'T00:00:00.000Z');
$rsSub = $stdSub->data;
//print_r($rsSub);
$totalhs = 0;
$i = 0;
?>
<div id="dv_tasks_<? echo $id_user; ?>" style="display: none;">
	<div id="dv_tree_<? echo $id_user; ?>">
	<ul>
	<?
	foreach($rsSub as $keySub => $valueSub){
				
		$stdTask = getJSON('tasks/'.$valueSub->id);
		$task = $stdTask->data;
		if($task->assignee->id == $id_user && 
				$task->completed ){
			$data = $task->completed_at;
			$data = explode('T', $data);
			$data = $data[0];
			
			if (($data >= $dtIni) && ($data <= $dtFim)){
				$nomeTask = $valueSub->name;
				$pattern = "/^\[(\d+([\.,]\d{1,2})?) ?h\]/";
				preg_match_all($pattern, $nomeTask, $matches);
				//print_r($matches);
				$hs = str_replace(",",".",$matches[1][0]);
					
				$totalhs += $hs;
				?>
				<li id="<? echo $valueSub->id; ?>"><? echo $nomeTask; ?></li>
				<?
				$i++;
			}
		}
		
	}
	?>
	</ul>
	</div>
</div>
<? 
$titulo = number_format($totalhs, 2, ',', ' ').' Hora'.($totalhs==0 || $totalhs>1?'s':'').', '.$i.' Task'.($i==0 || $i>1?'s':'');

?>
<table cellpadding="0" cellspacing="0">
	<tr>
		<td><? echo $titulo; ?></td>
		<? if($i>0){ ?>
			<td style="padding-left: 10px;"><a href="javascript:listarTasks('<? echo $titulo; ?>','<? echo $id_user; ?>');"><img alt="Listar Tasks" src="images/tasks.png"/></a></td>
		<? } ?>
	</tr>
</table>
<?

?>