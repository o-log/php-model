<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model;

interface CacheTTLInterface {
    public function cacheTtlSeconds(): int;
}
