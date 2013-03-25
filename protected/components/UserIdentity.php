<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */

 
 
class UserIdentity extends CUserIdentity
{
                            private $id;
                          

                             public function authenticate()
                                {
                                        $user=User::model()->find('LOWER(user_name)=?',array(strtolower($this->username)));
                                        if($user===null)
                                                $this->errorCode=self::ERROR_USERNAME_INVALID;
                                        //else if(!$user->validatePassword($this->password))
                                            else if($user->user_password!==$this->password)
                                                $this->errorCode=self::ERROR_PASSWORD_INVALID;
                                        else
                                        {
                                                $this->id=$user->user_id;
                                                //$this->username=$user->user_name;
                                                $this->setState('username',$user->user_name);
                                                $this->setState('role_id',$user->role_id);
                                                $this->setState('status_id',$user->status_id);
                                                $this->setState('city_id',$user->city_id);
                                                $this->setState('site_id',$user->site_id);
                                                $this->setState('is_active',$user->is_active);
                                            
                                                 $this->errorCode=self::ERROR_NONE;
                                        }
                                        return $this->errorCode==self::ERROR_NONE;
                                }

                            public function getId()
                            {
                                return $this->id;
                            }
                           
}