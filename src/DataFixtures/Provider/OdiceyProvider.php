<?php

namespace App\DataFixtures\Provider;

class OdiceyProvider
{
    // Array of games
    private $games = [
        
    ];

    // array of dwarves names
    private $dwarves = [
        "Ilgni(nain)",
        "Gimkhur(nain)",
        "Ilfann(nain)",
        "Vihjo(nain)",
        "Innbann(nain)",
        "Nisenn(nain)",
        "Fannag(nain)",
        "Tanolkum(nain)",
        "Gorrenn(nain)",
        "Nosbof(nain)",
        "Diondfal(nain)",
        "Tulbann(nain)",
    ];

    // array of humains names
    private $humains = [
        "Roneter(humain)",
        "Landwin(humain)",
        "Varuin(humain)",
        "Kevdar(humain)",
        "Keshjac(humain)",
        "Riankhad(humain)",
        "Ullnidas(humain)",
        "Thoster(humain)",
        "Liaty(humain)",
        "Jengreno(humain)",
        "Noredrago(humain)",
        "Jasmar(humain)",
    ];
    // array of orcs names
    private $orcs = [
        "Orrod(orc)",
        "Onkzak(orc)",
        "Gadzub(orc)",
        "Ghamgrom(orc)",
        "Shabo(orc)",
        "Kurdmalg(orc)",
        "Tanyag(orc)",
        "Morfim(orc)",
        "Gratdan(orc)",
        "Yagfim(orc)",
        "Uzozor(orc)",
        "Snagbo(orc)",
    ];
    // array of elves names
    private $elves = [
        "Calatrid(elfe)",
        "Denedio(elfe)",
        "Dallas(elfe)",
        "Diocir(elfe)",
        "Dilsae(elfe)",
        "Cilvalan(elfe)",
        "Aradur(elfe)",
        "Gonner(elfe)",
        "Rinros(elfe)",
        "Teleae(elfe)",
        "Néros(elfe)",
        "Rondgun(elfe)",
    ];

    // array of galleries
    private $galleries = [
        
    ];

    // array of characters
    private $characters = [
        
    ];

    /**
     * Get a random game
     */
    public function game()
    {
        return $this->games[array_rand($this->games)];
    }

    /**
     * Get a random dwarf
     */
    public function dwarves()
    {
        return $this->dwarves[array_rand($this->dwarves)];
    }

    /**
     * Get a random humains
     */
    public function Humains()
    {
        return $this->humains[array_rand($this->humains)];
    }

    /**
     * Get a random orcs
     */
    public function orcs()
    {
        return $this->orcs[array_rand($this->orcs)];
    }

    /**
     * Get a random elves
     */
    public function elves()
    {
        return $this->elves[array_rand($this->elves)];
    }

     /**
     * Get a random gallery
     */
    public function gallery()
    {
        return $this->galleries[array_rand($this->galleries)];
    }

    /**
     * Get a random character
     */
    public function character()
    {
        return $this->characters[array_rand($this->characters)];
    }
}