<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model;

interface WeightInterface {
    public function swapWeights($extra_fields_arr = array());
}
