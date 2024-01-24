
<html>
	 <style>
		table,th, td {
		  border: 0.2px solid black;
		  padding: 0px;
		  margin-top: -13.1px;
		  margin-bottom: -2px;
		  margin-right: -2px;
		  margin-left: -2px;
		  height:5vh;
		}
		table {
		  border-spacing: 0px;
		}
		#Table1 {
		  border-collapse: collapse;
		  margin-top: -20px;
		  padding: 12px 20px 12px 40px;
		  height:10.5vh;
		}
		#form {
			width:10%;
			font-size:18px;
			text-align:right;
			margin-top: 20.0px;
			margin-left: 1200px;
		}
		#Input {
		  width: 50%;
		  font-size: 16px;
		  margin-top: -5px;
		  padding: 6px 10px 6px 20px;
		  border: 1px solid #ddd;
		  margin-bottom: 10px;
		}
		#submit{
			margin-top:-10px;
		}
	</style>
	<body>

	 
	 <!--<form class="form" method="get" > 
		 <div class="row">
			<input type="text" id="Input" name="token" placeholder="ENTER TOKEN" title="Token please">
			<input type="hidden" id="Input" name="cname">
			<input type="submit" name="submit" value="Submit Token">
		</div>
	</form>-->

<?php 
// $token= 'WO12345';
// if(isset($_GET['token'])){
	
// if($_GET['token'] == $token)
// {
// if(isset($_GET['submit']))
	 // {
include( 'conn.php' );
	
	// $p = $_POST['cname'] ;
		
$record = $models->execute_kw($db, $uid, $password,
			'simrp.saleorder', 'sostatus',
			array(array()) 
			);
			// print_r($record);
				
// $record2 = $models->execute_kw($db2, $uid2, $password2,
// 			'simrp.saleorder', 'sostatus',
// 			array(array()) 
// 			);
// 			// print_r($record2);
$getwostatus= json_decode($record, True);
// print_r($getwostatus);
// $wostatus= json_decode($record2, True);
	
				$d = date("M/d/Y h:i:s a ", strtotime('+270 minutes'));
				$keys = array_keys($getwostatus);
				
								echo '<table style="width:100%" id="Table1">';
								echo '<tr style="background-color: #000000;text-align:center;font-family: Arial;color:F5DFDF;height:6.5vh;margin-top:0px">'; //#C5C6C8//
									echo '<th style="width:15%;font-size:15px;text-align:top">' . $d . '</th>';
									echo '<th style="width:85%;font-size:25px;margin-bottom: 2px"> SO Dashboard </th>';
									// echo '<th>
										  // <div>	
											// <form method="post" style="width:10%;font-size:18px;text-align:right;margin-top: 1vh"> 
												// <input type="text"  name="cname" placeholder="Search.. " title="Customer name please">
												
                                          // </div></th>';
									// echo '<th >
										  // <div>											
											// <input type="submit" name="submit" id="submit" value="Submit">
											// </form>
                                          // </div></th>';
									
									echo '</table>';
								echo '</tr>';
								echo '<table style="width:100%"id="myTable">';
								
								
								echo '<tr style="background-color: #21201F;text-align:center;font-family:Arial;font-size: Bold 5px;height:2%;color:FCF6ED">'; //#C5C6C8//
									 echo '<th style="font: Bold 2vh Arial">SO no</th>';
									 echo '<th style="width:25%;font: Bold 2vh Arial">Customer</th>';
									 echo '<th style="font: Bold 2vh Arial">PO no</th>';
									 // echo '<th style="width:10%;font: Bold 2vh Arial">Created On</th>';
									 echo '<th style="width:30%; font: Bold 2vh Arial">Item</th>';
									 echo '<th style="font: Bold 2vh Arial">Po Qty</th>';
									 echo '<th style="font: Bold 2vh Arial">Wo Qty</th>';
									 echo '<th style="font: Bold 2vh Arial">Bal Qty</th>';
									 echo '<th style="font: Bold 2vh Arial">Bal Value</th>';
								echo '</tr>';
							foreach ($getwostatus as $key) 
							{
											echo '<tr style="background-color:#E0FCFF; font-family:Arial;font-size:10px ;height:1%;text-align:center ">'; //#EBEEEF//
												echo '<td style="font: Bold 2vh Arial">' . $key[1]['Sono']. '</td>';
												echo '<td style="font: Bold 2vh Arial">' . $key[1]['Cust']. '</td>';
												echo '<td style="font: Bold 2vh Arial">' . $key[1]['Pono']. '</td>';
												echo '<td style="font: Bold 2vh Arial">' . $key[1]['Item']. '</td>';
												echo '<td style="font: Bold 2vh Arial">' . $key[1]['pqty']. '</td>';
												echo '<td style="font: Bold 2vh Arial">' . $key[1]['Wqty']. '</td>';
												echo '<td style="font: Bold 2vh Arial">' . $key[1]['Bqty']. '</td>';
												echo '<td style="font: Bold 2vh Arial">' . $key[1]['Bvalue']. '</td>';
										    echo '</tr>';
							}
							// foreach ($wostatus as $key) 
							// {
							// 				echo '<tr style="background-color:#E0FCFF; font-family:Arial;font-size:10px ;height:1%;text-align:center ">'; //#EBEEEF//
							// 					echo '<td style="font: Bold 2vh Arial">' . $key[1]['Sono']. '</td>';
							// 					echo '<td style="font: Bold 2vh Arial">' . $key[1]['Cust']. '</td>';
							// 					echo '<td style="font: Bold 2vh Arial">' . $key[1]['Pono']. '</td>';
							// 					echo '<td style="font: Bold 2vh Arial">' . $key[1]['Item']. '</td>';
							// 					echo '<td style="font: Bold 2vh Arial">' . $key[1]['pqty']. '</td>';
							// 					echo '<td style="font: Bold 2vh Arial">' . $key[1]['Wqty']. '</td>';
							// 					echo '<td style="font: Bold 2vh Arial">' . $key[1]['Bqty']. '</td>';
							// 					echo '<td style="font: Bold 2vh Arial">' . $key[1]['Bvalue']. '</td>';
							// 			    echo '</tr>';
							// }
															
							// foreach ($wostatus as $key) 
							// {
											// echo '<tr style="background-color:#E0FCFF; font-family:Arial;font-size:10px ;height:1%;text-align:center ">'; //#EBEEEF//
												// echo '<td style="font: Bold 2vh Arial">' . $key['Wono']. '</td>';
												// echo '<td style="border: 0.5px solid black;width:19.7vw;background-color:#59C74C;height:5vh"><div style="position:relative; top:0vh; left:0vw">
													 // <div style="position:absolute; top: -2.7vh; left: 0.1vw; font: Bold 1.8vh Arial">' . $key['customer'].'</div>
													 // <div style="position:absolute; top: -0.8vh; left: 1vw; width:18.5vw;  text-align:right; font:  1.4vh Arial">' . $key['partno']. '</div>
													 // </div></td>';
												// echo '<td style="width:2%;font: Bold 2vh Arial">' . $key['Woqty'].'</td>';
												// echo '<td style="border: 0.5px solid black;width:6vw;background-color:#F7E0FF;height:5vh"><div style="position:relative; top:0vh; left:0vw">
													 // <div style="position:absolute; top: -2.5vh; left: 0.1vw; font: 1.4vh Arial">' . $key["linkedsono"] . '</div>
													 // <div style="position:absolute; top: -1vh; left: 1vw; width:4.3vw;  text-align:right; font: Bold 3vh Arial">' . $key["Balanceqty"]. '</div>
													 // <div style="position:absolute; top: -1.2vh; left: 0vw; font: Bold 1.8vh Arial">' . $key["Soqty"]. '</div>
													 // <div style="position:absolute; top: -2.5vh; left: 2vw; width:3.5vw;  text-align:right; font: Bold 1.5vh Arial">' . $key["Dispqty"]. '</div>
													 // </div></td>';
												// echo '<td style="background-color:#FCF6ED">' . $key['Woprogresshtml'].'</td>';
										    // echo '</tr>';
							// }
								
							echo '</table>';
						
						
		// }
	// }
	// if($_GET['token'] != $token){
		// echo "INVALID TOKEN...";
		// }
// }		
			?>
		
</body>				
</html>
