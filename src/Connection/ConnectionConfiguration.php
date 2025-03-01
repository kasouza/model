<?php

namespace Kaso\Model\Connection;

class ConnectionConfiguration
{
    public function __construct(
        protected string $host,
        protected string $port,
        protected string $dbName,
        protected string $username,
        protected string $password
    ) {}

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getDbName()
    {
        return $this->dbName;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function setPort(string $port): self
    {
        $this->port = $port;
        return $this;
    }

    public function setDbName(string $dbName): self
    {
        $this->dbName = $dbName;
        return $this;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }
}
