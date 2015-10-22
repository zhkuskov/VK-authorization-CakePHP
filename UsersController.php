<?php
/**
 * Users Controller
 *
 * @property User $User
 */
App::uses('AppController', 'Controller');

class UsersController extends AppController {
    
    public function beforeFilter() {
        
        $this->Auth->allow('auth_vk');
        
    }
    /**
     * Redirected at vk.com authorization, which redirected at original page
     *
     * Configuration set in config/bootstrap.php
     */    
	public function auth_vk() {

        if ($this->Auth->loggedIn()) {
            
            $this->redirect($this->request->referer('/', true));
            
        }
        if(!$this->Session->check('User.authReferLink')) {
            
            $this->Session->write(
                'User.authReferLink', 
                $this->request->referer('/', true)
            );
            
        }
        if (!isset($this->request->query['code'])) {
            
            $params = array(
                'client_id'     => Configure::read('vkAuth.app_id'),
                'redirect_uri'  => 'http://'.$_SERVER['HTTP_HOST'].'/users/auth_vk',
                'response_type' => 'code',
                'scope'         => 'email'
            );        
            $urlGetCode = Configure::read('vkAuth.urlGetCode') 
                . '?' . urldecode(http_build_query($params));
            $this->redirect($urlGetCode);
            
        }       
        $params = array(
            'client_id' => Configure::read('vkAuth.app_id'),
            'client_secret' => Configure::read('vkAuth.app_secret'),
            'code' => $this->request->query['code'],
            'redirect_uri' => 'http://'.$_SERVER['HTTP_HOST'].'/users/auth_vk'
        );
        $tokenLink = Configure::read('vkAuth.urlGetToken') 
            . '?' . urldecode(http_build_query($params));
        $token = file_get_contents($tokenLink);
        $token = json_decode($token, true);
 
        if (isset($token['access_token'])) {
            
            $params = array(
                'uids'         => $token['user_id'],
                'fields'       => 'uid,first_name,last_name,sex,bdate',
                'access_token' => $token['access_token']
            );
            $userInfoLink = Configure::read('vkAuth.urlGetUserInfo') 
                . '?' . urldecode(http_build_query($params));
            $userInfo = file_get_contents($userInfoLink);
            $userInfo = json_decode($userInfo, true);
            if (isset($userInfo['response'][0]['uid'])) {
                
                $userInfo = $userInfo['response'][0];                
                if(isset($token['email'])) {
                    
                    $userInfo['email'] = $token['email'];
                
                }
                
                if($this->Session->check('User.authReferLink')) {                    
                    $referLink = $this->Session->read('User.authReferLink');  
                    $this->Session->delete('User.authReferLink');                    
                } else {
                    $referLink = '/';
                }
                
                $this->addUser($userInfo, 'vk', $referLink);               
            }
        }
	}
    /**
     * Add new users and authorize their by id in social network 
     *
     * array['uid'] int User id in social network
     * array['first_name'] string User first name
     * array['last_name'] string User last name
     * array['email'] string User email
     *
     * @param array $userInfo Array user data (see above)
     * @param string $socialNetwork Designation of social network
     * @param string $referLink Link to redirect afrer authorize
     * @param string $role 
     *
     * @access private
     *
     * @todo adding gender, birthday, updating  existing users fieldsby by new values  
     */     
    private function addUser($userInfo, $socialNetwork, $referLink, $role = 'user') {
        
        $socialNetworkField = $socialNetwork . '_key';
        $savedUser = $this->User->find(
            'first', 
            array(
                'conditions' => array(
                    'User.' . $socialNetworkField => $userInfo['uid']
                )
            )
        );                
        if(!empty($savedUser)) {
            
            if($this->Auth->login(array('id' => $savedUser['User']['id']))) {
                
                $this->redirect($referLink);
                
            }
        }        
        $userAr = Array(
            'User' => Array(
                $socialNetworkField => $userInfo['uid'],
                'first_name'        => $userInfo['first_name'],
                'last_name'         => $userInfo['last_name'],
                'email'             => $userInfo['email'],
                'role'              => $role               
            )
        );        
        $this->User->create();
        if($this->User->save($userAr)) {
            
            if($this->Auth->login(array($socialNetworkField => $userAr['User'][$socialNetworkField]))) {
                
                $this->redirect($referLink);
                
            }
        }
    }
}
