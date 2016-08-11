<?php


namespace OLOG\Model;


trait WeightTrait
{
    /**
     * возвращает максимальный вес в указанном контексте (т.е. для набора пар поле - значение)
     * @param array $extra_fields_arr
     * @return int
     */
    static public function getMaxWeightForContext($extra_fields_arr = array())
    {
        $where_arr = [];
        $params_arr = [];

        if (!empty($extra_fields_arr)) {
            foreach ($extra_fields_arr as $extra_field_name => $extra_field_value) {
                $extra_field_name = preg_replace('|[^a-zA-Z0-9_]|', '', $extra_field_name);
                $where_arr[] = $extra_field_name . '=?';
                $params_arr[] = $extra_field_value;
            }
        }

        $weight = \OLOG\DB\DBWrapper::readField(self::DB_ID,
            'SELECT MAX(weight) FROM ' . self::DB_TABLE_NAME . ' WHERE ' . implode(' AND ', $where_arr),
            $params_arr
        );

        return intval($weight);
    }

    /**
     * находит в указанном контексте (т.е. для набора пар поле - значение) объект с максимальным весом, меньшим чем у текущего, и меняет текущий объект с ним весами
     * т.е. объект поднимается на одну позицию вверх если сортировать по возрастанию веса
     * @param array $extra_fields_arr
     */
    public function swapWeights($extra_fields_arr = array())
    {
       $current_class_name = self::getMyClassName();

        $current_item_weight = $this->getWeight();

        $where_arr = array('weight < ?');
        $params_arr = array($current_item_weight);

        if (!empty($extra_fields_arr)) {
            foreach($extra_fields_arr as $extra_field_name => $extra_field_value) {
                $extra_field_name = preg_replace('|[^a-zA-Z0-9_]|', '', $extra_field_name);
                $where_arr[] = $extra_field_name . '=?';
                $params_arr[] = $extra_field_value;
            }
        }

        $sql = 'SELECT id FROM ' . self::DB_TABLE_NAME . ' WHERE ' . implode(' AND ', $where_arr) . ' ORDER BY weight DESC, id DESC LIMIT 1';
        $object_to_swap_weights_id = \OLOG\DB\DBWrapper::readField(self::DB_ID,
            $sql,
            $params_arr
        );

        if (!$object_to_swap_weights_id) {
            return;
        }

        $object_to_swap_weights_obj = $current_class_name::factory($object_to_swap_weights_id);

        $object_to_swap_weights_weight = $object_to_swap_weights_obj->getWeight();

        $this->setWeight($object_to_swap_weights_weight);
        $this->save();

        $object_to_swap_weights_obj->setWeight($current_item_weight);
        $object_to_swap_weights_obj->save();
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }
}