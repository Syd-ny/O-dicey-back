<?php

namespace App\DataFixtures\Provider;

class OdiceyProvider
{

    // array of dwarf names
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

    // array of human names
    private $humans = [
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
    // array of orc names
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
    // array of elf names
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

    /**
     * Get a random dwarf
     */
    public function dwarves()
    {
        return $this->dwarves[array_rand($this->dwarves)];
    }

    /**
     * Get a random human
     */
    public function humans()
    {
        return $this->humans[array_rand($this->humans)];
    }

    /**
     * Get a random orc
     */
    public function orcs()
    {
        return $this->orcs[array_rand($this->orcs)];
    }

    /**
     * Get a random elf
     */
    public function elves()
    {
        return $this->elves[array_rand($this->elves)];
    }

}
