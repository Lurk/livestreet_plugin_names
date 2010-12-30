<?php
function PluginNames_CheckLogin($sValue, $sParam, $iMin = 1, $iMax = 100)
{
    if (is_array($sValue)) {
        return false;
    }
    switch ($sParam)
    {
        case 'login':
            if (preg_match("/" . Config::Get('plugin.names.regular') . "{" . $iMin . ',' . $iMax . "}$/iu", $sValue)) {
                return true;
            }
            break;
        default:
            return false;
    }
    return false;
}

?>