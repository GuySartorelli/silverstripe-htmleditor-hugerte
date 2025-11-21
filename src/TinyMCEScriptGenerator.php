<?php

namespace GuySartorelli\HugeRTE;

/**
 * Declares a service which can generate a script URL for a given HTMLEditor config
 */
interface HugeRTEScriptGenerator
{
    /**
     * Generate a script URL for the given config
     *
     * @param HugeRTEConfig $config
     * @return string
     */
    public function getScriptURL(HugeRTEConfig $config);
}
