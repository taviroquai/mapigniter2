<?php

namespace App;

use Config;
use App\User;
use Exception;

/**
 * Description of Ldap
 *
 * @author mafonso
 */
class Ldap
{
    /**
     * Holds the ldap link
     * 
     * @var resource
     */
    protected $link;
    
    /**
     * Try user authentication
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login($username, $password)
    {
        $this->link = \ldap_connect(Config::get('ldap.host'));
        $username = Config::get('ldap.domain') . '\\' . $username;
        return @\ldap_bind($this->link, $username, $password);
    }

    /**
     * Get or create the authentication user
     * 
     * @param string $username
     * @param resource $link
     * @return User
     * @throws Exception
     */
    public function getUser($username)
    {
        $result = $this->searchSamAccount($username);
        
        if (empty($result['count'])) {
            throw new \Exception('LDAP user not found');
        }
        
        if (empty($result[0]['mail']['count'])) {
            throw new \Exception('LDAP user email not found');
        }
        
        if (empty($result[0]['givenname']['count'])) {
            throw new \Exception('LDAP user name not found');
        }
        
        $user = User::where(['email' => $result[0]['mail'][0]])->first();
        
        // Create user if does not exist in local storage
        if (!$user) {
            $data = [
                'email' => $result[0]['mail'][0],
                'name' => $result[0]['givenname'][0]
            ];
            $user = new User($data);
            $user->save();
        }
        return $user;
    }

    /**
     * Search ldap samAccount
     * 
     * @param type samAccount
     * @param array $attributes
     * @return array
     */
    public function searchSamAccount($samAccount, $attributes = array("givenname", "mail"))
    {
        $filter = "sAMAccountName=$samAccount";
        $results = ldap_search($this->link, Config::get('ldap.ou'), $filter, $attributes);
        return ldap_get_entries($this->link, $results);
    }
}
