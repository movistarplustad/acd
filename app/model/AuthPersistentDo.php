<?php
namespace Acd\Model;

class AuthPersistentDoException extends \exception
{
}
class AuthPersistentDo
{
    protected $id; /* Tocke */
    protected $login; /* Nickname */
    protected $rol; /* Editor, developer... */
    protected $creationDate;
    protected $lastUseDate;
    public function __construct()
    {
        $this->id = null;
        $this->login = null;
    }
    /* Setters and getters attributes */
    public function setId($id)
    {
        $this->id = (string)$id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setLogin($login)
    {
        $this->login = (string)$login;
    }
    public function getLogin()
    {
        return $this->login;
    }
    public function setRol($rol)
    {
        $this->rol = (string)$rol;
    }
    public function getRol()
    {
        return $this->rol;
    }
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }
    public function getCreationDate()
    {
        return $this->creationDate;
    }
    public function setLastUseDate($lastUseDate)
    {
        $this->lastUseDate = $lastUseDate;
    }
    public function getLastUseDate()
    {
        return $this->lastUseDate;
    }


    public function load($data)
    {
        if (isset($data['id']) && isset($data['login'])) {
            $this->setId($data['id']);
            $this->setLogin($data['login']);
            $this->setRol($data['rol']);
            $this->setCreationDate($data['timestamp']);
            $this->setLastUseDate(isset($data['lastUse']) ? $data['lastUse'] : null);
        } else {
            throw new AuthPersistentDoException('No data loaded, token o user-id probably does not exist. '.var_export($data, TRUE));
        }
    }

    public function tokenizeData()
    {
        $AuthPersistentData = array(
            '_id' => $this->getId(),
            'login' => $this->getLogin(),
            'rol' => $this->getRol(),
            'timestamp' => $this->getCreationDate(),
            'lastUse' => $this->getLastUseDate()
        );
        return $AuthPersistentData;
    }
}
