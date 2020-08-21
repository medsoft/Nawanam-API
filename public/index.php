<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../includes/DbOperations.php';

 $app = new \Slim\App([
        'settings' =>[
        'displayErrorDetails' =>  true 
 ]]);


$app->post('/createuser', function(Request $request, Response $response){
     
    if(!haveEmptyParameters(array('username', 'email', 'adresse', 'password'), $request,  $response)){
    
        $request_data = $request->getParsedBody(); 
         $username = $request_data['username'];
         $email = $request_data['email'];
         $adresse = $request_data['adresse'];
         $password = $request_data['password']; 
         $hash_password = password_hash($password, PASSWORD_DEFAULT);
         $db = new DbOperations; 
         $result = $db->createUser($username, $email, $adresse, $hash_password);
        
        if($result == USER_CREATED){
             $message = array(); 
             $message['error'] = false; 
             $message['message'] = 'Bravo ';
             $response->write(json_encode($message));
             return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
         }else if($result == USER_FAILURE){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Veuillez reessayer';
            $response->write(json_encode($message));
     
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
         }else if($result == USER_EXISTS){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Cet utilisateur existe deja';
             $response->write(json_encode($message));
             return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);    
});

$app->post('/apply', function(Request $request, Response $response){
     
       if (!haveEmptyParameters (array('statut' , 'user_id', 'id_mission','entreprise_id') , $request , $response)) {
        $request_data = $request->getParsedBody(); 
         $statut = $request_data['statut'];
         $user_id = $request_data['user_id'];
         $id_mission = $request_data['id_mission']; 
         $entreprise_id = $request_data['entreprise_id']; 

    
         $db = new DbOperations; 
         $result = $db->apply($statut, $user_id ,  $id_mission , $entreprise_id );
        
        if($result == POSTULANT_CREATED){
             $message = array(); 
             $message['error'] = false; 
             $message['message'] = 'Candidature acceptee ';
             $response->write(json_encode($message));
             return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
         }else if($result == POSTULANT_FAILURE){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Erreur veuillez reessayer ';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);  
         }else if($result == POSTULANT_EXISTS){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Vous avez deja postule';
             $response->write(json_encode($message));
             return $response
                        ->withHeader('Content-type', 'application/json')
                      
                        ->withStatus(422);    
        }

    }
    
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);    
});



$app->post('/sendcontrat', function(Request $request, Response $response){
     
       if (!haveEmptyParameters (array( 'user_id', 'entreprise_id','postulant_id','mission_id','statut_contrat','solde') , $request , $response)) {
        $request_data = $request->getParsedBody(); 
         $user_id = $request_data['user_id'];
         $entreprise_id = $request_data['entreprise_id'];
         $postulant_id = $request_data['postulant_id'];
         $mission_id = $request_data['mission_id']; 
         $statut_contrat = $request_data['statut_contrat']; 
         $solde = $request_data['solde']; 
        

    
         $db = new DbOperations; 
         $result = $db->addContrat (  $user_id  , $entreprise_id ,$postulant_id, $mission_id,
           $statut_contrat, $solde );
        
        if($result == POSTULANT_CREATED){
             $message = array(); 
             $message['error'] = false; 
             $message['message'] = 'Contrat envoyé avec succées  ';
             $response->write(json_encode($message));
             return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
         }else if($result == POSTULANT_FAILURE){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Erreur veuillez reessayer ';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);  
         }else if($result == POSTULANT_EXISTS){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Vous avez deja postule';
             $response->write(json_encode($message));
             return $response
                        ->withHeader('Content-type', 'application/json')
                      
                        ->withStatus(422);    
        }

    }
    
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);    
});


$app->post('/userlogin' , function(Request $request , Response $response){
    if (!haveEmptyParameters(array('email' , 'password'), $request ,  $response)) {
         $request_data = $request->getParsedBody(); 
         $email = $request_data['email'];
         $password = $request_data['password'];

         $db  =  new DbOperations ; 
         $result =  $db->userlogin($email , $password) ; 
         if ($result ==  USER_AUTHENTICATED) {
             
             $user = $db->getUserByEmail($email);
             $response_data =  array() ; 

             $response_data['error'] = false ; 
             $response_data['message'] = 'Login successfully'; 
             $response_data['user'] = $user ;

             $response->write(json_encode($response_data)) ;

              return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);  

         }elseif ($result == USER_NOT_FOUND){

             $response_data =  array() ; 

             $response_data['error'] = true ; 
             $response_data['message'] ='User not exist ' ; 

             $response->write(json_encode($response_data)) ;

              return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);  


         }elseif ($result == USER_PASSWORD_DO_NOT_MATCH) {
           $response_data =  array() ; 

             $response_data['error'] = true ; 
             $response_data['message'] = 'Invalid credentials '; 

             $response->write(json_encode($response_data)) ;

              return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);  
      
         }
    }
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);  
});

$app->get('/allusers' , function(Request $request , Response $response){

    $db = new DbOperations ; 

    $users  =  $db->getAllUsers() ;  
    $response_data = array() ;  
    $response_data['error'] =  false  ; 
    $response_data['users'] =  $users  ;
    $response->write(json_encode($response_data)) ; 
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);  
}) ;

$app->get('/getuser/{user_id}' , function(Request $request , Response $response ,array $args ){
$user_id = $args ['user_id']  ; 
    $db = new DbOperations ; 

    $candidat  =  $db->getUserByIdPostulants($user_id) ;  
    $response_data = array() ;  
    $response_data['error'] =  false  ; 
    $response_data['candidat'] =  $candidat  ;
    $response->write(json_encode($response_data)) ; 
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);  
}) ;

$app->get('/allreadyapplyed/{user_id}' , function(Request $request , Response $response ,array $args ){
$user_id = $args ['user_id']  ; 
    $db = new DbOperations ; 

    $dejatcandidat  =  $db->getNumberOfMissionApplied($user_id) ;  
    $response_data = array() ;  
    $response_data['error'] =  false  ; 
    $response_data['dejatcandidat'] =  $dejatcandidat  ;
    $response->write(json_encode($response_data)) ; 
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);  
}) ;



$app->get('/allmissionapplyed/{user_id}' , function(Request $request , Response $response, array $args){
   $user_id = $args ['user_id']  ; 
    $db = new DbOperations ; 

    $missions  =  $db->getApplyedMissionById($user_id) ;  
    $response_data = array() ;  
    $response_data['error'] =  false  ; 
    $response_data['missions'] =  $missions  ;
    $response->write(json_encode($response_data)) ; 
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);  
}) ;

$app->get('/allacceptedmission/{user_id}' , function(Request $request , Response $response, array $args){
   $user_id = $args ['user_id']  ; 
    $db = new DbOperations ; 

    $works  =  $db->getAcceptedMissionById($user_id) ;  
    $response_data = array() ;  
    $response_data['error'] =  false  ; 
    $response_data['works'] =  $works  ;
    $response->write(json_encode($response_data)) ; 
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);  
}) ;




$app->get('/allpostulants' , function(Request $request , Response $response){

    $db = new DbOperations ; 

    $postulants  =  $db->getUserMissionApllyedByEmail () ;  
    $response_data = array() ;  
    $response_data['error'] =  false  ; 
    $response_data['postulants'] =  $postulants  ;
    $response->write(json_encode($response_data)) ; 

     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);  
}) ;


$app->get('/allposts', function(Request $request , Response $response){

    $db = new DbOperations ; 

    $posts  =  $db->getAllPosts() ;  
    $response_data = array() ;  
    $response_data['error'] =  false  ; 
    $response_data['posts'] =  $posts  ;
    $response->write(json_encode($response_data)) ; 

     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);  
}) ;

$app->put('/updateuser/{id}' , function(Request $request , Response $response , array $args){
    $id = $args ['id']  ; 
    if (!haveEmptyParameters(array('email','name','school'), $request ,  $response)) {
         $request_data = $request->getParsedBody() ;  
         $email = $request_data['email'];
         $name = $request_data['name'];
         $school = $request_data['school']; 
         

         $db = new DbOperations  ; 
         if ($db->updateuser($email , $name,  $school , $id)) {
                $response_data = array() ; 
                $response_data['error']  =  false  ;
                $response_data['message'] = 'User updated successfully '  ; 
                $user =  $db->getUserByEmail($email) ; 
                $response_data['user']  =  $user ; 
                $response->write(json_encode($response_data)) ; 
                return $response
                 ->withHeader('Content-type', 'application/json')
                 ->withStatus(200);  
       }else{
               $response_data = array() ; 
                $response_data['error']  =  true  ;
                $response_data['message'] = 'Please try again  '  ; 
                $user =  $db->getUserByEmail($email) ; 
                $response_data['user']  =  $user ; 
                $response->write(json_encode($response_data)) ; 
                return $response
                 ->withHeader('Content-type', 'application/json')
                 ->withStatus(200);  
       }  
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);  
} ) ;


$app->put('/updatepassword' , function(Request $request , Response $response ){
     

     if (!haveEmptyParameters (array('currentpassword' , 'newpassword', 'email') , $request , $response)) {
            
            $request_data =  $request->getParsedBody() ;  
            $currentpassword =  $request_data['currentpassword'] ; 
            $newpassword =  $request_data['newpassword'] ; 
            $email =  $request_data['email'] ;

            $db = new DbOperations ;
            $result  =  $db->updatePassword($currentpassword , $newpassword , $email) ;
            if ($result == PASSWORD_CHANGED) {
                  $response_data = array() ; 
                  $response_data['error'] = false ; 
                  $response_data['message'] = 'Password changed ' ;

                 $response->write(json_encode($response_data)) ; 
                 return $response
                 ->withHeader('Content-type', 'application/json')
                 ->withStatus(200);  

              } elseif ($result == PASSWORD_DO_NOT_MATCH) {

                $response_data = array() ; 
                  $response_data['error'] = true ; 
                  $response_data['message'] = 'Password do not match  ' ;

                 $response->write(json_encode($response_data)) ; 
                 return $response
                 ->withHeader('Content-type', 'application/json')
                 ->withStatus(200);  
                
              } elseif ($result == PASSWORD_NOT_CHANGED) {
                $response_data = array() ; 
                  $response_data['error'] = true ; 
                  $response_data['message'] = 'Password not changed  ' ;

                 $response->write(json_encode($response_data)) ; 
                 return $response
                 ->withHeader('Content-type', 'application/json')
                 ->withStatus(200);  
              }
     }
       return $response
                 ->withHeader('Content-type', 'application/json')
                 ->withStatus(422);  

});
    
$app->delete('/deleteuser/{id}', function(Request $request , Response $response , array $args ){
        $id = $args ['id']  ; 
        $db =  new DbOperations  ; 
        if ($db->deleteuser($id)) {
             $response_data = array() ; 
                  $response_data['error'] = false ; 
                  $response_data['message'] = 'user has been deleted' ;
        }else{
                 $response_data = array() ; 
                  $response_data['error'] = true ; 
                  $response_data['message'] = 'user has not been deleted  ' ;
        }
        $response->write(json_encode($response_data));
        return $response
                 ->withHeader('Content-type', 'application/json')
                 ->withStatus(200);  
}) ; 
function haveEmptyParameters($required_params, $request ,  $response){
    $error = false; 
    $error_params = '';
    $request_params = $request->getParsedBody(); 
     foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true; 
            $error_params .= $param . ', ';
        }
    }
     if($error){
        $error_detail = array();
        $error_detail['error'] = true; 
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error; 
}

$app->run();
