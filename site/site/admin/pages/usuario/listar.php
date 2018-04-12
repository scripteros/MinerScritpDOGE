<?php

	if (basename($_SERVER["REQUEST_URI"]) === basename(__FILE__))
{
	exit('<h1>ERROR 404</h1>Entre em contato conosco e envie detalhes.');
}

?>
 <section class="content">
     <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header">
             <center> <h3 class="box-title">Contas de Usuários SSH e Revendedores</h3> </center>

             
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                    <th>Status</th>
                  <th>Nome</th>
                  <th>Login</th>
				  <th>Tipo</th>
				  <th>Contas SSH</th>
				  <th>Acessos SSH</th>
				  <th>Owner</th>
				  <th>Informações</th>
                </tr>
                
				
				
				  <?php

					$SQLUsuario = "select * from usuario ORDER BY ativo";
                    $SQLUsuario = $conn->prepare($SQLUsuario);
                    $SQLUsuario->execute();
                  
		

					// output data of each row
                   if (($SQLUsuario->rowCount()) > 0) {
                   
                   while($row = $SQLUsuario->fetch()) 
				    
				   
				   {
					  $status="";
					  $tipo="";
					  $owner = "";
					   $contas = 0;
					   $color = "";
					   
				    if($row['ativo']== 1){
						 $status="Ativo";
					}else{
						$status="Desativado";
					}  
					
					
					$SQLContasSSH = "select * from usuario_ssh WHERE id_usuario = '".$row['id_usuario']."'  ";
                    $SQLContasSSH = $conn->prepare($SQLContasSSH);
                    $SQLContasSSH->execute();
                    $contas += $SQLContasSSH->rowCount();
					
					$total_acesso_ssh = 0;
	                $SQLAcessoSSH = "SELECT sum(acesso) AS quantidade  FROM usuario_ssh where id_usuario='".$row['id_usuario']."' ";
                    $SQLAcessoSSH = $conn->prepare($SQLAcessoSSH);
                    $SQLAcessoSSH->execute();
	             	$SQLAcessoSSH = $SQLAcessoSSH->fetch();
                    $total_acesso_ssh += $SQLAcessoSSH['quantidade'];
		
		
					 if($row['ativo']!= 1){
						$color = "bgcolor='#FF6347'";
					} 	
					if($row['tipo']=="vpn"){
						$tipo="Usuário SSH";
						
					}else{
						$tipo="Revendedor";
						
						$SQLSub = "select * from usuario WHERE id_mestre = '".$row['id_usuario']."'  ";
                        $SQLSub = $conn->prepare($SQLSub);
                        $SQLSub->execute();
                    if (($SQLSub->rowCount()) > 0) {
    
                        while($rowS = $SQLSub->fetch()) {
							
							
							$SQLContasSSH = "select * from usuario_ssh WHERE id_usuario = '".$rowS['id_usuario']."'  ";
                            $SQLContasSSH = $conn->prepare($SQLContasSSH);
                            $SQLContasSSH->execute();
                            $contas += $SQLContasSSH->rowCount();
							
							$SQLAcessoSSH = "SELECT sum(acesso) AS quantidade  FROM usuario_ssh where id_usuario='".$rowS['id_usuario']."' ";
                            $SQLAcessoSSH = $conn->prepare($SQLAcessoSSH);
                            $SQLAcessoSSH->execute();
	             	        $SQLAcessoSSH = $SQLAcessoSSH->fetch();
                            $total_acesso_ssh += $SQLAcessoSSH['quantidade'];
							
						}
					}
						
						
					}
					
					if($row['id_mestre'] == 0){
						$owner = "Sistema";
					}else{

						$SQLRevendedor = "select * from usuario WHERE id_usuario = '".$row['id_mestre']."'  ";
                        $SQLRevendedor = $conn->prepare($SQLRevendedor);
                        $SQLRevendedor->execute();
					    $revendedor =  $SQLRevendedor->fetch();
						$owner = $revendedor['login'];
						
					}
					
					
					   ?>
				   
                  <tr <?php echo $color; ?> >
				   <td><?php echo $status;?></td>
                   <td><?php echo $row['nome'];?></td>
                  
                   <td><?php echo $row['login'];?></td>
                   
				   
					<td><?php echo $tipo;?></td>
					<td><?php echo $contas;?></td>
					<td><?php echo $total_acesso_ssh;?></td>
					
					<td><?php echo $owner;?></td>
                  
				   <td>
				     
					 
					   <a href="home.php?page=usuario/perfil&id_usuario=<?php echo $row['id_usuario'];?>" class="btn btn-primary">Visualizar</a>
					
				   
				   </td>
                  </tr>
				
				
	
	
   <?php }
}


?>
				
				
               
                
                
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
	  
	  
    
      <!-- /.row -->
    </section>
	
	
	
