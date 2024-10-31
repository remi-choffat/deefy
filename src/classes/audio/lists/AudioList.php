<?php

declare(strict_types=1);

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\exception\InvalidPropertyNameException;

class AudioList implements \Iterator
{

    protected int $id;
    protected string $nom;
    protected int $nbPistes;
    protected int $dureeTotale;
    protected array $listePistes = [];

    public function __construct(string $nom, array $listePistes = [])
    {
        $this->nom = $nom;
        $this->listePistes = $listePistes;
        $this->nbPistes = count($listePistes);
        $this->dureeTotale = $this->calculerDureeTotale();
    }

    public function calculerDureeTotale(): int
    {
        $dureeTotale = 0;
        foreach ($this->listePistes as $piste) {
            $dureeTotale += $piste->duree;
        }
        return $dureeTotale;
    }

    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new InvalidPropertyNameException("Invalid property : $name");
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function current()
    {
        return current($this->listePistes);
    }

    public function next()
    {
        return next($this->listePistes);
    }

    public function key()
    {
        return key($this->listePistes);
    }

    public function valid(): bool
    {
        return key($this->listePistes) !== null;
    }

    public function rewind()
    {
        return reset($this->listePistes);
    }

}