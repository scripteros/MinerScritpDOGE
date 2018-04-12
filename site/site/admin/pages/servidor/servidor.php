<?php


	if (basename($_SERVER["REQUEST_URI"]) === basename(__FILE__))
{
	exit('<h1>ERROR 404</h1>Entre em contato conosco e envie detalhes.');
}

   if(isset($_GET["id_servidor"])){
	    $SQLServidor = "select * from servidor WHERE id_servidor = '".$_GET['id_servidor']."' ";
        $SQLServidor = $conn->prepare($SQLServidor);
        $SQLServidor->execute();
		$servidor = $SQLServidor->fetch();
	   if(($SQLServidor->rowCount()) == 0 ){
		    echo '<script type="text/javascript">';
		echo 	'alert("Nao encontrado!");';
		echo	'window.location="home.php?page=servidor/listar";';
		echo '</script>';
		exit;

	   }
   }else{
	    echo '<script type="text/javascript">';
		echo 	'alert("Preencha todos os campos!");';
		echo	'window.location="home.php?page=servidor/listar";';
		echo '</script>';
        exit;
   }


           //Realiza a comunicacao com o servidor
			$ip_servidor= $servidor['ip_servidor'];
		    $loginSSH= $servidor['login_server'];
			$senhaSSH=  $servidor['senha'];
			$ssh = new SSH2($ip_servidor);



		   //Verifica se o servidor esta online
		   $servidor_online = $ssh->online($ip_servidor);
           if ($servidor_online) {
			   $servidor_autenticado = $ssh->auth($loginSSH,$senhaSSH);
			   if($servidor_autenticado){
				$status= "<div class='alert alert-success alert-dismissible'>

                <h4><center>Autenticado</center></h4>

              </div>";
				//Verifica memoria
			 $ssh->exec("free");
			 $mensagem = (string) $ssh->output();
             $words = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[\s,]+/",
			                         $mensagem, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			//Memoria total $words[7]
			//Memoria usada $words[8]
			//Memoria livre $words[9]

			//Quantidade de CPU fisico
			$ssh->exec("cat /proc/cpuinfo | grep 'physical id' | sort | uniq | wc -l ");
			$mensagem_f = (string) $ssh->output();
			$cpu_fisico = $mensagem_f;

			//Quantidade de CPU Virtual
			$ssh->exec("cat /proc/cpuinfo | egrep 'core id|physical id' | tr -d '\n' | sed s/physical/\\nphysical/g | grep -v ^$ | sort | uniq | wc -l");
			$mensagem_v = (string) $ssh->output();
		    $cpu_virtual = $mensagem_v;

			//Nome do Processador
			$ssh->exec("cat /proc/cpuinfo | egrep ' model name|model name'");
			$mensagem_p = (string) $ssh->output();
		    $partes = explode(":", $mensagem_p);
			$nome_processador= $partes[1];

			//UPTIME
			$ssh->exec("uptime");
			$mensagem_u = (string) $ssh->output();
			$uptime = $mensagem_u;

			if($servidor['tipo']<>'free'){
			//Usuarios SSH online neste servidor
			$SQLContasSSH = "SELECT sum(online) AS soma  FROM usuario_ssh where id_servidor = '".$_GET['id_servidor']."'   ";
            $SQLContasSSH = $conn->prepare($SQLContasSSH);
            $SQLContasSSH->execute();
		    $SQLContasSSH = $SQLContasSSH->fetch();
            $usuarios_online = $SQLContasSSH['soma'];
            }

			}else{
				$status= "<div class='alert alert-warning alert-dismissible'>

                <h4><center>Não Autenticado </center></h4>

              </div>";
			}




            }else{
				$status= "<div class='alert alert-danger alert-dismissible'>

                <h4><center>OFFLINE </center></h4>

              </div>";
			}









?>
 <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">


			  <?php echo $status; ?>
             <?php if($servidor_autenticado){?>
              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b><?php echo $nome_processador;?></b>
                </li>
                <li class="list-group-item">
                  <b>CPU física</b> <a class="pull-right"><?php echo $cpu_fisico; ?></a>
                </li>
                <li class="list-group-item">
                  <b>CPU Virtual</b> <a class="pull-right"><?php echo $cpu_virtual; ?></a>
                </li>
				<li class="list-group-item">
                  <b>Memoria total</b> <a class="pull-right"><?php echo $words[7]; ?> Kb</a>
                </li>
				<li class="list-group-item">
                  <b>Memoria usada</b> <a class="pull-right"><?php echo $words[8]; ?> Kb</a>
                </li>
				<li class="list-group-item">
                  <b>Memoria livre</b> <a class="pull-right"><?php echo $words[9]; ?> Kb</a>
                </li>
				<li class="list-group-item">
				<?php if($servidor['tipo']<>'free'){ ?>
                  <b>Usuários Online</b> <a class="pull-right"><?php echo $usuarios_online; ?> </a>
                  <?php }else{ ?>
                         <center><h4>Servidor Free</h4></center>
                  <?php } ?>
                </li>
              </ul>
			 <?php }?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
           <?php if($servidor_autenticado){?>
           <script type="text/javascript">
function updatescripts(){
decisao = confirm("Tem certeza que vai atualizar? , os usuarios serão resetados!");
if (decisao){
   window.location.href='../admin/pages/servidor/servidor_exe.php?id_servidor=<?php echo $servidor['id_servidor'];?>&op=updateScript'
} else {

}


}
</script>
          <div class="box box-warning">
		    <div class="box-header with-border">
            <center>  <h3 class="box-title">Ações</h3></center>
            </div> <br>
             <center>  <a href="../admin/pages/servidor/servidor_exe.php?id_servidor=<?php echo $servidor['id_servidor'];?>&op=reiniciar" class="btn btn-warning">	Reiniciar Servidor</a><br><br>
			  <a href="../admin/pages/servidor/servidor_exe.php?id_servidor=<?php echo $servidor['id_servidor'];?>&op=desligar" class="btn btn-danger">	Desligar Servidor</a><br><br>
			  <a href="../admin/pages/servidor/servidor_exe.php?id_servidor=<?php echo $servidor['id_servidor'];?>&op=reiniciarSquid" class="btn btn-primary">	Reiniciar Squid</a><br><br>
			  <a onclick="updatescripts()" class="btn btn-success">	Update Scripts</a><br><br>
			  </center>

            </div>
            <?php } ?>

        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab">Informações</a></li>
              <li><a href="#timeline" data-toggle="tab">Contas SSH</a></li>
               <li><a href="#ovpn" data-toggle="tab">Arquivo OVPN</a></li>

            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="activity">

                   <form role="form" action="pages/servidor/editar_exe.php" method="post" enctype="multipart/form-data" >
              <div class="box-body">
			     <input type="hidden" class="form-control" id="id_servidor" name="id_servidor" value="<?php echo $servidor['id_servidor'];?>">
				<div class="form-group">
                  <label for="exampleInputEmail1">Nome do servidor</label>
                  <input required="required" type="text" class="form-control" id="nomesrv" name="nomesrv" value="<?php echo $servidor['nome'];?>">
                </div>

				<div class="form-group">
                  <label for="exampleInputEmail1">Endereço IP</label>
                  <input required="required" type="text" class="form-control" id="ip" name="ip" value="<?php echo $servidor['ip_servidor'];?>">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Login</label>
                  <input required="required" type="text" class="form-control" id="login" name="login" value="<?php echo $servidor['login_server'];?>">
                </div>
				 <div class="form-group">
                  <label for="exampleInputPassword1">Senha</label>
                  <input required="required" type="password" class="form-control" id="login" name="senha" value="<?php echo $servidor['senha'];?>">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Site Servidor</label>
                  <input required="required" type="text" class="form-control" id="siteserver" name="siteserver" value="<?php echo $servidor['site_servidor'];?>" placeholder="Só precisa em Server Free">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Localização</label>
                  <input required="required" type="text" class="form-control" id="localiza" name="localiza" value="<?php echo $servidor['localizacao'];?>" placeholder="Só precisa em Server Free">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Icone</label>
                  <input required="required" type="text" class="form-control" id="localiza_ico" name="localiza_ico" value="<?php echo $servidor['localizacao_img'];?>" placeholder="Só precisa em Server Free">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Validade</label>
                  <input required="required" type="text" maxlength="2" class="form-control" id="validade" name="validade" value="<?php echo $servidor['validade'];?>" placeholder="Só precisa em Server Free">
                </div>
                 <div class="form-group">
                  <label for="exampleInputPassword1">Limite</label>
                  <input required="required" type="text" maxlength="3" class="form-control" id="limite" name="limite" value="<?php echo $servidor['limite'];?>" placeholder="Só precisa em Server Free">
                </div>




              </div>
              <!-- /.box-body -->


              <div class="box-footer box-primary">
              <div class="box-header with-border">
            <center>  <h3 class="box-title"> + Opções </h3></center>
            </div>  <br>
                <button type="submit" class="btn btn-primary">Alterar Servidor</button>

				 <a href="../admin/pages/servidor/servidor_exe.php?id_servidor=<?php echo $servidor['id_servidor'];?>&op=deletarContas" class="btn btn-warning">	Deletar Contas</a><br><br>
                 <a href="../admin/pages/servidor/servidor_exe.php?id_servidor=<?php echo $servidor['id_servidor'];?>&op=deletarGeral" class="btn btn-danger">	Deletar TUDO</a>
                 <?php if($servidor['manutencao']=='nao'){ ?>
                 <a href="../admin/pages/servidor/servidor_exe.php?id_servidor=<?php echo $servidor['id_servidor'];?>&op=manutencao" class="btn btn-danger">  Por MANUTENÇÃO</a><br><br>
			     <?php }else{ ?>
			     <a href="../admin/pages/servidor/servidor_exe.php?id_servidor=<?php echo $servidor['id_servidor'];?>&op=manutencao" class="btn btn-danger">  TIRAR MANUTENÇÃO</a><br><br>
			     <?php } ?>
			 </div>


            </form>

			</div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="timeline">

				  <table class="table table-hover">
                <tr>
                  <th>Login SSH</th>
                  <th>Vencimento</th>
				  <th>Online</th>
                  <th>Acesso</th>



                </tr>
				 <?php

    if($servidor['tipo']=='free'){
    $SQLUsuarioSSH = "select * from usuario_ssh_free where servidor='".$servidor['id_servidor']."'  ";
    }else{    $SQLUsuarioSSH = "select * from usuario_ssh where id_servidor='".$servidor['id_servidor']."'  ";    }
    $SQLUsuarioSSH = $conn->prepare($SQLUsuarioSSH);
    $SQLUsuarioSSH->execute();


if (($SQLUsuarioSSH->rowCount()) > 0) {


    while($row2 = $SQLUsuarioSSH->fetch()   ){
         if($servidor['tipo']<>'free'){
		   $SQLTotalUser = "select * from usuario_ssh WHERE id_servidor='".$servidor['id_servidor']."' ";
           $SQLTotalUser = $conn->prepare($SQLTotalUser);
           $SQLTotalUser->execute();
	       $total_user = $SQLTotalUser->rowCount();


		 $SQLAcessoSSH = "SELECT sum(acesso) AS quantidade  FROM usuario_ssh where id_servidor = '".$row2['id_servidor']."'  and id_usuario='".$_GET['id_usuario']."' ";
         $SQLAcessoSSH = $conn->prepare($SQLAcessoSSH);
         $SQLAcessoSSH->execute();
	     $SQLAcessoSSH = $SQLAcessoSSH->fetch();
         $acessos += $SQLAcessoSSH['quantidade'];
		 }
		 $data_atual = date("Y-m-d ");
		$data_validade = $row2['validade'];
		if($data_validade > $data_atual){
		   $data1 = new DateTime( $data_validade );
           $data2 = new DateTime( $data_atual );
           $dias_acesso = 0;
           $diferenca = $data1->diff( $data2 );
           $ano = $diferenca->y * 364 ;
	       $mes = $diferenca->m * 30;
		   $dia = $diferenca->d;
           $dias_acesso = $ano + $mes + $dia;

		}


		 ?>


	          <tr>
                  <td><?php echo $row2['login'];?></td>
                  <td>  <span class="pull-left-container" style="margin-right: 5px;">
                            <span class="label label-primary pull-left">
					            <?php echo $dias_acesso."  dias   "; ?>
				            </span>
                       </span> </td>
                  <td><?php if($servidor['tipo']=='premium'){ echo $row2['online']; } else{ echo "<small>Server Free</small>";}?></td>
				<td><?php if($servidor['tipo']=='premium'){echo $row2['acesso'];}else{ echo "<small>Server Free</small>";}?></td>


                </tr>


	<?php


	}
}
?>




              </table>


			  </div>
        <?php
         $SQLovpn = "select * from ovpn WHERE servidor_id = '".$_GET["id_servidor"]."' ";
        $SQLovpn = $conn->prepare($SQLovpn);
        $SQLovpn->execute();

      ?>

     <div class="tab-pane" id="ovpn">



        <div class="box-body">
        <?php if($SQLovpn->rowCount()==0){ ?>
        <form role="form" action="pages/servidor/enviar_ovpn.php" method="post" enctype="multipart/form-data" >
                 <input name="servidorid" type="hidden" value="<?php echo $servidor['id_servidor'];?>">
         <div class="form-group">
                  <label for="exampleInputFile">Escolha o arquivo</label>
                  <input type="file" id="arquivo" name="arquivo" required=required>

                  <p class="help-block">.OVPN Tamanho Máximo 2MB.</p>
                  <?php }else{ ?>
                   <div class="box box-solid box-success">
              <div class="box-header">
                <h3 class="box-title">Arquivo Enviado!</h3>
              </div><!-- /.box-header -->
              <div class="box-body">
                Já possui um arquivo <b>OVPN</b> instalado neste Servidor
              </div><!-- /.box-body -->
            </div>

            <?php }?>

              <div class="box-footer box-primary">
              <div class="box-header with-border">
              <?php if($SQLovpn->rowCount()==0){ ?>  <button type="submit" class="btn btn-primary">Enviar OVPN</button></form><?php } ?> <a href="../admin/pages/servidor/deletar_ovpn.php?id_servidor=<?php echo $servidor['id_servidor'];?>" class="btn btn-danger">Deletar OVPN</a>
                </div>
                </div>
                </div>

        </div>
      </div>
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->