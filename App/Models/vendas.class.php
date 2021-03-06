<?php

/**
* Vendas
*/

require_once 'connect.php';

class Vendas extends Connect
{


	public function itensVendidos($iditem, $quant, $cliente, $email, $cpfcliente, $idUsuario, $perm)
	{

    	
        if($perm != 2){
          echo "Você não tem permissão!";
          exit();
        }

        $this->query = "SELECT * FROM `itens` WHERE `idItens`= '$iditem'";
        $this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL));

        if($this->result){

        		//------Verificação da Venda-----------

        		if($row = mysqli_fetch_array($this->result)){

        			$q = $row['QuantItens'];
        			$v = $row['QuantItensVend'];

        			$quantotal = $v + $quant;

        			if($q >= $quantotal){

                        $valor = ($row['ValVendItens'] * $quant);

                        $id = Vendas::idCliente($cpfcliente);

                        if($id > 0){
                            $idCliente = $id;
                        }else{

                            $this->novoclient = "INSERT INTO `cliente`(`idCliente`, `NomeCliente`, `EmailCliente`, `cpfCliente`, `statusCliente`, `Usuario_idUsuario`) VALUES (NULL,'$cliente','$email','$cpfcliente',1,'$idUsuario')";

                               if(mysqli_query($this->SQL, $this->novoclient) or die (mysqli_error($this->SQL))){
                                $idCliente = mysqli_insert_id($this->SQL);
                             }                            
                        }
                        
                        
                        $this->query = "INSERT INTO `vendas`(`idvendas`, `quantitens`, `valor`, `iditem`, `cliente_idCliente`, `idusuario`) VALUES (NULL, '$quant', '$valor', '$iditem', '$idCliente', '$idUsuario')";
                        if($this->result = mysqli_query($this->SQL, $this->query) or die (mysqli_error($this->SQL))){


        				$this->query = "UPDATE `itens` SET `QuantItensVend` = '$quantotal' WHERE `idItens`= '$iditem'";
        				if($this->result = mysqli_query($this->SQL, $this->query) or die (mysqli_error($this->SQL))){

        					echo 'Venda efetuada!';
                        }

        				}else{
        					echo 'Não foi possivel efetuar a venda!';
        				}

        			}else{

        				$estoque = $row['QuantItens'] - $row['QuantItensVend'];
        				echo 'Quantidade maior do que em estoque! </br> Quantidade em estoque disponivel: '.$estoque;
        			}


        		}


        		//------------------

        }else{
        	header('Location: ../../views/vendas/index.php?alert=0');
        }


	}// itensVendidos

    public function idcliente($cpfCliente){

        $this->client = "SELECT * FROM `cliente` WHERE `cpfCliente` = '$cpfCliente'";

            if($this->resultcliente = mysqli_query($this->SQL, $this->client)  or die (mysqli_error($this->SQL))){

                $row = mysqli_fetch_array($this->resultcliente);
                return $idCliente = $row['idCliente'];
            }
    }

}//Class