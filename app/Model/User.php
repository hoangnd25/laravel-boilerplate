<?php

namespace App\Model;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements Authenticatable, CanResetPassword
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=60)
     */
    protected $password;

    protected $plainPassword;

    /**
     * @ORM\Column(name="remember_token", type="string", length=100, nullable=true)
     */
    protected $rememberToken;

    /**
     * @ORM\Column(name="api_token", type="string", length=100, nullable=true)
     */
    protected $apiToken;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * @inheritdoc
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    /**
     * @inheritdoc
     */
    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    /**
     * @inheritdoc
     */
    public function setRememberToken($value)
    {
        $this->rememberToken = $value;
    }

    /**
     * @inheritdoc
     */
    public function getRememberTokenName()
    {
        return 'rememberToken';
    }

    public function getEmailForPasswordReset()
    {
        return $this->getEmail();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param mixed $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

}

