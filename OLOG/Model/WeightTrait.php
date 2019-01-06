<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model;

use OLOG\DB\DB;

/**
Использование WeightTrait

Создать у модели поле weight int not null default 0

В модели:

    implements InterfaceWeight
    use WeightTrait

В beforeSave() модели вызвать initWeight() с контекстом.
Вот пример инициализации веса для стадии турнира (внутри каждого турнира у стадий свои веса):

    public function beforeSave(){
        $this->initWeight(['tournament_id' => $this->getTournamentId()]);
    }

После этого можно вывести в списке моделей widgetWeight, также передавая ему контекст. При этом таблицу нужно отсортировать по полю weight по возрастанию.

Внимание! Если weightTrait добавляется к существующй модели, то для существующих объектов веса проинициализированы не будут! У них всех веса будут равны 0.
В этом случае нужно инициализировать их уникальными значениями, например:

    update stage set weight = id;

*/
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

                if (is_null($extra_field_value)){
                    $where_arr[] = $extra_field_name . ' is null';
                } else {
                    $where_arr[] = $extra_field_name . '=?';
                    $params_arr[] = $extra_field_value;
                }
            }
        }

        $sql = 'select max(weight) from ' . self::DB_TABLE_NAME;
        if (count($where_arr)){
            $sql .= ' where ' . implode(' and ', $where_arr);
        }

        $weight = DB::readField(self::DB_ID,
            $sql,
            $params_arr
        );

        return intval($weight);
    }

    public function initWeight($context_fields_arr){
        if (is_null($this->getId())){ // TODO: check interface
            $this->weight = self::getMaxWeightForContext($context_fields_arr) + 1;
        }
    }

    /**
     * находит в указанном контексте (т.е. для набора пар поле - значение) объект с максимальным весом, меньшим чем у текущего, и меняет текущий объект с ним весами
     * т.е. объект поднимается на одну позицию вверх если сортировать по возрастанию веса
     * @param array $extra_fields_arr
     */
    public function swapWeights($extra_fields_arr = array())
    {
       $current_class_name = get_class($this);

        $current_item_weight = $this->weight;

        $where_arr = array('weight < ?');
        $params_arr = array($current_item_weight);

        if (!empty($extra_fields_arr)) {
            foreach($extra_fields_arr as $extra_field_name => $extra_field_value) {
                $extra_field_name = preg_replace('|[^a-zA-Z0-9_]|', '', $extra_field_name);

                if (is_null($extra_field_value)){
                    $where_arr[] = $extra_field_name . ' is null';
                } else {
                    $where_arr[] = $extra_field_name . '=?';
                    $params_arr[] = $extra_field_value;
                }
            }
        }

        $sql = 'SELECT id FROM ' . self::DB_TABLE_NAME . ' WHERE ' . implode(' AND ', $where_arr) . ' ORDER BY weight DESC, id DESC LIMIT 1';
        $object_to_swap_weights_id = DB::readField(self::DB_ID,
            $sql,
            $params_arr
        );

        if (!$object_to_swap_weights_id) {
            return;
        }

        $object_to_swap_weights_obj = $current_class_name::factory($object_to_swap_weights_id);

        $object_to_swap_weights_weight = $object_to_swap_weights_obj->weight;

        $this->weight = $object_to_swap_weights_weight;
        $this->save();

        $object_to_swap_weights_obj->weight = $current_item_weight;
        $object_to_swap_weights_obj->save();
    }
}
