<?php
	//Definindo as credenciais
	$email = "email_pagseguro";
	$token = "token_pagseguro";
	$token_teste = "token_pagseguro_teste";
	$ambiente_teste = 0;// 1 - Ambiente de teste
							
	//URL da chamada para o PagSeguro Produção
	$url = "https://ws.pagseguro.uol.com.br/v2/checkout/?email=".$email."&token=".$token;
	//URL da chamada para o PagSeguro Teste
	if ($ambiente_teste==1){
		$url = "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout/?email=".$email."&token=".$token_teste;
	}
	
	//Dados da compra
	$dadosCompra['currency'] = 'BRL';
	$dadosCompra['itemId1'] = '0001';
	$dadosCompra['itemDescription1'] = 'Notebook Prata';
	$dadosCompra['itemAmount1'] = '24300.00';
	$dadosCompra['itemQuantity1'] = '1';
	$dadosCompra['itemWeight1'] = '1000';
	$dadosCompra['itemId2'] = '0002';
	$dadosCompra['itemDescription2'] = 'Notebook Rosa';
	$dadosCompra['itemAmount2'] = '25600.00';
	$dadosCompra['itemQuantity2'] = '2';
	$dadosCompra['itemWeight2'] = '750';
	$dadosCompra['reference'] = 'REF1234';
	$dadosCompra['senderName'] = 'Jose Comprador';
	$dadosCompra['senderAreaCode'] = '11';
	$dadosCompra['senderPhone'] = '56273440';
	$dadosCompra['senderEmail'] = 'comprador@uol.com.br';
	$dadosCompra['shippingType'] = '1';
	$dadosCompra['shippingAddressStreet'] = 'Av. Brig. Faria Lima';
	$dadosCompra['shippingAddressNumber'] = '1384';
	$dadosCompra['shippingAddressComplement'] = '5o andar';
	$dadosCompra['shippingAddressDistrict'] = 'Jardim Paulistano';
	$dadosCompra['shippingAddressPostalCode'] = '01452002';
	$dadosCompra['shippingAddressCity'] = 'Sao Paulo';
	$dadosCompra['shippingAddressState'] = 'SP';
	$dadosCompra['shippingAddressCountry'] = 'BRA';
	$dadosCompra['redirectURL'] = 'http://www.sounoob.com.br/paginaDeAgracedimento';

	//Transformando os dados da compra no formato da URL
	$dadosCompra = http_build_query($dadosCompra);

	//Realizando a chamada
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $dadosCompra);
	$respostaPagSeguro = curl_exec($curl);
	$http = curl_getinfo($curl);

	if($http['http_code'] != "200"){
	//Criando um log de erro.
		$data = date("Y_m_d");
		$hora = date("H:i:s T");
		$arquivo = fopen("LogErroPagSeguro.$data.txt", "ab");
		fwrite($arquivo,"Log de erro\\\\r\\\\n");
		fwrite($arquivo,"HTTP: ".$http['http_code']." \\\\r\\\\n");
		fwrite($arquivo,"Data: ".$data." \\\\r\\\\n");
		fwrite($arquivo,"Hora: ".$hora." \\\\r\\\\n");
		if($http['http_code'] == "401"){
			echo $http['http_code'];
			fwrite($arquivo,"Erro:".$respostaPagSeguro." \\\\r\\\\n");
			fwrite($arquivo,"Esta mensagem de erro é ocasionada quando as credenciais (e-mail e token) da chamada estão erradas.\\\\r\\\\n");
		}

		else{
			curl_close($curl);
			$respostaPagSeguro= simplexml_load_string($respostaPagSeguro);

			foreach ($respostaPagSeguro->error as $key => $erro) {
			fwrite($arquivo,"-----------------------------------------------------------------------------------------------------------\\\\r\\\\n");
			fwrite($arquivo,"Código do erro: ".$erro->code." \\\\r\\\\n");
			fwrite($arquivo,"Mensagem: ".$erro->message." \\\\r\\\\n");
			fwrite($arquivo,"-----------------------------------------------------------------------------------------------------------\\\\r\\\\n");
			}
			fwrite($arquivo,"Neste caso, você precisa verificar se os dados foram passados de acordo com a documentação do PagSeguro.\\\\r\\\\n");
		}
		fwrite($arquivo,"________________________________________________________________________________________________________________ \\\\r\\\\n");
		fclose($arquivo);
	}
	$respostaPagSeguro= simplexml_load_string($respostaPagSeguro);

?>