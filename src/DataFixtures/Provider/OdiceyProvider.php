<?php

namespace App\DataFixtures\Provider;

class OdiceyProvider
{
    // Array of games
    private $games = [
        
    ];

    // array of dwarves names
    private $dwarves = [
        "Ilgni",
        "Gimkhur",
        "Ilfann",
        "Vihjo",
        "Innbann",
        "Nisenn",
        "Fannag",
        "Tanolkum",
        "Gorrenn",
        "Nosbof",
        "Diondfal",
        "Tulbann",
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
     * Get a random user
     */
    public function dwarves()
    {
        return $this->dwarves[array_rand($this->dwarves)];
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