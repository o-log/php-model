<?php


namespace OLOG\Model;

interface WeightInterface {
    public function swapWeights($extra_fields_arr = array());
    public function getWeight();
    public function setWeight($weight);
}