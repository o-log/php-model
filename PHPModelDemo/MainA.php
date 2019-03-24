<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace PHPModelDemo;

use OLOG\GET;

class MainA
{
    const FIELD_OPERATION = 'a';
    const OPERATION_ADD_MODEL = 'OPERATION_ADD_MODEL';
    const OPERATION_DELETE_MODEL = 'OPERATION_DELETE_MODEL';
    const OPERATION_SELECT = 'OPERATION_SELECT';
    const OPERATION_SINGLE = 'OPERATION_SINGLE';

    public static function render()
    {
       if (GET::optional(self::FIELD_OPERATION) == self::OPERATION_ADD_MODEL) {
            $new_model = new Test5();
            $new_model->title = rand(1, 1000);
            $new_model->bool_val = rand(0, 2) ? false : true;
            $new_model->save();
        }

        if (GET::optional(self::FIELD_OPERATION) == self::OPERATION_DELETE_MODEL) {
            $model = Test5::factory(GET::required('id'));
            $model->delete();
        }

        if (GET::optional(self::FIELD_OPERATION) == self::OPERATION_SELECT) {
            $models = Test5::forRandint(GET::required('randint'));
            echo '<h1>Selected for randint</h1>';
            foreach ($models as $model) {
                echo '<div>' . $model->getId() . '</div>';
            }
        }

        if (GET::optional(self::FIELD_OPERATION) == self::OPERATION_SINGLE) {
            $model = Test5::single(Test5::forRandint(GET::required('randint')));
            echo '<h1>Single for randint</h1>';
            echo '<div>' . $model->getId() . '</div>';
        }

        echo '<h1>Models <a href="/?' . self::FIELD_OPERATION . '=' . self::OPERATION_ADD_MODEL . '">+</a></h1>';

        $models = Test5::all(10, 0);

        echo '<div><b>First: </b>' . print_r(Test5::first($models, false), true) . '</div>';

        foreach ($models as $model) {
            echo '<div>' . print_r($model, true) . '</div>';
            echo '<div><a href="/?' . self::FIELD_OPERATION . '=' . self::OPERATION_DELETE_MODEL . '&id=' . $model->getId() . '">delete</a></div>';
            echo '<div><a href="/?' . self::FIELD_OPERATION . '=' . self::OPERATION_SELECT . '&randint=' . $model->getRandint() . '">select</a></div>';
            echo '<div><a href="/?' . self::FIELD_OPERATION . '=' . self::OPERATION_SINGLE . '&randint=' . $model->getRandint() . '">single</a></div>';
        }
    }
}
