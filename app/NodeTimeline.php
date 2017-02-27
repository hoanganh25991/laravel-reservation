<?php

namespace App;

use Illuminate\Support\Collection;

class NodeTimeline
{
    /* @var Collection $node_array */
    protected $node_array;

    public function __construct(){
        $this->node_array = collect([]);
    }

    public function push(Timing $timing, $type){
        //check sth before push
        //check first
        $node = $this->node_array->filter(function($node)use($timing){
            return $node->time == $timing->first_arrival_time;
        });

        if($node->count() == 0){
            //completely new
            $new_node = $this->createNode(['time' => $timing->first_arrival_time, 'first_info' => $timing, 'type' => $type]);
            $this->node_array->push($new_node);
        }

        //a node X has exist
        if($node->count() == 1){
            $nodeX = $node->first();
            //normal case
            $nodeX->first_info = $timing;
            $nodeX->type = $type;
        }
        
        
        //check last
        $node2 = $this->node_array->filter(function($node)use($timing){
            return $node->time == $timing->last_arrival_time;
        });

        if($node2->count() == 0){
            //completely new
            $new_node = $this->createNode(['time' => $timing->last_arrival_time, 'last_info' => $timing, 'type' => $type]);
            $this->node_array->push($new_node);
        }

        //a node X has exist
        if($node2->count() == 1){
            $nodeX = $node2->first();
            //normal case
            $nodeX->last_info = $timing;
            $nodeX->type = $type;
        }
    }


    public function createNode($info = []){
        $a = array_merge([
            'time' => null,
            'type' => 0,
            'first_info' => null,
            'last_info' => null,
        ], $info);

        return (object) $a;
    }
    
    public function getNodeArray(){
        return $this->node_array->sortBy(function ($node){
            $time = explode(":", $node->time)[0];
            return (int) $time;
        })->values();
    }
}