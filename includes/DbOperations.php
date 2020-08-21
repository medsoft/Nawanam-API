<?php 
 class DbOperations{
         private $con; 
         function __construct(){
            require_once dirname(__FILE__) . '/DbConnect.php';
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }
         public function createUser($username, $email, $adresse, $password){
           if(!$this->isEmailExist($email)){
                $stmt = $this->con->prepare("INSERT INTO users (username, email, adresse, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $email, $adresse, $password);
                if($stmt->execute()){
                    return USER_CREATED; 
                }else{
                    return USER_FAILURE;
                } 
           }
           return USER_EXISTS; 
        }
         public function apply ($statut ,  $user_id  , $id_mission ,$entreprise_id )  {
                if ($statut ==  2 ) {
                       $stmt =  $this->con->prepare("INSERT INTO postulants (statut,user_id,id_mission,entreprise_id) VALUES (?, ?, ?,?) ")  ; 
                        $stmt->bind_param("ssss" , $statut,$user_id,$id_mission,$entreprise_id) ; 
                        if ($stmt->execute()) {
                            return  POSTULANT_CREATED ; 
                        }else{
                            return POSTULANT_FAILURE ; 
                        }
                }
                 return POSTULANT_EXISTS;
                  }
           public function addContrat (  $user_id  , $entreprise_id ,$postulant_id, $mission_id,
           $statut_contrat,$solde)  {
                       $stmt =  $this->con->prepare("INSERT INTO contrat(user_id,entreprise_id,postulant_id,mission_id,statut_contrat,solde) VALUES (?, ?, ?,?,?,?) ")  ; 
                        $stmt->bind_param("ssssss" ,$user_id,$entreprise_id,$postulant_id,$mission_id,$statut_contrat,$solde) ; 
                        if ($stmt->execute()) {
                            return  POSTULANT_CREATED ;   
                        }else{
                            return POSTULANT_FAILURE ; 
                        }
                  }     
       public function getUserByIdPostulants ($user_id) {
            $stmt =  $this->con->prepare("SELECT  id , statut , user_id, id_mission FROM postulants WHERE  user_id =  ? ") ;
            $stmt->bind_param("i", $user_id) ;
            $stmt->execute()  ; 
            $stmt->bind_result($id , $statut,$user_id,$id_mission) ; 
            $stmt->fetch() ; 
            $user = array();
            $user['id'] = $id  ; 
            $user['statut'] = $statut; 
            $user['user_id'] = $user_id ; 
            $user['id_mission'] = $id_mission  ; 
            return $user; 
        }

        public function getApplyedMissionById ($user_id){
            $stmt =  $this->con->prepare("SELECT postulants.id,postulants.statut,users.username,users.email,posts.location,posts.title,posts.remuneration,posts.debut,posts.fin,entreprise.name_entreprise,entreprise.logo
                FROM postulants
                LEFT JOIN users ON postulants.user_id =  users.id 
                LEFT JOIN posts ON postulants.id_mission = posts.id
                LEFT JOIN entreprise ON postulants.entreprise_id = entreprise.id_entreprise
                WHERE user_id = ? AND statut <= 3
                ORDER BY id DESC ");
            
            $stmt->bind_param( "i" , $user_id)  ;
            $stmt->execute()  ;
            $stmt->bind_result($id,$statut,$username,$email,$location,$title,$remuneration,$debut,$fin,$name_entreprise,$logo) ; 
            $users = array();
            while ( $stmt->fetch() ) {
            $user['id']=  $id;
            $user['statut']=  $statut;
            $user['username']=  $username;
            $user['email']=  $email;
            $user['location']=  $location;
            $user['title']=  $title;
            $user['remuneration']=  $remuneration;
            $user['debut']=  $debut;
            $user['fin']=  $fin;
            $user['name_entreprise']=  $name_entreprise;
            $user['logo']=  $logo;
             array_push($users, $user) ; 
            }
            return $users  ; 
        }
           public function getAcceptedMissionById ($user_id){
           $stmt =  $this->con->prepare("SELECT postulants.id,postulants.statut,postulants.user_id,postulants.id_mission,users.username,users.email,posts.location,posts.title,posts.remuneration,posts.debut,posts.fin,posts.heure_debut,posts.heure_fin,posts.duree,postulants.entreprise_id,entreprise.name_entreprise,entreprise.logo,entreprise.manager,entreprise.telephone
                FROM postulants
                LEFT JOIN users ON postulants.user_id =  users.id 
                LEFT JOIN posts ON postulants.id_mission = posts.id
                LEFT JOIN entreprise ON postulants.entreprise_id = entreprise.id_entreprise
                WHERE user_id = ? AND statut = 4
                ORDER BY id DESC ");
            
            $stmt->bind_param( "i" , $user_id)  ;
            $stmt->execute()  ;
            $stmt->bind_result($id,$statut,$user_id,$id_mission,$username,$email,$location,$title,$remuneration,$debut,$fin,$heure_debut,$heure_fin,$duree,$id_entreprise,$name_entreprise,$logo,$manager,$telephone) ; 
            $users = array();
            while ( $stmt->fetch() ) {
            $user['id']=  $id;
            $user['statut']=  $statut;
            $user['user_id']=  $user_id;
            $user['id_mission']=  $id_mission;
            $user['username']=  $username;
            $user['email']=  $email;
            $user['location']=  $location;
            $user['title']=  $title;
            $user['remuneration']=  $remuneration;
            $user['debut']=  $debut;
            $user['fin']=  $fin;
            $user['heure_debut']=  $heure_debut;
            $user['heure_fin']=  $heure_fin;
            $user['duree']=  $duree;
            $user['id_entreprise']=  $id_entreprise;
            $user['name_entreprise']=  $name_entreprise;
            $user['logo']=  $logo;
            $user['manager']=  $manager;
            $user['telephone']=  $telephone;
            
             array_push($users, $user) ; 
            }
            return $users; 
        }
                   
        public function getNumberOfMissionApplied ($user_id){
            $stmt =  $this->con->prepare("SELECT postulants.id,postulants.statut,postulants.user_id
                FROM postulants
                LEFT JOIN users ON postulants.user_id =  users.id 
                WHERE user_id = ? ");
            
            $stmt->bind_param( "i" , $user_id)  ;
            $stmt->execute()  ;
            $stmt->bind_result($id,$statut,$user_id) ; 
            $users = array();
            while ( $stmt->fetch() ) {
            $user['id']=  $id;
            $user['statut']=  $statut;
            $user['user_id']=  $user_id;
             array_push($users, $user) ; 
            }
            return $users  ; 
        }
     public function getMissionApplyedByUser () 
        {
            $stmt =  $this->con->prepare("SELECT posts.id,posts.location,posts.title,posts.description,posts.role,posts.dress_code,posts.duree,posts.debut,posts.fin,posts.heure_debut,posts.heure_fin, posts.remuneration,posts.numero,users.username ,users.email FROM posts 
                LEFT JOIN users ON users.id_mission =  posts.id

                ") ;
          $stmt->execute()  ; 
            $stmt->bind_result($id,$location,$title,$description,$role,$dress_code,$duree,$debut,$fin,$heure_debut,$heure_fin,$remuneration,$numero,$username,$email,$statut,$id_mission) ; 
       
        $posts = array();
            while ($stmt->fetch()){
            $post['id'] = $id  ; 
            $post['location'] = $location ; 
            $post['title'] = $title ; 
            $post['description'] = $description ; 
            $post['role'] = $role; 
            $post['dress_code'] = $dress_code; 
            $post['duree'] = $duree; 
            $post['debut'] = $debut; 
            $post['fin'] = $fin; 
            $post['heure_debut'] = $heure_debut; 
            $post['heure_fin'] = $heure_fin; 
            $post['remuneration'] = $remuneration; 
            $post['numero'] = $numero; 
            $post['username'] = $username ; 
            $post['email'] = $email ; 
            $post['statut'] = $statut ; 
            $post['id_mission'] = $id_mission ; 
            array_push($posts, $post); 
            }
            return $posts ; 
}

    public function getUserMissionApllyedByEmail ($email) {
            $stmt =  $this->con->prepare("SELECT posts.id,posts.location,posts.title,posts.description,posts.role,posts.dress_code,posts.duree,posts.debut,posts.fin,posts.heure_debut,posts.heure_fin, posts.remuneration,posts.numero,users.username ,users.email ,users.statut,users.id_mission FROM posts 
                LEFT JOIN users ON users.id_mission =  posts.id WHERE email = ?  ") ;
            $stmt->bind_param("s", $email) ;
            $stmt->execute()  ; 
             $stmt->bind_result($id,$location,$title,$description,$role,$dress_code,$duree,$debut,$fin,$heure_debut,$heure_fin,$remuneration,$numero,$username,$email,$statut,$id_mission) ; 
            $stmt->fetch() ; 
            $post = array();
            $post['id'] = $id  ; 
            $post['location'] = $location ; 
            $post['title'] = $title ; 
            $post['description'] = $description ; 
            $post['role'] = $role; 
            $post['dress_code'] = $dress_code; 
            $post['duree'] = $duree; 
            $post['debut'] = $debut; 
            $post['fin'] = $fin; 
            $post['heure_debut'] = $heure_debut; 
            $post['heure_fin'] = $heure_fin; 
            $post['remuneration'] = $remuneration; 
            $post['numero'] = $numero; 
            $post['username'] = $username ; 
            $post['email'] = $email ; 
            $post['statut'] = $statut ; 
            $post['id_mission'] = $id_mission ; 
            return $post; 
        }      
        public function userlogin ($email , $password) {
        	if ($this->isEmailExist($email)) {
        		$hashed_password = $this->getUserPasswordByEmail($email); 
        		if (password_verify($password , $hashed_password)) {
        			return USER_AUTHENTICATED  ;  
        		}else{
        			return USER_PASSWORD_DO_NOT_MATCH ;  
        		}
        	}else{
        		return USER_NOT_FOUND ;  
        	}
        }
         public function getUserPasswordByEmail ($email) {
        	$stmt =  $this->con->prepare("SELECT password   FROM users WHERE  email =  ? ") ;
        	$stmt->bind_param("s", $email) ;
        	$stmt->execute()  ; 
        	$stmt->bind_result($password) ; 
        	$stmt->fetch() ; 
        	return $password ;  
        }
        public function getAllUsers () 
        {
        	$stmt =  $this->con->prepare("SELECT id,email,username,adresse FROM users ") ;
        	$stmt->execute()  ; 
        	$stmt->bind_result($id , $email,$username,$adresse) ; 
       
        	$users = array();
        	while ($stmt->fetch()) {
            $user['id'] = $id  ; 
        	$user['username'] = $username  ; 
        	$user['email'] = $email ; 
        	$user['adresse'] = $adresse ; 
        	array_push($users, $user); 
        	}
        	return $users ; 
       
        }
            
             public function getOnlyPostulants () 
        {
            $stmt =  $this->con->prepare("SELECT id,email,username,adresse ,id_mission,statut FROM postulants ") ;
            $stmt->execute()  ; 
            $stmt->bind_result($id , $email,$username,$adresse,$id_mission,$statut) ; 
       
            $postulants = array();
            while ($stmt->fetch()) {
            $postulant['id'] = $id  ; 
            $postulant['username'] = $username  ; 
            $postulant['email'] = $email ; 
            $postulant['adresse'] = $adresse ; 
            $postulant['id_mission'] = $id_mission ; 
            $postulant['statut'] = $adresse ; 
            array_push($postulants, $postulant); 
            }
            return $postulants ; 
       
        }

       
            public function getAllPosts () 
        {
            $stmt =  $this->con->prepare("SELECT posts.id,posts.location,posts.title,posts.description,posts.role,posts.dress_code,posts.duree,posts.debut,posts.fin,posts.heure_debut,posts.heure_fin, posts.remuneration,posts.numero,entreprise.id_entreprise,entreprise.name_entreprise ,entreprise.logo FROM posts 
                LEFT JOIN entreprise ON posts.entreprise_id =  entreprise.id_entreprise") ;
          $stmt->execute()  ; 
            $stmt->bind_result($id,$location,$title,$description,$role,$dress_code,$duree,$debut,$fin,$heure_debut,$heure_fin,$remuneration,$numero,$id_entreprise,$name_entreprise,$logo) ; 
       
        $posts = array();
            while ($stmt->fetch()){
            $post['id'] = $id  ; 
            $post['location'] = $location ; 
            $post['title'] = $title ; 
            $post['description'] = $description ; 
            $post['role'] = $role; 
            $post['dress_code'] = $dress_code; 
            $post['duree'] = $duree; 
            $post['debut'] = $debut; 
            $post['fin'] = $fin; 
            $post['heure_debut'] = $heure_debut; 
            $post['heure_fin'] = $heure_fin; 
            $post['remuneration'] = $remuneration; 
            $post['numero'] = $numero; 
            $post['id_entreprise'] = $id_entreprise; 
            $post['name_entreprise'] = $name_entreprise ; 
            $post['logo'] = $logo ; 
            array_push($posts, $post); 
            }
            return $posts ; 
}

        public function getUserByEmail ($email) {
        	$stmt =  $this->con->prepare("SELECT id,email,username,adresse FROM users WHERE  email =  ? ") ;
        	$stmt->bind_param("s", $email) ;
        	$stmt->execute()  ; 
        	$stmt->bind_result($id , $email,$username,$adresse) ; 
        	$stmt->fetch() ; 
        	$user = array();
        	$user['id'] = $id  ; 
        	$user['email'] = $email ; 
        	$user['username'] = $username ; 
        	$user['adresse'] = $adresse  ; 
        	return $user; 
        }



        public function updateUser ($email , $name  , $school  , $id){
        	$stmt  =  $this->con->prepare("UPDATE users  SET email = ?  ,  name = ?  , school = ? WHERE id = ? ")  ;
        	$stmt->bind_param('sssi', $email , $name , $school , $id ) ;
        	if ($stmt->execute()) {
        		return true  ;
        	}else{
        		return false  ;  
        	}
        }


        public function updatePassword ($currentpassword ,$newpassword , $email ) {
        	$hashed_password = $this->getUserPasswordByEmail($email); 
        	if (password_verify($currentpassword , $hashed_password)) {
        		$hashed_password =  password_hash($newpassword , PASSWORD_DEFAULT) ; 
        		$stmt =  $this->con->prepare("UPDATE users SET password =  ? WHERE email = ? ") ;
        		$stmt->bind_param('ss', $hashed_password , $email);

        		if ($stmt->execute()) {
        			return PASSWORD_CHANGED  ; 
        		}else{
        			return PASSWORD_NOT_CHANGED ;
        		}
        	}else{
        		return PASSWORD_DO_NOT_MATCH;
        	}	
        }

        	public function deleteuser ($id)
        	{
        	$stmt = $this->con->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
           	if ( $stmt->execute())
            	return true  ;  
            	return false ; 	
        	}


        public function isNumeroExistOnPostulants($id_mission){
            $stmt = $this->con->prepare("SELECT id FROM postulants WHERE id_mission = ?");
            $stmt->bind_param("s", $id_mission);
            $stmt->execute(); 
            $stmt->store_result(); 
            return $stmt->num_rows > 0;  
        }

 public function isEmailExistOnPostulants($email){
            $stmt = $this->con->prepare("SELECT id FROM postulants WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute(); 
            $stmt->store_result(); 
            return $stmt->num_rows > 0;  
        }


         public function isEmailExist($email){
            $stmt = $this->con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute(); 
            $stmt->store_result(); 
            return $stmt->num_rows > 0;  
        }



    } 


    

 ?>