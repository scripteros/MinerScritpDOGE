<?php

	if (basename($_SERVER["REQUEST_URI"]) === basename(__FILE__))
{
	exit('<h1>ERROR 404</h1>Entre em contato conosco e envie detalhes.');
}

?>
<?php
    $dias_acesso=0;

  if(isset($_GET["id_ssh"])){

	$diretorio="../../admin/home.php?page=ssh/editar&id_ssh=".$_GET['id_ssh'];


	$SQLUsuarioSSH = "select * from usuario_ssh WHERE id_usuario_ssh = '".$_GET['id_ssh']."' ";
    $SQLUsuarioSSH = $conn->prepare($SQLUsuarioSSH);
    $SQLUsuarioSSH->execute();


    $usuario_ssh = $SQLUsuarioSSH->fetch();

	if(($SQLUsuarioSSH->rowCount()) > 0){

		$SQLServidor = "select * from servidor WHERE id_servidor = '".$usuario_ssh['id_servidor']."'  ";
        $SQLServidor = $conn->prepare($SQLServidor);
        $SQLServidor->execute();
        $ssh_srv = $SQLServidor->fetch();

        //Calcula os dias restante
	    $data_atual = date("Y-m-d ");
		$data_validade = $usuario_ssh['data_validade'];
		if($data_validade > $data_atual){
		   $data1 = new DateTime( $data_validade );
           $data2 = new DateTime( $data_atual );
           $dias_acesso = 0;
           $diferenca = $data1->diff( $data2 );
           $ano = $diferenca->y * 364 ;
	       $mes = $diferenca->m * 30;
		   $dia = $diferenca->d;
           $dias_acesso = $ano + $mes + $dia;

		}else{
			 $dias_acesso = 0;
		}

		$SQLUsuario = "select * from usuario WHERE id_usuario = '".$usuario_ssh['id_usuario']."'  ";
        $SQLUsuario = $conn->prepare($SQLUsuario);
        $SQLUsuario->execute();


        $usuario_sistema = $SQLUsuario->fetch();

		$owner;

		if(!(($SQLUsuario->rowCount())  > 0)){

		    echo '<script type="text/javascript">';
			echo 	'alert("Nao encontrado!");';
			echo	'window.location="home.php?page=ssh/contas";';
			echo '</script>';
            exit;
	    }

	}else{
		    echo '<script type="text/javascript">';
			echo 	'alert("Nao encontrado!");';
			echo	'window.location="home.php?page=ssh/contas";';
			echo '</script>';
            exit;
	}


  }else{
	        echo '<script type="text/javascript">';
			echo 	'alert("Preencha todos os campos!");';
			echo	'window.location="home.php?page=ssh/contas";';
			echo '</script>';
			exit;

  }

	if($usuario_ssh['online'] >= 1){
		  $status= "<div class='alert alert-success alert-dismissible'>

                <h4><center>ONLINE</center></h4>
				<center><p>".$usuario_ssh['online']." conexão de ".$usuario_ssh['acesso']."</p></center>

              </div>";
	  }else{
		   $status= "<div class='alert alert-danger alert-dismissible'>

                <h4><center>OFFLINE</center></h4>

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

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Vencimento</b> <a class="pull-right"><?php echo $dias_acesso." dias"; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Servidor</b> <a class="pull-right"><?php echo $ssh_srv['nome'];?></a>
                </li>
                <li class="list-group-item">
                  <b>Login SSH</b> <a class="pull-right"><?php echo $usuario_ssh['login'];?></a>
                </li>
                <li class="list-group-item">
                  <b>Dono</b> <a href="home.php?page=usuario/perfil&id_usuario=<?php echo $usuario_sistema['id_usuario'];?>" class="pull-right"><?php echo $usuario_sistema['nome'];?></a>
                </li>
              </ul>
			   <form role="form2" action="../pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">
              <div class="box-footer">

					<input type="hidden"  id="diretorio" name="diretorio" value="../../admin/home.php?page=ssh/contas"  >
					<input type="hidden"  id="id_usuario_ssh" name="id_usuario_ssh" value="<?php echo $usuario_ssh['id_usuario_ssh']; ?>"  >
                    <input type="hidden"  id="owner" name="owner" value="<?php echo $accessKEY; ?>"  >



					 <center>
				<button type="submit" class="btn btn-danger" id="op" name="op" value="deletar" >Deletar conta SSH</button><br><br>
					<?php if($usuario_ssh['status']==2){?>
					<button type="submit" class="btn btn-success" id="op" name="op" value="ususpender" >Reativar conta</button><br><br>
					<?php }else{ ?>
						  <button type="submit" class="btn btn-warning" id="op" name="op" value="suspender" >Suspender conta</button><br><br>
					<?php } ?>
					<button type="submit" class="btn btn-default" id="op" name="op" value="kill" >Derrubar conta SSH</button><br><br>
					 </center>
					 </div>

		  </form>



            </div>

            <!-- /.box-body -->
          </div>
          <!-- /.box -->



        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#owner" data-toggle="tab">Alterar Owner</a></li>
			   <li><a href="#senha" data-toggle="tab">PassWord</a></li>
               <li><a href="#vencimento" data-toggle="tab">Vencimento</a></li>
              <li><a href="#hist" data-toggle="tab">Historico</a></li>
			   <li><a href="#acesso" data-toggle="tab">Quantidade de Acesso</a></li>
			   <li ><a href="#migrar" data-toggle="tab">Migrar Conta</a></li>


            </ul>
            <div class="tab-content">


			 <div class="active tab-pane" id="owner">
                <div >



            <form role="owner" action="../pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">
                <div class="form-group">
              <center>
                <select class="form-control select2" style="width: 70%;margin-left: 100px; margin-top: 12px; "  name="n_owner" id="n_owner">


				     <option selected="selected" value="<?php echo $usuario_sistema['id_usuario']; ?>"><?php echo $usuario_sistema['login']; ?></option>



				 <?php

	            $owner = $usuario_sistema['id_usuario'];

	 $SQLUsuario = "SELECT * FROM usuario ";
     $SQLUsuario = $conn->prepare($SQLUsuario);
     $SQLUsuario->execute();

if (($SQLUsuario->rowCount()) > 0) {
    // output data of each row
    while($row = $SQLUsuario->fetch()) {
		if($row['id_usuario'] != $usuario_sistema['id_usuario']){


		?>

	<option value="<?php echo $row['id_usuario'];?>" ><?php echo $row['login'];?></option>

   <?php }
	}
}

?>
                </select>
              </center>
			  </div>
              <!-- /.box-body -->
              <div class="box-footer">
                    <input type="hidden"  id="op" name="op" value="owner"  >

					<input type="hidden"  id="diretorio" name="diretorio" value="<?php echo $diretorio; ?>"  >

					<input type="hidden"  id="id_usuario_ssh" name="id_usuario_ssh" value="<?php echo $usuario_ssh['id_usuario_ssh']; ?>"  >

                    <input type="hidden"  id="owner" name="owner" value="<?php echo $accessKEY; ?>"  >

                <center><button type="submit" class="btn btn-primary">Alterar Owner da conta SSH</button> </center>
              </div>
              <!-- /.box-footer -->
            </form>
          </div>

              </div>

			   <div class="tab-pane" id="senha">
                 <div >

            <!-- /.box-header -->
            <!-- form start -->


            <form role="senha" id="senha" name="senha" action="../pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-2 control-label">Senha</label>

                  <div class="col-sm-10">
                    <input required="required" type="text" class="form-control" id="senha_ssh" name="senha_ssh" placeholder="Digite a nova senha">
                  </div>

				    <input type="hidden"  id="op" name="op" value="senha"  >
                    <input type="hidden"  id="id_ssh" name="id_ssh" value="<?php echo $_GET["id_ssh"]; ?>"  >
					<input type="hidden"  id="diretorio" name="diretorio" value="<?php echo $diretorio; ?>"  >
					<input type="hidden"  id="id_servidor" name="id_servidor" value="<?php echo $ssh_srv['id_servidor']; ?>"  >
					<input type="hidden"  id="id_usuario_ssh" name="id_usuario_ssh" value="<?php echo $usuario_ssh['id_usuario_ssh']; ?>"  >
                    <input type="hidden"  id="owner" name="owner" value="<?php echo $accessKEY; ?>"  >
                </div>


              </div>
              <!-- /.box-body -->
              <div class="box-footer">

                <center> <button type="submit" class="btn btn-primary">Alterar Senha</button> </center>
              </div>
              <!-- /.box-footer -->
            </form>

		  </div>



              </div>

			  <div class="tab-pane" id="vencimento">

            <div class="box-header with-border">



            <form role="form2" action="../pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Quantidade</label>

                  <div class="col-sm-10">
                    <input required="required" type="number" class="form-control" id="dias" name="dias" placeholder="Digite a quantidade dias de acesso" value="<?php echo $dias_acesso; ?>" >
                  </div>



                    <input type="hidden"  id="op" name="op" value="dias"  >
                    <input type="hidden"  id="id_usuarioSSH" name="id_usuarioSSH" value="<?php echo $_GET["id_ssh"]; ?>"  >
					<input type="hidden"  id="diretorio" name="diretorio" value="<?php echo $diretorio; ?>"  >

                    <input type="hidden"  id="owner" name="owner" value="<?php echo $accessKEY; ?>"  >

                </div>



              </div>
                      <center><button type="submit" class="btn btn-primary">Alterar dias de acesso</button> </center>

            </form>
          </div>

			 </div>



              <div class="tab-pane" id="hist">

				<div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Servidor</th>
                  <th>Inicio</th>
                  <th>Fim</th>
				  <th>DuraÃ§Ã£o</th>


                </tr>
				 <?php

    $SQLHistSSH = "select * from hist_usuario_ssh_online where id_usuario='".$usuario_ssh['id_usuario_ssh']."'  ";
    $SQLHistSSH = $conn->prepare($SQLHistSSH);
    $SQLHistSSH->execute();

    $SQLServidor = "select * from servidor WHERE id_servidor = '".$usuario_ssh['id_servidor']."'  ";
    $SQLServidor = $conn->prepare($SQLServidor);
    $SQLServidor->execute();
    $servidor = $SQLServidor->fetch();

if (($SQLHistSSH->rowCount()) > 0) {
    // output data of each row
    while($row_user = $SQLHistSSH->fetch()   ){

		   $fim_conexao = " UsuÃ¡rio Online " ;
		   $tempo_conectado = " ";

		   if($row_user['status']== 1){
			   $tempo_conectado =  tempo_corrido($row_user['hora_conexao']);
		   }else if($row_user['status'] != 1){
			   $fim_conexao = $row_user['hora_desconexao'];
			    $tempo_conectado =  tempo_final($row_user['hora_conexao'],$fim_conexao);

		   }





		 ?>


	          <tr >
                  <td><?php echo $servidor['nome'];?></td>
                  <td><?php echo $row_user['hora_conexao'];?></td>
                  <td><?php echo $fim_conexao;?></td>
				  <td><?php echo $tempo_conectado;?></td>


                </tr>


	<?php

	}
}
?>




              </table>
            </div>
			  </div>


              <div class="tab-pane" id="acesso">
			   <div >
              <div class="box-header with-border">



            <form role="form2" action="../pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Quantidade</label>

                  <div class="col-sm-10">
                    <input required="required" type="number" class="form-control" id="acesso" name="acesso" placeholder="Digite a quantidade de acesso" value="<?php echo $usuario_ssh['acesso']; ?>">
                  </div>



                    <input type="hidden"  id="op" name="op" value="acesso"  >
					<input type="hidden"  id="diretorio" name="diretorio" value="<?php echo $diretorio; ?>"  >
					<input type="hidden"  id="id_usuario_ssh" name="id_usuario_ssh" value="<?php echo $usuario_ssh['id_usuario_ssh']; ?>"  >
                    <input type="hidden"  id="sistema" name="sistema" value="<?php echo $owner; ?>"  >
				    <input type="hidden"  id="owner" name="owner" value="<?php echo $accessKEY; ?>"  >
                </div>


              </div>
              <!-- /.box-body -->
              <div class="box-footer">

                <center><button type="submit" class="btn btn-primary">Alterar conexÃ£o simultÃ¢nea</button> </center>
              </div>
              <!-- /.box-footer -->
            </form>
          </div>


			 </div>
			 </div>

			<div class="tab-pane" id="migrar">
                <div class="box-header with-border">



            <form role="migrar" action="../pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">


			  <div class="form-group">
                  <label for="exampleInputEmail1">Servidor Atual</label>
				  <?php
				    $SQLServidor = "select * from servidor WHERE id_servidor = '".$ssh_srv['id_servidor']."' ";
       $SQLServidor = $conn->prepare($SQLServidor);
       $SQLServidor->execute();
       $servidor = $SQLServidor->fetch();


		$SQLContasSSH = "SELECT sum(acesso) AS quantidade  FROM usuario_ssh where id_servidor = '".$ssh_srv['id_servidor']."'  ";
        $SQLContasSSH = $conn->prepare($SQLContasSSH);
        $SQLContasSSH->execute();
		$SQLContasSSH = $SQLContasSSH->fetch();
        $contas_ssh_criadas += $SQLContasSSH['quantidade'];
				  ?>
                  <input required="required" type="text" class="form-control"  value=" <?php echo $ssh_srv['nome'];?> - <?php echo $ssh_srv['ip_servidor'];?> -  <?php echo $contas_ssh_criadas;?> Conexões" >

				</div>
				<div class="form-group">
                <label>Selecione um servidor destino</label>
                <select class="form-control select2" style="width: 100%;"  name="id_new_servidor" id="id_new_servidor">

                  <?php



	                $SQLAcesso= "select * from servidor where id_servidor != '".$ssh_srv['id_servidor']."' ";
                    $SQLAcesso = $conn->prepare($SQLAcesso);
                    $SQLAcesso->execute();


if (($SQLAcesso->rowCount()) > 0) {
    // output data of each row
    while($row_srv = $SQLAcesso->fetch()) {
		$contas_ssh_criadas = 0;

       $SQLServidor = "select * from servidor WHERE id_servidor = '".$row_srv['id_servidor']."' ";
       $SQLServidor = $conn->prepare($SQLServidor);
       $SQLServidor->execute();
       $servidor = $SQLServidor->fetch();


		$SQLContasSSH = "SELECT sum(acesso) AS quantidade  FROM usuario_ssh where id_servidor = '".$row_srv['id_servidor']."'  ";
        $SQLContasSSH = $conn->prepare($SQLContasSSH);
        $SQLContasSSH->execute();
		$SQLContasSSH = $SQLContasSSH->fetch();
        $contas_ssh_criadas += $SQLContasSSH['quantidade'];








		?>

	<option value="<?php echo $row_srv['id_servidor'];?>" > <?php echo $servidor['nome'];?> - <?php echo $servidor['ip_servidor'];?> -  <?php echo $contas_ssh_criadas;?>  Conexões </option>

   <?php }
}

?>


                </select>
              </div>



              <!-- /.box-body -->
              <div class="box-footer">
                    <input type="hidden"  id="op" name="op" value="migrar"  >

					<input type="hidden"  id="diretorio" name="diretorio" value="<?php echo $diretorio; ?>"  >

					<input type="hidden"  id="id_ssh" name="id_ssh" value="<?php echo $usuario_ssh['id_usuario_ssh']; ?>"  >

                    <input type="hidden"  id="owner" name="owner" value="<?php echo $accessKEY; ?>"  >

                <center><button type="submit" class="btn btn-primary">Mudar de Servidor</button> </center>
              </div>
              <!-- /.box-footer -->
            </form>
          </div>

              </div>


              <!-- /.tab-pane -->
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