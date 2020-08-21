<?php 
 
	 class DbConnect {
	 	private $con ;  
	 	function  __construct ()
	 	{

	 	} 
	 	function connect ()
	 	{
	 		require_once dirname(__FILE__).'/Constants.php' ;
	 		$this->con =  new mysqli (DB_HOST , DB_USER , DB_PASSWORD , DB_NAME ) ; 

	 		if (mysqli_connect_errno () ) {
	 		 	echo "Echec de connexion a la base de donnees".mysqli_connect_err();
	 		 } 
	 		 return $this->con ;

	 	}

	 }
?>


